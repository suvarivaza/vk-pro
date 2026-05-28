<?php
/**
 * Библиотека отправки почты.
 * Возможно использование различных транспортов: как sendmail, так и своя реализация smtp-протокола.
 */
class Lib_Mail_Sender
{
    /** Неизвестная ошибка */
    public const ERR_UNKNOWN_ERROR = 1;
    /** Не задан ни один адресат */
    public const ERR_FIELD_TO_IS_EMPTY = 2;
    /** Не задан отправитель */
    public const ERR_FIELD_FROM_IS_EMPTY = 3;
    /** Ошибка отправки сообщения */
    public const ERR_SEND_ERROR = 4;
    /** Отсутствует тело сообщения */
    public const ERR_NO_PREPARED_BODY = 5;

    public const BT_PLAIN = 'text/plain';
    public const BT_HTML = 'text/html';
    public const BT_ALTERNATIVE = 'multipart/alternative';
    public const BT_MIXED = 'multipart/mixed';

    public const PR_URGENT = 2;
    public const PR_NORMAL = 3;
    public const PR_NONURGENT = 4;

    /**
     * @par Типы транспортов
     */
    /** sendmail-транспорт */
    public const ST_MAIL = 1;
    /** Собственная реализация smtp-протокола со случайным выбором сервера из списка */
    public const ST_SMTP_Rand = 3;
    /** Собственная реализация smtp-протокола с последовательным выбором сервера из списка */
    public const ST_SMTP_RoundRobin = 4;

    /** @var Lib_Mail_Transport_Abstract $_transport */
    private $_transport = null;

    private $_address = [
        'from' => [],
        'to' => [],
        'cc' => [],
        'bcc' => [],
        'reply-to' => [],
        'return-path' => [],
    ];

    private $_headers = [];
    private $_body = [];

    private $_body_ready = false;

    private $_trusted_headers = [
        'Disposition-Notification-To',
        'Subject',
        'Precedence',
    ];

    private $_trusted_address_type = [
        'from' => 'From',
        'to' => 'To',
        'cc' => 'Cc',
        'bcc' => 'Bcc',
        'reply-to' => 'Reply-To',
        'return-path' => 'Return-Path',
    ];

    private $charset = 'utf-8';
    private $body_type = null;
    private $mailer = null;
    private $priority = 3;

    /**
     * Возвращает строку в виде MIME последовательности
     *
     * @param string $input
     *
     * @return string
     */
    private function encode_mime_string($input)
    {
        $preferences = [
            'input-charset' => $this->charset,
            'output-charset' => $this->charset,
            'line-length' => 500,
            'line-break-chars' => '', //"\r\n",
            'scheme' => 'B',
        ];

        $out = substr(iconv_mime_encode('', $input, $preferences), 2);

        $out = str_replace('[', '=5B', $out);
        $out = str_replace(']', '=5D', $out);
        $out = str_replace('(', '=28', $out);
        $out = str_replace(')', '=29', $out);

        return $out;
    }

    /**
     * Собирает дополнительные заголовки сообщения
     *
     * @throws Lib_Exception_Logic_Backtraced
     * @throws Lib_Exception_InvalidArgument_Backtraced
     *
     * @return bool
     */
    public function PrepareAdditionalHeaders()
    {
        if (true !== $this->_body_ready) {
            throw new Lib_Exception_Logic_Backtraced('Headers must be prepared after body', self::ERR_NO_PREPARED_BODY);
        }

        $headers = '';

        $headers .= "MIME-Version: 1.0\r\n";

        if ($this->mailer !== null) {
            $headers .= 'X-Mailer: ' . $this->mailer . "\r\n";
        }

        if ($this->priority != self::PR_NORMAL) {
            $headers .= 'Priority: ' . $this->priority . "\r\n";
        }

        if (sizeof($this->_headers) == 0) {
            return $headers;
        }

        foreach ($this->_headers as $k => $v) {
            if ($k == 'Subject') {
                continue;
            }
            $headers .= $k . ': ' . $v . "\r\n";
        }

        if (sizeof($this->_address['from']) < 1) {
            throw new Lib_Exception_InvalidArgument_Backtraced('Field FROM is empty', self::ERR_FIELD_FROM_IS_EMPTY);
        }

        foreach ($this->_trusted_address_type as $which => $title) {
            if ($which == 'to') {
                continue;
            }

            if (false === isset($this->_address[$which]) || sizeof($this->_address[$which]) < 1) {
                continue;
            }
            $headers .= $title . ': ' . implode(', ', $this->_address[$which]) . "\r\n";
        }

        return $headers;
    }

    /**
     * Отправка сообщения
     */
    public function Send()
    {
        if ($this->_transport === null) {
            $this->SetTransport();
        }
        $this->_transport->Send($this);
    }

    /**
     * Добавить заголовок (внутренний метод)
     *
     * @param string $name Название заголовка
     * @param string $value Значение заголовка
     * @param bool $encode кодировать ли значение заголовка?
     *
     * @throws Lib_Exception_InvalidArgument
     *
     * @return bool
     */
    private function _AddHeader($name, $value, $encode = true)
    {
        if ($encode === true) {
            $this->_headers[$name] = $this->encode_mime_string($value);
        } else {
            if (!mb_check_encoding($value, 'US-ASCII')) {
                throw new Lib_Exception_InvalidArgument('Invalid message header value');
            }
            $this->_headers[$name] = $value;
        }

        return true;
    }

    /**
     * Добавить заголовок
     *
     * @param string $name Название заголовка
     * @param string $value Значение заголовка
     * @param bool $encode кодировать ли значение заголовка?
     *
     * @return bool Возвращает true, в случае успеха
     */
    public function AddHeader($name, $value, $encode = true)
    {
        if (false === in_array($name, $this->_trusted_headers)) {
            return false;
        }

        return $this->_AddHeader($name, $value, $encode);
    }

    /**
     * Удалить заголовок
     *
     * @param string $name Название заголовка
     */
    public function DeleteHeader($name)
    {
        if (isset($this->_headers[$name])) {
            unset($this->_headers[$name]);
        }
    }

    /**
     * Проверяет, установлен ли заголовок
     *
     * @param string $name Название заголовка
     *
     * @return bool
     */
    public function IssetHeader($name)
    {
        return isset($this->_headers[$name]);
    }

    /**
     * Возвращает заголовок
     *
     * @param string $name    Название заголовка
     *
     * @return string|null Возвращает значение заголовка, или null, если такой заголовок не установлен
     */
    public function GetHeader($name)
    {
        if (isset($this->_headers[$name])) {
            return $this->_headers[$name];
        }

        return null;
    }

    /**
     * Добавить вложение (mimetype определяется автоматически)
     *
     * @param string $name Название куска
     * @param string $path Путь до файла
     *
     * @return bool
     */
    public function AddAttachment($name, $path)
    {
        if (!is_file($path)) {
            return false;
        }

        $fi = new finfo(FILEINFO_MIME);
        $mt = 'application/octet-stream';

        if (is_file($path)) {
            $mtype = $fi->file($path);
            list($mt) = explode(';', $mtype, 2);
        }
        $fileName = explode('.', $path);
        $ext = array_pop($fileName);

        if ($ext === 'xlsx') {
            $mt = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        }
        $file = file_get_contents($path);

        $type = $mt;

        return $this->AddBody($name, $file, $type, null, true);
    }

    /**
     * Добавить кусок тела
     *
     * @param string $name Название куска
     * @param string $value Значение куска
     * @param string $type тип куска (mime-type) (BT_*)
     * @param string $charset Кодировка
     * @param bool $attachment Является ли этот кусок вложением?
     *
     * @return bool
     */
    public function AddBody($name, $value, $type = null, $charset = null, $attachment = false)
    {
        if ($type === null) {
            $type = self::BT_PLAIN;
        }

        $this->_body[$name] = [
            'name' => $name,
            'value' => $value,
            'type' => $type,
            'charset' => $charset,
            'attachment' => $attachment,
        ];

        $this->_body_ready = false;

        return true;
    }

    /**
     * Удалить кусок тела
     *
     * @param string $name Название куска
     *
     * @return bool
     */
    public function DeleteBody($name)
    {
        if (isset($this->_body[$name])) {
            unset($this->_body[$name]);
        }

        $this->_body_ready = false;

        return true;
    }

    /**
     * Установлен ли кусок тела
     *
     * @param string $name Название куска
     *
     * @return bool
     */
    public function IssetBody($name)
    {
        return isset($this->_body[$name]);
    }

    /**
     * Собирает тело сообщения
     *
     * @return string Возвращает подготовленное тело сообщения
     */
    public function PrepareBody()
    {
        $body = '';

        if (sizeof($this->_body) == 0) {
            $this->_AddHeader('Content-type', 'text/plain; charset=' . $this->charset, false);
        } elseif (sizeof($this->_body) == 1) {
            foreach ($this->_body as $v) {
                $this->_AddHeader('Content-type', $v['type'] . '; charset=' . ($v['charset'] ? $v['charset'] : $this->charset), false);
                $this->_AddHeader('Content-Transfer-Encoding', 'base64', false);
                $body = chunk_split(base64_encode($v['value'])) . "\r\n";
            }
        } elseif (sizeof($this->_body) > 1) {
            $boundary = '--' . md5(time() . rand(1, 400));

            if ($this->body_type == self::BT_MIXED || $this->body_type == self::BT_ALTERNATIVE) {
                $this->_AddHeader('Content-type', $this->body_type . ";\r\n\tboundary=\"" . $boundary . '"', false);
            } else {
                $this->_AddHeader('Content-type', self::BT_MIXED . ";\r\n\tboundary=\"" . $boundary . '"', false);
            }

            foreach ($this->_body as $v) {
                $body .= '--' . $boundary . "\r\n";

                if ($v['attachment']) {
                    //-------- перебираем все вложения
                    $body .= 'Content-Type: ' . $v['type'] . '; name="' . $this->encode_mime_string($v['name']) . "\"\r\n";
                    $body .= "Content-Transfer-Encoding: base64\r\n";
                    $body .= 'Content-Disposition: attachment; filename="' . $this->encode_mime_string($v['name']) . "\"\r\n\r\n";
                    $body .= chunk_split(base64_encode($v['value'])) . "\r\n";
                } else {
                    //-------- текст сообщения
                    $body .= 'Content-Type: ' . $v['type'] . '; charset=' . ($v['charset'] ? $v['charset'] : $this->charset) . "\r\n";
                    $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
                    $body .= chunk_split(base64_encode($v['value'])) . "\r\n";
                }
            }
            $body .= '--' . $boundary . "--\r\n";
        }

        $this->_body_ready = true;

        return $body;
    }

    /**
     * Добавить адрес
     *
     * @param string $which Тип адреса (to, from, cc, bcc)
     * @param string $email Email
     * @param string $name Имя
     * @param bool $encode кодировать ли имя?
     *
     * @return bool Возвращает true, в случае успеха
     */
    public function AddAddress($which, $email, $name = null, $encode = true)
    {
        if (!isset($this->_trusted_address_type[$which])) {
            return false;
        }

        if ($email === null || Lib_Text::IsEmail($email) === false) {
            return false;
        }

        if ($name !== null && $name != '' && $name !== 'NIL') {
            if ($encode === true) {
                $name = $this->encode_mime_string($name);
            }
            $this->_address[$which][$email] = $name . ' <' . $email . '>';
        } else {
            $this->_address[$which][$email] = $email;
        }

        return true;
    }

    /**
     * Установлен ли адрес
     *
     * @param string $which Тип адреса (to, from, cc, bcc)
     * @param string $email Email
     *
     * @return bool
     */
    public function IssetAddress($which, $email)
    {
        if (false === isset($this->_trusted_address_type[$which])) {
            return false;
        }

        return isset($this->_address[$which][$email]);
    }

    /**
     * Удалить адрес
     *
     * @param string $which Тип адреса (to, from, cc, bcc)
     * @param string $email Email
     *
     * @return bool
     */
    public function DeleteAddress($which, $email)
    {
        if (!isset($this->_trusted_address_type[$which])) {
            return false;
        }

        if (isset($this->_address[$which][$email])) {
            unset($this->_address[$which][$email]);
        }

        return true;
    }

    /**
     * Получение установленного адреса
     *
     * @param string $which Тип адреса (to, from, cc, bcc)
     *
     * @return array
     */
    public function GetAddress($which)
    {
        if (false === isset($this->_trusted_address_type[$which])) {
            return [];
        }

        return $this->_address[$which];
    }

    /**
     * Установить рабочую кодировку
     *
     * @param string $value кодировка
     */
    public function SetCharset($value)
    {
        $this->charset = $value;
        $this->Flush();
    }

    /**
     * Установить приоритет
     *
     * @param int $value приоритет (PR_*)
     *
     * @return bool Возвращает true, в случае успеха
     */
    public function SetPriority($value)
    {
        $value = intval($value);

        if ($value < 2 || $value > 4) {
            return false;
        }
        $this->priority = $value;

        return true;
    }

    /**
     * Установить название mailera
     *
     * @param string $value название
     */
    public function SetMailer($value)
    {
        $this->mailer = $value;
    }

    /**
     * Установить body type
     *
     * @param string $value Body Type (BT_*)
     */
    public function SetBodyType($value)
    {
        $this->body_type = $value;
    }

    /**
     * Установить транспорт
     *
     * @param int $value Тип транспорта
     *         Одна из констант ST_*
     */
    public function SetTransport($value = 0)
    {
        if ($this->_transport !== null) {
            unset($this->_transport);
        }

        switch ($value) {
            case self::ST_MAIL:
                $this->_transport = new Lib_Mail_Transport_PHPMail();
                break;
            default:
                $this->_transport = new Lib_Mail_Transport_PHPMail();
        }
    }

    /**
     * Закрывает все открытые подключения, уничтожает все созданные транспорты
     *
     * @return bool Возвращает true, в случае успеха
     */
    public function Flush()
    {
        if ($this->_transport !== null) {
            $this->_transport->Flush();
        }
        $this->_transport = null;

        return true;
    }
}
