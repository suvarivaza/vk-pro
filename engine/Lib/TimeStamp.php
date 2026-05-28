<?php

/**
 * Библиотека для работы с датами
 */
class Lib_TimeStamp extends DateTime
{
    /** TZ данных, которые хранятся в базе */
    public const DEFAULT_TIMEZONE = 'Europe/Moscow';

    //@{
    /** @name Предопределенные форматы */
    public const CURRENT_YEAR_FORMAT = 'j F';
    public const YEAR_FORMAT = 'j F Y';
    public const TODAY_FORMAT = 'f H:i';
    public const FULL_FORMAT = 'j F Y H:i';
    public const MYSQL_FORMAT = 'Y-m-d H:i:s';
    public const MYSQL_DATE_FORMAT = 'Y-m-d';

    public const HTTP_FORMAT = 'D, d M Y H:i:s T';
    public const ISO8601_FORMAT = 'Y-m-d\TH:i:sP';
    //}@

    public const SECONDS_IN_DAY = 86400;

    public static $hours = [
        '00:00',
        '01:00',
        '02:00',
        '03:00',
        '04:00',
        '05:00',
        '06:00',
        '07:00',
        '08:00',
        '09:00',
        '10:00',
        '11:00',
        '12:00',
        '13:00',
        '14:00',
        '15:00',
        '16:00',
        '17:00',
        '18:00',
        '19:00',
        '20:00',
        '21:00',
        '22:00',
        '23:00',
        '24:00',
    ];
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
        42 => 'Asia/Omsk',
        43 => 'Europe/Moscow',
        45 => 'Asia/Yekaterinburg',
        48 => 'Europe/Moscow',
        51 => 'Europe/Moscow',
        53 => 'Europe/Moscow',
        56 => 'Asia/Yekaterinburg',
        59 => 'Asia/Yekaterinburg',
        60 => 'Europe/Moscow',
        62 => 'Europe/Moscow',
        63 => 'Europe/Moscow',
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
        18 => 'Europe/Moscow',
        36 => 'Europe/Moscow',
        193 => 'Europe/Moscow',
        102 => 'Asia/Yekaterinburg',
        163 => 'Europe/Moscow',
    ];

    /**
     * Текущая временная зона
     *
     * @var DateTimeZone|null
     */
    private static $_timezone = null;

    /** @var array $day_of_week Дни недели */
    public static $day_of_week = [
        1 => 'Понедельник',
        2 => 'Вторник',
        3 => 'Среда',
        4 => 'Четверг',
        5 => 'Пятница',
        6 => 'Суббота',
        0 => 'Воскресенье',
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

    /** @var array $interval_name Служебные слова для интервалов времени */
    public static $interval_name = [
        'day' => 'день',
        'now' => 'сейчас',
        'before yesterday' => 'позавчера',
        'yesterday' => 'вчера',
        'today' => 'сегодня',
        'tomorrow' => 'завтра',
    ];

    /**
     * Конструктор. ))
     *
     * @param string $time - дата/время строкой в любом формате, воспринимаемом DataTime
     * @param DateTimeZone $timezone - Часовой пояс для объекта. Если не указан - ставится как на сайте
     *
     * @return \Lib_TimeStamp
     */
    public function __construct($time = 'now', DateTimeZone $timezone = null)
    {
        if (null === self::$_timezone) {
            self::setDefaultTimeZone(self::DEFAULT_TIMEZONE);
        }

        if (null === $timezone) {
            $timezone = self::$_timezone;
        }

        parent::__construct($time, $timezone);
    }

    /**
     * @param int $regid
     *
     * @return Lib_TimeStamp
     */
    public static function now($regid)
    {
        $timezone = new DateTimeZone(self::GetTimeZoneByRegId($regid));

        return new self('now', $timezone);
    }

    /**
     * Получение временной зоны по региону
     *
     * @static
     *
     * @param int $regid Идентификатор региона
     *
     * @return string
     *
     * @throws Lib_Exception_InvalidArgument_Type_Backtraced
     */
    public static function GetTimeZoneByRegId($regid)
    {
        if (false === is_int($regid) || $regid < 0) {
            throw new Lib_Exception_InvalidArgument_Type_Backtraced($regid, 'natural int');
        }

        if (false === isset(self::$_timezones_by_regid[$regid])) {
            return self::DEFAULT_TIMEZONE;
        }

        return self::$_timezones_by_regid[$regid];
    }

    /**
     * Установка текущей временной зоны по умолчанию
     *
     * @static
     *
     * @param string $timezone
     *
     * @throws Lib_Exception_InvalidArgument_Backtraced
     */
    public static function setDefaultTimeZone($timezone)
    {
        try {
            self::$_timezone = new DateTimeZone($timezone);
        } catch (Exception $e) {
            throw new Lib_Exception_InvalidArgument_Backtraced('Invalid timezone \'' . $timezone . '\'');
        }
    }

    /**
     * Создаем экземпляр класса Lib_TimeStamp, используя форматированную строку
     *
     * @static
     *
     * @param string $format Формат ввода данных
     * @param string $time Строка, содержащая дату/время в указанном формате
     * @param DateTimeZone|null $timezone Временная зона входящих данных
     *
     * @throws Lib_Exception_InvalidArgument
     *
     * @return Lib_TimeStamp
     */
    public static function createFromFormat($format, $time, $timezone = null)
    {
        $instance = new self();

        if (null === $timezone) {
            $timezone = self::$_timezone;
        }

        $datetime = parent::createFromFormat($format, $time, $timezone);

        if (false === $datetime) {
            throw new \Lib_Exception_InvalidArgument('Invalid argument for createFromFormat');
        }

        $instance->setTimezone(self::$_timezone);
        $instance->setTimestamp($datetime->getTimestamp());

        return $instance;
    }

    /**
     * Создаем экземпляр класса Lib_TimeStamp, используя TimeStamp в качестве входных данных
     *
     * @static
     *
     * @param int $timestamp Входящая метка времени
     * @param DateTimeZone|null $timezone Временная зона входящих данных
     *
     * @throws Lib_Exception_InvalidArgument
     *
     * @return Lib_TimeStamp
     */
    public static function createFromTimestamp($timestamp, DateTimeZone $timezone = null)
    {
        if (false === is_int($timestamp)) {
            throw new \Lib_Exception_InvalidArgument('Invalid argument for createFromTimestamp');
        }
        $instance = new self();

        if (null === $timezone) {
            $timezone = self::$_timezone;
        }

        $instance->setTimezone($timezone);
        $instance->setTimestamp($timestamp);

        return $instance;
    }

    /**
     * @return string
     */
    public function formatMySqlDateTime()
    {
        return $this->format(self::MYSQL_FORMAT);
    }

    /**
     * @return string
     */
    public function formatMySqlDate()
    {
        return $this->format(self::MYSQL_DATE_FORMAT);
    }

    /**
     * Форматируем дату по заданному шаблону
     *
     * @param string $format Шаблон для вывода данных
     * @param DateTimeZone|null $timezone Часовой пояс, в котором ВЫВОДИМ данные
     *
     * @return string
     */
    public function format($format = self::FULL_FORMAT, $timezone = null)
    {
        $prevTimezone = null;

        if (empty($format)) {
            $format = self::FULL_FORMAT;
        }

        if (null === $timezone) {
            $timezone = self::$_timezone;
        }

        if ($timezone->getName() !== $this->getTimezone()->getName()) {
            $prevTimezone = $this->getTimezone();
            $this->setTimezone($timezone);
        }

        $mon = parent::format('n');
        $format = str_replace('Fi', self::$month[$mon], $format);
        $format = str_replace('Fs', self::$month_reduced_genitive[$mon], $format);
        $format = str_replace('F', self::$month_genitive[$mon], $format);

        $d = parent::format('w');
        $format = str_replace('Dw', self::$day_of_week[$d], $format);

        $text = parent::format($format);

        if (null !== $prevTimezone) {
            $this->setTimezone($prevTimezone);
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
     * @param DateTimeZone|null $timezone Часовой пояс, в котором ВЫВОДИМ данные
     *
     * @return string
     */
    public function formatText($format = self::TODAY_FORMAT, $format2 = self::CURRENT_YEAR_FORMAT, $format3 = self::YEAR_FORMAT, $timezone = null)
    {
        $text = '';
        $prevTimezone = null;

        if (null === $timezone) {
            $timezone = self::$_timezone;
        }

        if ($timezone->getName() !== $this->getTimezone()->getName()) {
            $prevTimezone = $this->getTimezone();
            $this->setTimezone($timezone);
        }

        $nowtime = new self('now', $this->getTimezone());
        list($nowtime_Y, $nowtime_m, $nowtime_d) = array_map('intval', explode('-', $nowtime->format('Y-m-d')));
        $nowTimeStamp = mktime(0, 0, 0, $nowtime_m, $nowtime_d, $nowtime_Y);

        list($Y, $m, $d) = array_map('intval', explode('-', $this->format('Y-m-d')));
        $curTimeStamp = mktime(0, 0, 0, $m, $d, $Y);

        // проверяем разницу в днях
        if ($nowTimeStamp <= $curTimeStamp && $nowTimeStamp + 86400 > $curTimeStamp) {
            $text = self::$interval_name['today'];
        } elseif ($nowTimeStamp + 86400 <= $curTimeStamp && $nowTimeStamp + 2 * 86400 > $curTimeStamp) {
            $text = self::$interval_name['tomorrow'];
        } elseif ($nowTimeStamp - 86400 <= $curTimeStamp && $nowTimeStamp > $curTimeStamp) {
            $text = self::$interval_name['yesterday'];
        } elseif ($nowTimeStamp - 2 * 86400 <= $curTimeStamp && $nowTimeStamp - 86400 > $curTimeStamp) {
            $text = self::$interval_name['before yesterday'];
        } elseif ($nowtime_Y == $Y) {
            $format = $format2;
        } else {
            $format = $format3;
        }

        $format = str_replace('f', $text, $format);
        $format = str_replace('Fs', self::$month_reduced_genitive[$m], $format);
        $format = str_replace('F', self::$month_genitive[$m], $format);
        $format = str_replace('Dw', self::$day_of_week[parent::format('w')], $format);

        $text = parent::format($format);

        if (null !== $prevTimezone) {
            $this->setTimezone($prevTimezone);
        }

        return $text;
    }
}
