<?php

abstract class Multiprocess extends \System\Service_Controller_Shell
{
    const PROC_STATUS_UNKNOWN = -1;
    const PROC_STATUS_TRUE = 2;
    const PROC_STATUS_FALSE = 3;
    const PROC_STATUS_EXCEPTION = 4;

    /**
     * Порядковый номер подпроцесса
     * В подпроцессе имеет неотрицательное значение, используется при выводе отладочной информации
     * Отсчет с нуля
     * @var int
     */
    private $_subprocess_num = -1;

    /**
     * PID подроцесса
     * В подроцессе имеет неотрицательное значение
     * @var int
     */
    private $_subprocess_pid = -1;

    /**
     * Сокет подпроцесса
     * Используется для записи результата в родительский процесс.
     * @var resource
     */
    private $_subprocess_pipe;

    /**
     * Идентификаторы процессов по номерам процессов.
     * Используется для ожидания процессов.
     * @var int[]
     */
    private $_processes_pids = array ();

    /**
     * Pipes для процессов.
     * Используются для обмена данными с подпроцессами.
     * @var resource[]
     */
    private $_processes_pipes = array ();

    /**
     * Количество процессов.
     * @var int
     */
    private $_processes_count = 2;

    /**
     * Устанавливает количество процессов (не меньше 2-х)
     * @param int $count
     *
     * @throws \Lib_Exception_Runtime_Backtraced
     */
    final protected function _setProcessesCount( $count )
    {
        $this->_processes_count = (int) $count;
        if ( $this->_processes_count <= 1 )
        {
            throw new \Lib_Exception_Runtime_Backtraced( 'Processes count must be greater than 1' );
        }
    }

    /**
     * Максимальное количество паралелльно выполняющихся подпроцессов
     * @return int
     */
    final protected function _getProccessesCount()
    {
        return $this->_processes_count;
    }

    /**
     * Порядковый номер этого подпроцесса.
     * Отсчет с нуля.
     * @return int
     */
    final protected function _subProcessNum()
    {
        return $this->_subprocess_num;
    }

    /**
     * PID этого подпроцесса
     * @return int
     */
    final protected function _subProcessPID()
    {
        return $this->_subprocess_pid;
    }

    /**
     * Пока возвращает true _RunContinuously продолжает порождать подпроцессы
     * @return bool
     */
    protected function _checkContinueAfterWait()
    {
        return true;
    }

    /**
     * Вызывается в родительском процессе, перед порождением подпроцесса
     * @param int $proc_num
     */
    protected function _beforeFork( $proc_num )
    {
    }

    /**
     * Порождает все подпроцессы.
     * Когда подпроцесс заканчивается он запускается вновь.
     * Блокируется пока все процессы одновременно не закончатся.
     * Или пока $this->_checkContinueAfterWait не прервет порождение новых подпроцессов.
     * После того как $this->_checkContinueAfterWait вернет false метод блокируется пока не закончатся все подпроцессы.
     * @return void
     */
    final protected function _RunContinuously()
    {
        $pid = $this->_forkAll();
        if ( 0 != $pid )
        {
            do
            {
                list( $free_proc_num, ) = $this->_waitOne();
                if ( $free_proc_num == -1 )
                {
                    $this->_finish();
                    return;
                }
                if ( !$this->_checkContinueAfterWait() )
                {
                    $this->_waitAll();
                    $this->_finish();
                    return;
                }
                $pid = $this->_forkOne( $free_proc_num );
                if ( $pid == -1 )
                {
                    $this->log_processes( 'Lost proc_num: ' . $free_proc_num . '. May be count of processes has been decreased.' );
                }
            }
            while ( $pid != 0 );
        }

        try
        {
            $data = $this->_subProcessWork();
        }
        catch ( \Exception $e )
        {
            if ( !( $e instanceof \Lib_Exception_Backtrace_Interface ) )
                \Lib_Trace::BacktraceException( $e );
            exit( self::PROC_STATUS_EXCEPTION );
        }
        $writed = fwrite( $this->_subprocess_pipe, serialize( $data ) );
        fclose( $this->_subprocess_pipe );
        if ( false === $writed )
            exit( self::PROC_STATUS_FALSE );
        else
            exit( self::PROC_STATUS_TRUE );
    }

    /**
     * Порождает все подпроцессы 1 раз.
     * Блокируется пока все они не отработают.
     * @return void
     */
    final protected function _RunOnce()
    {
        $pid = $this->_forkAll();
        if ( 0 != $pid )
        {
            $this->_waitAll();
            $this->_finish();
            return;
        }

        try
        {
            $data = $this->_subProcessWork();
        }
        catch ( \Exception $e )
        {
            if ( !( $e instanceof \Lib_Exception_Backtrace_Interface ) )
                \Lib_Trace::BacktraceException( $e );
            exit( self::PROC_STATUS_EXCEPTION );
        }

        $str = serialize( $data );

        $writed = fwrite( $this->_subprocess_pipe, $str );

        fclose( $this->_subprocess_pipe );
        if ( false === $writed )
            exit( self::PROC_STATUS_FALSE );
        else
            exit( self::PROC_STATUS_TRUE );
    }

    /**
     * Порождает процесс.
     *
     * @param int $proc_num
     *
     * @throws \Lib_Exception_Backtrace
     * @return int
     */
    private function _forkOne( $proc_num )
    {
        $pid = -1;
        if ( count( $this->_processes_pids ) >= $this->_processes_count )
            return $pid;

        $this->_beforeFork( $proc_num );

        list( $parent_pipe, $proc_socket ) = stream_socket_pair( STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP );
        $pid = pcntl_fork();
        if ( -1 == $pid )
        {
            throw new \Lib_Exception_Backtrace( 'Can\'t fork' );
        }
        elseif ( $pid )
        {
            fclose( $proc_socket );
            $this->_processes_pipes[$proc_num] = $parent_pipe;
            $this->log_processes( 'Forked #' . $proc_num . '[' . $pid . ']' );
            $this->_processes_pids[$proc_num] = $pid;

            $message = array (
                'pid' => $pid,
            );
            $message_str = serialize( $message );
            $message_len = strlen( $message_str );
            $message_len_data = pack( 'L', $message_len );
            fwrite( $parent_pipe, $message_len_data );
            fwrite( $parent_pipe, $message_str );
        }
        else
        {
            fclose( $parent_pipe );
            $this->_subprocess_pipe = $proc_socket;
            $this->_subprocess_num = $proc_num;

            $message_len_data = unpack( 'L', fread( $proc_socket, 4 ) );
            $message_len = array_shift( $message_len_data );
            $readed = 0;
            $message_str = '';
            do
            {
                $message_str .= fread( $proc_socket, $message_len - $readed );
                $readed = strlen( $message_str );
            }
            while ( $readed < $message_len );

            /** @var array $message */
            $message = unserialize( $message_str );
            $this->_subprocess_pid = $message['pid'];
        }

        return $pid;
    }

    /**
     * Порождает процессы.
     *
     * @throws \Lib_Exception_Backtrace
     *
     * @return int
     */
    private function _forkAll()
    {
        $pid = -1;
        $proc_num = 0;
        while ( count( $this->_processes_pids ) < $this->_processes_count )
        {
            $pid = $this->_forkOne( $proc_num );
            if ( $pid == 0 )
            {
                break;
            }
            $proc_num++;
        }

        return $pid;
    }

    /**
     * Функция ожидания одного порожденного процесса.
     *
     * @return array
     */
    private function _waitOne()
    {
        if ( !count( $this->_processes_pids ) )
            return array ( -1, 0 );

        $this->log_processes( 'Waiting proccess: ' . join( ',', $this->_processes_pids ) );
        $pcntl_status = -1;
        $child_pid = pcntl_wait( $pcntl_status );
        $this->log_processes( 'Finished process pid: ' . $child_pid . ' with status: ' . $pcntl_status );
        $_free_proc_num = -1;
        foreach ( $this->_processes_pids as $proc_num => $pid )
        {
            if ( $child_pid == $pid )
            {
                $_free_proc_num = $proc_num;
                $status = self::PROC_STATUS_UNKNOWN;
                if ( pcntl_wifexited( $pcntl_status ) )
                {
                    $status = pcntl_wexitstatus( $pcntl_status );
                }
                switch ( $status )
                {
                    case self::PROC_STATUS_UNKNOWN:
                        \Lib_Trace::Backtrace( 'Proccess #' . $proc_num . '[' . $child_pid . '] was not exited properly' );
                        break;
                    case self::PROC_STATUS_TRUE:
                        $data = '';
                        while ( !feof( $this->_processes_pipes[$proc_num] ) )
                        {
                            $data .= fread( $this->_processes_pipes[$proc_num], 1024 );
                        }
                        $res = unserialize( $data );
                        $this->_appendResultFromSubProcess( $res, $proc_num, $pid );
                        $this->log_processes( 'Process #' . $proc_num . '[' . $child_pid . '] returns.' );
                        break;
                    default:
                        \Lib_Trace::BackTrace( 'Process #' . $proc_num . '[' . $child_pid . '] returns unknown status: ' . $status );
                }
                fclose( $this->_processes_pipes[$proc_num] );
                unset( $this->_processes_pipes[$proc_num] );
                unset( $this->_processes_pids[$proc_num] );
                break;
            }
        }
        return array ( $_free_proc_num, (int) ( isset( $status ) && $status == self::PROC_STATUS_TRUE ) );
    }

    /**
     * Функция ожидания порожденных процессов.
     *
     * @return void
     */
    private function _waitAll()
    {
        $result_count = 0;
        $processes_count = count( $this->_processes_pids );
        while ( count( $this->_processes_pids ) )
        {
            list( , $success ) = $this->_waitOne();
            $result_count += $success;
        }
        $this->log_processes( $processes_count . ' pids finished. Result count: ' . $result_count );
    }

    /**
     * Метод родительского процесса, который вызывается после получения данных от подпроцесса.
     * Данные уже десериализованы.
     * @param mixed $data
     *
     * @param int $proc_num проядковый номер подпроцесса
     * @param int $pid PID подпроцесса
     *
     * @return void
     */
    protected function _appendResultFromSubProcess( $data, $proc_num, $pid )
    {

    }

    /**
     * Основной метод подпроцесса.
     * В нем нужно определить какие данные относятся именно к этому процессу.
     * Обработать их и вернуть результат в родителський процесесс.
     * Данные при передаче в родительский процесс сериализуются.
     * @return mixed
     */
    abstract protected function _subProcessWork();

    /**
     * Вызывается когда все процессы отработали
     * @return void
     */
    protected function _finish()
    {

    }

    /**
     * Вывод отладочной информации
     *
     * @param string $value Значение
     * @param bool $with_newline Добавлять ли PHP_EOL в конце
     */
    private function log_processes( $value = '', $with_newline = true )
    {
        if ( false == isset( $this->params['debug_processes'] ) || !$this->params['debug_processes'] )
        {
            return;
        }

        $appendix = '';
        if ( $with_newline )
        {
            $appendix = PHP_EOL;
        }

        if ( is_scalar( $value ) )
        {
            echo $value, $appendix;
        }
        else
        {
            echo var_export( $value, true ), $appendix;
        }
    }
}
