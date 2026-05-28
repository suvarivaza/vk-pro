<?php

/**
 * Библиотека для работы с датами
 *
 * @method Lib_Time add( \DateInterval $interval )
 * @method Lib_Time sub( \DateInterval $interval )
 * @method Lib_Time setTimestamp( $time )
 * @method Lib_Time setTime( $hour, $minute, $second = 0 )
 */
class Lib_Time extends DateTime
{
    public const UTC_TIMEZONE = 'UTC';

    public const CURRENT_YEAR_FORMAT = 'j F';
    public const YEAR_FORMAT = 'j F Y';
    public const TODAY_FORMAT = 'f H:i';
    public const FULL_FORMAT = 'j F Y H:i';
    public const MYSQL_FORMAT = 'Y-m-d H:i:s';
    public const MYSQL_DATE_FORMAT = 'Y-m-d';

    public const HTTP_FORMAT = 'D, d M Y H:i:s T';
    public const ISO8601_FORMAT = 'Y-m-d\TH:i:sP';
    public const SOLR_FORMAT = 'Y-m-d\TH:i:s.u\Z';
    public const SOLR_FORMAT_SHORT = 'Y-m-d\TH:i:s\Z';

    public const SECONDS_IN_DAY = 86400;

    /** @var array $_timezones_by_regid Сответствие временных зон регионам */
    private static $_timezones_by_regid = [
        2 => 'Asia/Yekaterinburg',
        16 => 'Europe/Moscow',
        14 => 'Asia/Yakutsk',
        54 => 'Asia/Novosibirsk',
        61 => 'Europe/Moscow',
        64 => 'Europe/Moscow',
        78 => 'Europe/Moscow',
        74 => 'Asia/Yekaterinburg',
        26 => 'Europe/Moscow',
        29 => 'Europe/Moscow',
        34 => 'Europe/Moscow',
        35 => 'Europe/Moscow',
        42 => 'Asia/Krasnoyarsk',
        43 => 'Europe/Moscow',
        45 => 'Asia/Yekaterinburg',
        48 => 'Europe/Moscow',
        51 => 'Europe/Moscow',
        53 => 'Europe/Moscow',
        56 => 'Asia/Yekaterinburg',
        59 => 'Asia/Yekaterinburg',
        60 => 'Europe/Moscow',
        62 => 'Europe/Moscow',
        63 => 'Europe/Samara',
        68 => 'Europe/Moscow',
        70 => 'Asia/Omsk',
        71 => 'Europe/Moscow',
        72 => 'Asia/Yekaterinburg',
        75 => 'Asia/Yakutsk',
        76 => 'Europe/Moscow',
        86 => 'Asia/Yekaterinburg',
        89 => 'Asia/Yekaterinburg',
        93 => 'Europe/Moscow',
        66 => 'Asia/Yekaterinburg',
        0 => 'Asia/Yekaterinburg',
        38 => 'Asia/Irkutsk',
        24 => 'Asia/Krasnoyarsk',
        174 => 'Asia/Yekaterinburg',
        55 => 'Asia/Omsk',
        18 => 'Europe/Samara',
        36 => 'Europe/Moscow',
        193 => 'Europe/Moscow',
        102 => 'Asia/Yekaterinburg',
        163 => 'Europe/Moscow',
    ];

    /** @var array $day_of_week Дни недели */
    public static $day_of_week = [
        0 => 'Воскресенье',
        1 => 'Понедельник',
        2 => 'Вторник',
        3 => 'Среда',
        4 => 'Четверг',
        5 => 'Пятница',
        6 => 'Суббота',
    ];

    /** @var array $month Месяцы в родительном падеже */
    public static $month = [
        1 => 'январь',
        2 => 'февраль',
        3 => 'март',
        4 => 'апрель',
        5 => 'май',
        6 => 'июнь',
        7 => 'июль',
        8 => 'август',
        9 => 'сентябрь',
        10 => 'октябрь',
        11 => 'ноябрь',
        12 => 'декабрь',
    ];

    /** @var array $month_genitive Месяцы в родительном падеже */
    public static $month_genitive = [
        1 => 'января',
        2 => 'февраля',
        3 => 'марта',
        4 => 'апреля',
        5 => 'мая',
        6 => 'июня',
        7 => 'июля',
        8 => 'августа',
        9 => 'сентября',
        10 => 'октября',
        11 => 'ноября',
        12 => 'декабря',
    ];

    /**
     * Месяцы (трехбуквенные аббревиатуры)
     *
     * @var array
     */
    public static $month_reduced = [
        1 => 'янв',
        2 => 'фев',
        3 => 'мар',
        4 => 'апр',
        5 => 'май',
        6 => 'июн',
        7 => 'июл',
        8 => 'авг',
        9 => 'сен',
        10 => 'окт',
        11 => 'ноя',
        12 => 'дек',
    ];

    /**
     * Месяцы (трехбуквенные аббревиатуры в родительном падеже)
     *
     * @var array
     */
    public static $month_reduced_genitive = [
        1 => 'янв',
        2 => 'фев',
        3 => 'мар',
        4 => 'апр',
        5 => 'мая',
        6 => 'июн',
        7 => 'июл',
        8 => 'авг',
        9 => 'сен',
        10 => 'окт',
        11 => 'ноя',
        12 => 'дек',
    ];

    /**
     * Дни недели (аббревиатуры)
     *
     * @var array
     */
    public static $day_of_week_short = [
        0 => 'ВС',
        1 => 'ПН',
        2 => 'ВТ',
        3 => 'СР',
        4 => 'ЧТ',
        5 => 'ПТ',
        6 => 'СБ',
    ];

    /**
     * Конструктор. ))
     *
     * @param string $time - дата/время строкой в любом формате, воспринимаемом DataTime
     * @param string $timezone - Часовой пояс
     *
     * @throws Lib_Exception_InvalidArgument_Backtraced
     */
    public function __construct($time, $timezone = APP_DEFAULT_TIMEZONE)
    {
        if (is_numeric($time)) {
            throw new Lib_Exception_InvalidArgument_Backtraced('Time string can\'t be a timestamp. Have: ' . $time);
        }
        parent::__construct($time, new \DateTimeZone(self::ValidateTimeZone($timezone)));
    }

    /**
     * @param string $time
     * @param string $timezone
     *
     * @return Lib_Time
     *
     * @throws Lib_Exception_InvalidArgument_Backtraced
     */
    public static function create($time, $timezone = APP_DEFAULT_TIMEZONE)
    {
        return new self($time, $timezone);
    }

    /**
     * @param string $timeZone
     *
     * @return Lib_Time
     */
    public static function now($timeZone = APP_DEFAULT_TIMEZONE)
    {
        return new self('now', $timeZone);
    }

    /**
     * @param int $regid
     *
     * @return string
     *
     * @throws Lib_Exception_InvalidArgument_Backtraced
     * @throws Lib_Exception_InvalidArgument_Type_Backtraced
     */
    public static function GetTimeZoneByRegId($regid)
    {
        if (false === is_int($regid) || $regid < 0) {
            throw new Lib_Exception_InvalidArgument_Type_Backtraced($regid, 'natural int');
        }

        if (false === isset(self::$_timezones_by_regid[$regid])) {
            return APP_DEFAULT_TIMEZONE;
        }

        return self::$_timezones_by_regid[$regid];
    }

    /**
     * Создаем экземпляр класса Lib_Time, используя форматированную строку
     *
     * @static
     *
     * @param string $format Формат ввода данных
     * @param string $time Строка, содержащая дату/время в указанном формате
     * @param string $timezone Временная зона входящих данных
     *
     * @throws Lib_Exception_InvalidArgument
     *
     * @return Lib_Time
     */
    public static function createFromFormat($format, $time, $timezone = APP_DEFAULT_TIMEZONE)
    {
        $instance = new self('now', $timezone);
        $timezone = $instance->getTimezone();

        $datetime = parent::createFromFormat($format, $time, $timezone);

        if (false === $datetime && $format === self::SOLR_FORMAT) {
            $datetime = parent::createFromFormat(self::SOLR_FORMAT_SHORT, $time, $timezone);
        }

        if (false === $datetime) {
            throw new \Lib_Exception_InvalidArgument('Invalid argument for createFromFormat: ' . $time . ' with format: ' . $format);
        }

        $instance->setTimestamp($datetime->getTimestamp());

        return $instance;
    }

    /**
     * Создаем экземпляр класса Lib_Time, используя TimeStamp в качестве входных данных
     *
     * @static
     *
     * @param int $timestamp Входящая метка времени
     * @param string $timezone Временная зона входящих данных
     *
     * @throws Lib_Exception_InvalidArgument
     *
     * @return Lib_Time
     */
    public static function createFromTimestamp($timestamp, $timezone = APP_DEFAULT_TIMEZONE)
    {
        if (false === is_int($timestamp)) {
            throw new \Lib_Exception_InvalidArgument('Invalid argument for createFromTimestamp');
        }
        $instance = new self('now', $timezone);
        $instance->setTimestamp($timestamp);

        return $instance;
    }

    /**
     * @param string $timeZone
     *
     * @return $this
     *
     * @throws Lib_Exception_InvalidArgument_Backtraced
     */
    public function setTimezone($timeZone)
    {
        parent::setTimezone(new \DateTimeZone(self::ValidateTimeZone($timeZone)));

        return $this;
    }

    /**
     * @param string|null $timezone
     *
     * @return string
     */
    public function formatMySqlDateTime($timezone = null)
    {
        return $this->format(self::MYSQL_FORMAT, $timezone);
    }

    /**
     * @param string|null $timezone
     *
     * @return string
     */
    public function formatMySqlDate($timezone = null)
    {
        return $this->format(self::MYSQL_DATE_FORMAT, $timezone);
    }

    /**
     * Форматируем дату по заданному шаблону
     *
     * @param string $format Шаблон для вывода данных
     * @param string|null $timezone Часовой пояс, в котором ВЫВОДИМ данные
     *
     * @return string
     */
    public function format($format = self::FULL_FORMAT, $timezone = null)
    {
        $prevTimezone = null;

        if (empty($format)) {
            $format = self::FULL_FORMAT;
        }

        if (null !== $timezone) {
            $timezone = new \DateTimeZone(self::ValidateTimeZone($timezone));
        }

        if (null !== $timezone && $timezone->getName() !== $this->getTimezone()->getName()) {
            $prevTimezone = $this->getTimezone();
            parent::setTimezone($timezone);
        }

        $mon = parent::format('n');
        $format = str_replace('Fs', self::$month_reduced_genitive[$mon], $format);
        $format = str_replace('Fl', self::$month[$mon], $format);
        $format = str_replace('F', self::$month_genitive[$mon], $format);

        $d = parent::format('w');
        $format = str_replace('Dw', self::$day_of_week[$d], $format);

        $text = parent::format($format);

        if (null !== $prevTimezone) {
            parent::setTimezone($prevTimezone);
        }

        return $text;
    }

    /**
     * Форматируем дату по заданному шаблону.
     * Вывод на русском, языке.
     *
     * @param string $format Шаблон для вывода ближайших дат (вчера-сегодня-завтра)
     * j - текущий день месяца без ведущего нуля
     * F - полное наименование месяца
     * Y - 4-хзначный номер года
     * f - название дня ('сегодня', 'завтра', 'вчера') или что-то еще
     * H - часы
     * i - минуты (с ведущим нулем)
     * @param string $format2 Шаблон для вывода дат в течении текущего года
     * @param string $format3 Шаблон для вывода дат в другие года
     * @param string|null $timezone Часовой пояс, в котором ВЫВОДИМ данные
     *
     * @return string
     */
    public function formatText($format = self::TODAY_FORMAT, $format2 = self::CURRENT_YEAR_FORMAT, $format3 = self::YEAR_FORMAT, $timezone = null)
    {
        return $this->simplyDate($timezone, $format, $format2, $format3);
    }

    /**
     * Форматирует дату
     *
     * @param string $timezone
     * @param string $format Формат
     * j - текущий день месяца без ведущего нуля
     * F - полное наименование месяца
     * Y - 4-хзначный номер года
     * f - название дня ('сегодня', 'завтра', 'вчера') или что-то еще
     * H - часы
     * i - минуты (с ведущим нулем)
     * @param string $format2 Формат даты ранее, чем позавчера
     * @param string $format3 Формат даты, ранее года
     *
     * @return string
     */
    public function simplyDate($timezone = null, $format = self::TODAY_FORMAT, $format2 = self::CURRENT_YEAR_FORMAT, $format3 = self::YEAR_FORMAT)
    {
        $timezone_ = $timezone;

        if ($timezone_ === null) {
            $timezone_ = $this->getTimezone()->getName();
        }

        // вычисляем текущее локальное время
        $nowtime = self::create('today', $timezone_);
        // это timestamp начала дня 00:00:00
        $day_begin_time = $nowtime->getTimestamp();
        // это текущий год
        $nowyear = $nowtime->format('y', $timezone);

        $year = $this->format('y', $timezone);
        $mon = $this->format('n', $timezone);

        $text = '';
        // проверяем разницу в днях
        if ($day_begin_time <= $this->getTimestamp() && $day_begin_time + 86400 > $this->getTimestamp()) {
            $text = 'сегодня';
        } elseif ($day_begin_time + 86400 <= $this->getTimestamp() && $day_begin_time + 2 * 86400 > $this->getTimestamp()) {
            $text = 'завтра';
        } elseif ($day_begin_time - 86400 <= $this->getTimestamp() && $day_begin_time > $this->getTimestamp()) {
            $text = 'вчера';
        } elseif ($day_begin_time - 2 * 86400 <= $this->getTimestamp() && $day_begin_time - 86400 > $this->getTimestamp()) {
            $text = 'позавчера';
        } elseif ($nowyear == $year) {
            $format = $format2;
        } else {
            $format = $format3;
        }

        $format = str_replace('f', $text, $format);
        $format = str_replace('Fr', self::$month_reduced[$mon], $format);
        $format = str_replace('Fs', self::$month_reduced_genitive[$mon], $format);
        $format = str_replace('Fl', self::$month[$mon], $format);
        $format = str_replace('F', self::$month_genitive[$mon], $format);
        $format = str_replace('Dw', self::$day_of_week[$this->format('w', $timezone)], $format);

        return $this->format($format, $timezone);
    }

    /**
     * @param int $seconds
     * @param bool $force_seconds
     *
     * @throws Lib_Exception_InvalidArgument
     *
     * @return string
     */
    public static function FormatTime($seconds, $force_seconds = false)
    {
        if (!is_int($seconds)) {
            throw new \Lib_Exception_InvalidArgument('Seconds must be integer, not ' . gettype($seconds) . ' with value ' . $seconds);
        }
        $sec = $seconds % 60;
        $minutes = intval(($seconds - $sec) % 3600 / 60);
        $hours = intval($seconds / 3600);
        $res = $hours < 10 ? '0' . $hours : $hours;
        $res .= ':' . ($minutes < 10 ? '0' . $minutes : $minutes);
        $res .= ($sec > 0 || $force_seconds) ? ':' . ($sec < 10 ? '0' . $sec : $sec) : '';

        return $res;
    }

    /**
     * @param string $str
     *
     * @return int
     */
    public static function TimeFromString($str)
    {
        $parts = explode(':', $str, 3);
        $parts = array_map('intval', $parts);

        while (count($parts) < 3) {
            $parts[] = 0;
        }

        return $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
    }

    /**
     * Форматирует время последнего захода пользователя
     *
     * @param int $regID
     * @param int $time Дата
     * @param int $gender Пол пользователя
     * @param bool $textonly
     *
     * @return string
     */
    public static function UserOnLine($regID, $time, $gender = 1, $textonly = false)
    {
        $timezone = self::GetTimeZoneByRegId($regID);

        if (is_string($time)) {
            $time = self::createFromFormat(self::MYSQL_FORMAT, (string) $time, $timezone)->getTimestamp();
        }

        if ($time <= 0) {
            return '';
        }

        $now = new self('now', $timezone);

        $user_time = clone $now;
        $user_time->setTimestamp($time);

        $nowtime = $now->getTimestamp();

        if ($nowtime - 15 * 60 < $time) {
            if ($textonly) {
                return 'сейчас на сайте';
            } else {
                return '<img src="http://i.sdska.ru' . DOMAIN_SUFFIX . '/_img/passport/default/online_01.gif" width="100" height="10" alt="на сайте" />';
            }
        }

        $yesterday = clone $now;
        $two_days_ago = clone $now;
        $yesterday->sub(new DateInterval('P1D'));
        $two_days_ago->sub(new DateInterval('P2D'));

        if ($now->format('Ymd') == $user_time->format('Ymd')) {
            return 'был' . ($gender == 2 ? 'а' : '') . ' сегодня в ' . $user_time->format('H:i');
        } elseif ($yesterday->format('Ymd') == $user_time->format('Ymd')) {
            return 'был' . ($gender == 2 ? 'а' : '') . ' вчера в ' . $user_time->format('H:i');
        } elseif ($two_days_ago->format('Ymd') == $user_time->format('Ymd')) {
            return 'был' . ($gender == 2 ? 'а' : '') . ' позавчера в ' . $user_time->format('H:i');
        } else {
            if ($now->format('Y') == ($year = $user_time->format('Y'))) {
                $year = '';
            }

            return 'был' . ($gender == 2 ? 'а' : '') . ' ' . $user_time->format('d') . ' ' . self::$month_genitive[intval($user_time->format('n'))] . ($year ? ' ' . $year . 'г.' : '');
        }
    }

    /**
     * @return Lib_Time
     */
    public function cloneIt()
    {
        return clone $this;
    }

    /**
     * @param string $timezone
     *
     * @return string
     *
     * @throws Lib_Exception_InvalidArgument
     */
    public static function ValidateTimeZone($timezone)
    {
        try {
            $tz = new \DateTimeZone($timezone);

            return $tz->getName();
        } catch (\Exception $e) {
            throw new \Lib_Exception_InvalidArgument_Backtraced('Invalid timezone \'' . $timezone . '\'', $e->getCode(), $e);
        }
    }

    /**
     * @param float $latitude
     * @param float $longitude
     *
     * @return Lib_Time
     *
     * @throws Lib_Exception_InvalidArgument_Backtraced
     */
    public function sunrise($latitude, $longitude)
    {
        $sun = new \Lib_Sun($this);

        return $sun->getSunrise($latitude, $longitude);
    }

    /**
     * @param float $latitude
     * @param float $longitude
     *
     * @return Lib_Time
     *
     * @throws Lib_Exception_InvalidArgument_Backtraced
     */
    public function sunset($latitude, $longitude)
    {
        $sun = new \Lib_Sun($this);

        return $sun->getSunset($latitude, $longitude);
    }

    /**
     * Вывод промежутка между датами в виде 26 ноября-31 декабря
     * (если один месяц, то отображается 1 раз, если есть год, то пишется год).
     *
     * @param int $date1
     * @param int $date2
     *
     * @return string
     */
    public static function DateRange($date1, $date2)
    {
        $return_date = '';

        if (date('n', $date1) == date('n', $date2)) { //Если разные месяцы
            if (date('j', $date1) != date('j', $date2)) { //Если разные дни
                $return_date .= date('j', $date1) . '-';
            }
            $return_date .= date('j', $date2) . ' ' . self::$month_genitive[date('n', $date1)];
        } else {
            $return_date .= date('j', $date1) . ' ' . self::$month_genitive[date('n', $date1)];

            if (date('Y', $date1) != date('Y', $date2)) { //Если разные года
                $return_date .= ' ' . date('Y', $date1);
            }
            $return_date .= ' - ' . date('j', $date2) . ' ' . self::$month_genitive[date('n', $date2)];

            if (date('Y', $date1) != date('Y', $date2)) {
                $return_date .= ' ' . date('Y', $date2);
            }
        }

        return $return_date;
    }

    /**
     * @param string $start_time
     * @param string $end_time
     * @param string $interval
     * @param string $key_format
     * @param string $value_format
     * @param string $timezone
     *
     * @return array
     */
    public static function rangeGenerator($start_time, $end_time, $interval, $key_format, $value_format, $timezone = APP_DEFAULT_TIMEZONE)
    {
        $res = [];

        $time = self::create($start_time, $timezone);
        $to = self::create($end_time, $timezone)->getTimestamp();

        if ($time->getTimestamp() < $to) {
            for ($i = 0; $time->getTimestamp() < $to; $time->add(new \DateInterval($interval)), $i++) {
                $res[$key_format ? $time->format($key_format) : $i] = $time->format($value_format);
            }
        } else {
            for ($i = 0; $time->getTimestamp() > $to; $time->sub(new \DateInterval($interval)), $i++) {
                $res[$key_format ? $time->format($key_format) : $i] = $time->format($value_format);
            }
        }

        return $res;
    }

    /**
     * @param string $start_time
     * @param int $count
     * @param string $interval
     * @param string $key_format
     * @param string $value_format
     * @param string $timezone
     *
     * @return array
     */
    public static function rangeGenerator2($start_time, $count, $interval, $key_format, $value_format, $timezone = APP_DEFAULT_TIMEZONE)
    {
        $res = [];

        if ($count > 0) {
            for ($time = self::create($start_time, $timezone), $i = 0; $i < $count; $time->add(new \DateInterval($interval)), $i++) {
                $res[$key_format ? $time->format($key_format) : $i] = $time->format($value_format);
            }
        } else {
            for ($time = self::create($start_time, $timezone), $i = 0; $i < -1 * $count; $time->sub(new \DateInterval($interval)), $i++) {
                $res[$key_format ? $time->format($key_format) : $i] = $time->format($value_format);
            }
        }

        return $res;
    }
}
