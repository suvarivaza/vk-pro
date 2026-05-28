<?php

class Lib_DateTime
{
    public const INV360 = 0.0027777777777778;

    public const PI = 3.1415926535897932384;
    /** Направление смещения для вывода дат */
    public const OFFSET_OUT = 1;
    /** Направление смещения для вооде дат */
    public const OFFSET_IN = -1;

    /**
     * Дни недели
     *
     * @var array
     */
    public static $day_of_week = [
        0 => 'воскресенье',
        1 => 'понедельник',
        2 => 'вторник',
        3 => 'среда',
        4 => 'четверг',
        5 => 'пятница',
        6 => 'суббота',
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
     * Месяцы
     *
     * @var array
     */
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

    /**
     * Месяцы в родительном падеже
     *
     * @var array
     */
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
     * Форматирует дату
     *
     * @static
     *
     * @uses smarty_make_timestamp()
     *
     * @param mixed $string Время timestamp-ом или строкой
     * @param string $format Формат
     * @param string $format2 Формат даты ранее, чем позавчера
     * @param string $format3 Формат даты, ранее года
     *
     * @return string
     */
    public static function SimplyDate($string, $format = '%f %H:%M', $format2 = '%e %F', $format3 = '%e %F %Y')
    {
        if (empty($string)) {
            $time = time();
        } elseif (is_string($string)) {
            $time = strtotime($string);
        } else {
            $time = intval($string);
        }

        $nowtime = time();
        // это timestamp начала дня 00:00:00
        $day_begin_time = mktime(0, 0, 0, idate('m', $nowtime), idate('d', $nowtime), idate('y', $nowtime));
        // это текущий год
        $nowyear = idate('y', $nowtime);

        $year = date('y', $time);
        $mon = date('n', $time);

        $text = '';
        // проверяем разницу в днях
        if ($day_begin_time <= $time && $day_begin_time + 86400 > $time) {
            $text = 'сегодня';
        } elseif ($day_begin_time + 86400 <= $time && $day_begin_time + 2 * 86400 > $time) {
            $text = 'завтра';
        } elseif ($day_begin_time - 86400 <= $time && $day_begin_time > $time) {
            $text = 'вчера';
        } elseif ($day_begin_time - 2 * 86400 <= $time && $day_begin_time - 86400 > $time) {
            $text = 'позавчера';
        } elseif ($nowyear == $year) {
            $format = $format2;
        } else {
            $format = $format3;
        }

        $format = str_replace('%f', $text, $format);
        $format = str_replace('%Fs', self::$month_reduced_genitive[$mon], $format);
        $format = str_replace('%F', self::$month_genitive[$mon], $format);

        return strftime($format, $time);
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
}
