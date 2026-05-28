<?php

class Lib_Text
{
    public const GRS_CMD_CONST = 1;
    public const GRS_CMD_SYMBOL = 2;
    public const GRS_CMD_RANGE = 3;

    public const GRS_SET_ALPHA = 1;
    public const GRS_SET_DIGIT = 2;

    /** @var array */
    public static $RandRules;

    private static $domains = [
        'com', 'net', 'org', 'info', 'biz', 'name', 'aero', 'arpa', 'edu', 'int', 'gov', 'mil', 'coop', 'museum', 'mobi', 'pro', 'tel', 'travel', 'xxx', 'ac', 'ad', 'ae', 'af', 'ag', 'ai', 'al', 'am', 'an', 'ao', 'aq', 'ar', 'as', 'at', 'au', 'aw', 'az', 'ba', 'bb', 'bd', 'be', 'bf', 'bg', 'bh', 'bi', 'bj', 'bm', 'bn', 'bo', 'br', 'bs', 'bt', 'bv', 'bw', 'by', 'bz', 'ca', 'cc', 'cd', 'cf', 'cg', 'ch', 'ci', 'ck', 'cl', 'cm', 'cn', 'co', 'cr', 'cu', 'cv', 'cx', 'cy', 'cz', 'de', 'dj', 'dk', 'dm', 'do', 'dz', 'ec', 'ee', 'eg', 'eh', 'er', 'es', 'et', 'eu', 'fi', 'fj', 'fk', 'fm', 'fo', 'fr', 'ga', 'gd', 'ge', 'gf', 'gg', 'gh', 'gi', 'gl', 'gm', 'gn', 'gp', 'gq', 'gr', 'gs', 'gt', 'gu', 'gw', 'gy', 'hk', 'hm', 'hn', 'hr', 'ht', 'hu', 'id', 'ie', 'il', 'im', 'in', 'io', 'iq', 'ir', 'is', 'it', 'je', 'jm', 'jo', 'jp', 'ke', 'kg', 'kh', 'ki', 'km', 'kn', 'kp', 'kr', 'kw', 'ky', 'kz', 'la', 'lb', 'lc', 'li', 'lk', 'lr', 'ls', 'lt', 'lu', 'lv', 'ly', 'ma', 'mc', 'md', 'mg', 'mh', 'mk', 'ml', 'mm', 'mn', 'mo', 'mp', 'mq', 'mr', 'ms', 'mt', 'mu', 'mv', 'mw', 'mx', 'my', 'mz', 'na', 'nc', 'ne', 'nf', 'ng', 'ni', 'nl', 'no', 'np', 'nr', 'nu', 'nz', 'om', 'pa', 'pe', 'pf', 'pg', 'ph', 'pk', 'pl', 'pm', 'pn', 'pr', 'ps', 'pt', 'pw', 'py', 'qa', 're', 'ro', 'ru', 'рф', 'rw', 'sa', 'sb', 'sc', 'sd', 'se', 'sg', 'sh', 'si', 'sj', 'sk', 'sl', 'sm', 'sn', 'so', 'sr', 'st', 'su', 'sv', 'sy', 'sz', 'tc', 'td', 'tf', 'tg', 'th', 'tj', 'tk', 'tm', 'tn', 'to', 'tp', 'tr', 'tt', 'tv', 'tw', 'tz', 'ua', 'ug', 'uk', 'um', 'us', 'uy', 'uz', 'va', 'vc', 've', 'vg', 'vi', 'vn', 'vu', 'wf', 'ws', 'ye', 'yt', 'yu', 'za', 'zm', 'zw',
    ];

    private static $nul = 'ноль';

    private static $ten = [
        ['', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'],
        ['', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'],
    ];

    private static $a20 = ['десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать'];

    private static $tens = [2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто'];

    private static $hundred = ['', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот'];

    private static $unit = [ // Units
        ['копейка', 'копейки', 'копеек', 1],
        ['рубль', 'рубля', 'рублей', 0],
        ['тысяча', 'тысячи', 'тысяч', 1],
        ['миллион', 'миллиона', 'миллионов', 0],
        ['миллиард', 'милиарда', 'миллиардов', 0],
    ];

    public static function IsString($value)
    {
        if (strlen($value) <= 0) {
            return false;
        }

        return ctype_alnum('' . $value) ? true : false;
    }

    public static function IsAlpha($value)
    {
        if (strlen($value) <= 0) {
            return false;
        }

        return ctype_alpha('' . $value) ? true : false;
    }

    public static function IsAlphaRange($value, $from, $to)
    {
        if (strlen($value) <= 0 || !is_numeric($from) || !is_numeric($to) || $from < 0 || $from > 255 || $to < 0 || $to > 255) {
            return false;
        }

        if ($from > $to) {
            $from += $to;
            $to = $from - $to;
            $from = $from - $to;
        }

        $len = strlen($value);

        for ($i = 0; $i < $len; $i++) {
            $code = ord(substr($value, $i, 1));

            if ($code < $from || $code > $to) {
                return false;
            }
        }

        return true;
    }

    /**
     * @static function IsPhone
     *
     * @param string $phone - номер тел для проверки
     *
     * @return bool - номер или нет
     */
    public static function IsPhone($phone)
    {
        if ($phone == '') {
            return false;
        }

        if (!preg_match("/^[\d]{11}$/i", $phone)) {
            return false;
        }

        return true;
    }

    public static function IsEmail($email)
    {
        if (!is_string($email) || empty($email)) {
            return false;
        }

        if (!preg_match('/^[a-z0-9\.\-_\+]+@[a-z0-9\-_]+\.([a-z0-9\-_]+\.)*?[a-z]+$/is', $email)) {
            return false;
        }

        return true;
    }

    //2do: Переписать IsUrl

    /**
     * @static function IsUrl
     * Заглушка. Проверка введенного пользователем url-адреса.
     *
     * @param string $url - УРЛ для проверки
     *
     * @return bool - валидная ссылка или нет
     */
    public static function IsUrl($url)
    {
        $url = trim($url);

        if (!preg_match('@^http://@', $url)) {
            $url = 'http://' . $url;
        }

        $up = parse_url($url);

        if (!$up || !$up['scheme'] || !$up['host']) {
            return false;
        }

        if (!(($up['scheme'] == 'http') || ($up['scheme'] == 'https') || ($up['scheme'] == 'ftp'))) {
            return false;
        }

        // если в домене есть что-либо, кроме разрешенных символов - ...
        // Список символов отсюда http://stackoverflow.com/questions/1547899/which-characters-make-a-url-invalid
        if (preg_match('@[^A-Za-zА-Яа-яёЁ0-9\-\._~:/\?#\[\]\@!$&\'\(\)\*\+,;=%]@', $up['host'])) {
            return false;
        }

        return true;
    }

    /**
     * Обрезает строку до длины $length.
     *
     * @param string $string Строка для обрезания.
     * @param int $length Длина, до которой будет обрезаться строка.
     * @param string $etc Добавляется в конце результата.
     * @param bool $break_words Обрезать на полуслове или нет.
     * @param bool $middle По середине или нет.
     *
     * @throws Lib_Exception_InvalidArgument
     *
     * @return mixed|string - Готовая строка или false в случае неуспешности операции
     */
    public static function Truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false)
    {
        if (is_null($string)) {
            return '';
        }

        if (!is_string($string)) {
            throw new \Lib_Exception_InvalidArgument(
                '$string must be a string.'
            );
        }

        if (!is_string($etc)) {
            throw new \Lib_Exception_InvalidArgument(
                '$etc must be a string.'
            );
        }

        if (!is_bool($break_words)) {
            throw new \Lib_Exception_InvalidArgument(
                '$break_words must be a boolean.'
            );
        }

        if (!is_bool($middle)) {
            throw new \Lib_Exception_InvalidArgument(
                '$middle must be a boolean.'
            );
        }

        if ($length == 0) {
            return '';
        }

        $string = str_replace(',', ', ', $string);
        $string = str_replace('  ', ' ', $string);

        if (strlen($string) > $length) {
            $length -= strlen($etc);

            if (!$break_words && !$middle) {
                $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length + 1));
            }

            if (!$middle) {
                return substr($string, 0, $length) . $etc;
            } else {
                return substr($string, 0, $length / 2) . $etc . substr($string, -$length / 2);
            }
        } else {
            return $string;
        }
    }

    /**
     * @static
     *
     * @param string $text текст, который надо форматировать
     * @param int $text_before длина строки перед текстом
     * @param int $text_after длина строки после текста
     * @param int $line количество строк
     * @param int $len длина каждой строки
     * @param int $w_len максимальная длина слова (не должна быть больше длины строки)
     * @param string $add строка добавляется если текст был обрезан
     *
     * @return string отформатированный по строкам текст
     */
    public static function ScrapText($text = '', $text_before = 0, $text_after = 0, $line = 1, $len = 30, $w_len = 20, $add = '...')
    {
        $ar = explode(' ', $text, 20);

        if (count($ar) == 1) {
            if (strlen($ar[0]) > $w_len) {
                $result = substr($ar[0], 0, $w_len) . $add;
            } else {
                $result = $ar[0];
            }
        } else {
            $line_c = 1;
            $cur_line = '';
            $result = '';

            while (list($k, $v) = each($ar)) {
                if (strlen($v) > $w_len) {
                    $v = substr($v, 0, $w_len) . $add;
                }

                if ($line_c == 1) {
                    $text_add = $text_before;
                } elseif ($line_c == $line) {
                    $text_add = $text_after;
                } else {
                    $text_add = 0;
                }

                if ((strlen($cur_line) + strlen($v) + $text_add) > $len) {
                    $result .= ($result ? "\n" : '') . $cur_line;
                    $cur_line = $v . ' ';
                    $line_c++;
                } else {
                    $cur_line .= $v . ' ';
                }

                if ($line < $line_c) {
                    break;
                }
            }

            if ($line >= $line_c && $cur_line) {
                $result .= ($result ? "\n" : '') . $cur_line;
            } elseif ($line < $line_c) {
                $result .= $add;
            }
        }

        return $result;
    }

    /**
     * @static
     * Очистка от повторяющихся пробелов и ентеров
     *
     * @param string $text - строка для чистки
     * @param int $max
     *
     * @return mixed
     */
    public static function ScrapSpaces($text, $max = 5)
    {
        $text = preg_replace('@(<br[^>]*>(\s*)){' . $max . ',}@i', '<br />', $text);
        $text = preg_replace('@(\s){' . $max . ',}@', ' ', $text);

        return $text;
    }

    public static function TranslitEng($str)
    {
        $tr = [
            'CH' => 'ч', 'SH' => 'ш', 'SCH' => 'щ', 'Q' => 'кью', 'W' => 'в', 'E' => 'е', 'R' => 'р',
            'T' => 'т', 'Y' => 'игрек', 'U' => 'у', 'I' => 'и', 'O' => 'о', 'P' => 'п', 'A' => 'а',
            'S' => 'с', 'D' => 'д', 'F' => 'ф', 'G' => 'джи', 'H' => 'эйч', 'J' => 'джей', 'K' => 'к',
            'L' => 'л', 'Z' => 'з', 'X' => 'х', 'C' => 'с', 'V' => 'в', 'B' => 'б', 'N' => 'н', 'M' => 'м',
        ];

        return strtr($str, $tr);
    }

    public static function ClearNumber($str)
    {
        $tr = [
            'Q' => '', 'W' => '', 'E' => '', 'R' => '',
            'T' => '', '}' => '', ';' => '', 'V' => '', '  ' => ' ',
            'u' => '', 'f' => '', 'x' => '',
            'Y' => '', '[' => '', 'F' => '', "'" => '', 'B' => '',
            'q' => '', 'i' => '', 'g' => '', 'c' => '',
            'U' => '', ']' => '', 'G' => '', ':' => '', 'N' => '',
            'w' => '', 'o' => '', 'h' => '', 'v' => '',
            'I' => '', '|' => '', 'H' => '', '"' => '',
            'e' => '', 'p' => '', 'j' => '', 'b' => '',
            'O' => '', 'A' => '', 'J' => '', 'Z' => '', 'M' => '',
            'r' => '', 'a' => '', 'k' => '', 'n' => '',
            'P' => '', 'S' => '', 'K' => '', 'X' => '', ',' => '',
            't' => '', 's' => '', 'l' => '', 'm' => '',
            '{' => '', 'D' => '', 'L' => '', 'C' => '', '.' => '',
            'y' => '', 'd' => '', 'z' => '',
            '(' => '', ')' => '', '-' => '', '_' => '', '/' => '', '\\' => '',
            '*' => '', '=' => '', '>' => '', '<' => '', '!' => '', '@' => '', '$' => '',
            ' ' => '', '—' => '', '+' => '', '№' => '', '~' => '', '&' => '', ' ' => '',
        ];
        strtolower($str);

        return strtr($str, $tr);
    }

    public static function ClearNumberXls($str)
    {
        $tr = [
            'Q' => '', 'W' => '', 'E' => '', 'R' => '',
            'T' => '', '}' => '', ';' => '', 'V' => '', '  ' => ' ',
            'u' => '', 'f' => '', 'x' => '',
            'Y' => '', '[' => '', 'F' => '', "'" => '', 'B' => '',
            'q' => '', 'i' => '', 'g' => '', 'c' => '',
            'U' => '', ']' => '', 'G' => '', ':' => '', 'N' => '',
            'w' => '', 'o' => '', 'h' => '', 'v' => '',
            'I' => '', '|' => '', 'H' => '', '"' => '',
            'e' => '', 'p' => '', 'j' => '', 'b' => '',
            'O' => '', 'A' => '', 'J' => '', 'Z' => '', 'M' => '',
            'r' => '', 'a' => '', 'k' => '', 'n' => '',
            'P' => '', 'S' => '', 'K' => '', 'X' => '',
            't' => '', 's' => '', 'l' => '', 'm' => '',
            '{' => '', 'D' => '', 'L' => '', 'C' => '',
            'y' => '', 'd' => '', 'z' => '',
            '(' => '', ')' => '', '-' => '', '_' => '', '/' => '', '\\' => '',
            '*' => '', '=' => '', '>' => '', '<' => '', '!' => '', '@' => '', '$' => '',
            ' ' => '', '—' => '', '+' => '', '№' => '', '~' => '', '&' => '', ' ' => '',
        ];
        strtolower($str);

        return strtr($str, $tr);
    }

    public static function Translit($str)
    {
        $tr = [
            'А' => 'a', 'Б' => 'b', 'В' => 'v', 'Г' => 'g',
            'Д' => 'd', 'Е' => 'e', 'Ж' => 'j', 'З' => 'z', 'И' => 'i',
            'Й' => 'y', 'К' => 'k', 'Л' => 'l', 'М' => 'm', 'Н' => 'n',
            'О' => 'o', 'П' => 'p', 'Р' => 'r', 'С' => 's', 'Т' => 't',
            'У' => 'u', 'Ф' => 'f', 'Х' => 'h', 'Ц' => 'ts', 'Ч' => 'ch',
            'Ш' => 'sh', 'Щ' => 'sch', 'Ъ' => '', 'Ы' => 'yi', 'Ь' => '',
            'Э' => 'e', 'Ю' => 'yu', 'Я' => 'ya', 'а' => 'a', 'б' => 'b',
            'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ж' => 'j',
            'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l',
            'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h',
            'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ъ' => 'y',
            'ы' => 'yi', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            ' ' => '-', '.' => '', '/' => '-', ':' => '-', '$' => '', '\\' => '-',
            "\n" => '-', PHP_EOL => '-', '&nbsp;' => '_', ',' => '', ' ' => '-',
        ];
        strtolower($str);

        return strtr($str, $tr);
    }

    public static function TranslitSwitch($str)
    {
        $tr = [
            'А' => 'F', 'Б' => '<', 'В' => 'D', 'Г' => 'U',
            'Д' => 'L', 'Е' => 'T', 'Ж' => ':', 'З' => 'P', 'И' => 'B',
            'Й' => 'Q', 'К' => 'R', 'Л' => 'K', 'М' => 'V', 'Н' => 'Y',
            'О' => 'J', 'П' => 'G', 'Р' => 'H', 'С' => 'C', 'Т' => 'N',
            'У' => 'E', 'Ф' => 'A', 'Х' => '{', 'Ц' => 'W', 'Ч' => 'X',
            'Ш' => 'I', 'Щ' => 'O', 'Ъ' => '}', 'Ы' => 'S', 'Ь' => 'M',
            'Э' => '"', 'Ю' => '>', 'Я' => 'Z', 'а' => 'f', 'б' => ',',
            'в' => 'd', 'г' => 'u', 'д' => 'l', 'е' => 't', 'ж' => ';',
            'з' => 'p', 'и' => 'b', 'й' => 'q', 'к' => 'r', 'л' => 'k',
            'м' => 'v', 'н' => 'y', 'о' => 'j', 'п' => 'g', 'р' => 'h',
            'с' => 'c', 'т' => 'n', 'у' => 'e', 'ф' => 'a', 'х' => '[',
            'ц' => 'w', 'ч' => 'x', 'ш' => 'i', 'щ' => 'o', 'ъ' => ']',
            'ы' => 's', 'ь' => 'm', 'э' => "'", 'ю' => '.', 'я' => 'z',
        ];

        return strtr($str, $tr);
    }

    public static function TranslitPseudo($str)
    {
        $tr = [
            'А' => 'F', 'Б' => '<', 'В' => 'D', 'Г' => 'U',
            'Д' => 'L', 'Е' => 'T', 'Ж' => ':', 'З' => 'P', 'И' => 'B',
            'Й' => 'Q', 'К' => 'R', 'Л' => 'K', 'М' => 'V', 'Н' => 'Y',
            'О' => 'J', 'П' => 'G', 'Р' => 'H', 'С' => 'C', 'Т' => 'N',
            'У' => 'E', 'Ф' => 'A', 'Х' => '{', 'Ц' => 'W', 'Ч' => 'X',
            'Ш' => 'I', 'Щ' => 'O', 'Ъ' => '}', 'Ы' => 'S', 'Ь' => 'M',
            'Э' => '"', 'Ю' => '>', 'Я' => 'Z', 'а' => 'f', 'б' => ',',
            'в' => 'd', 'г' => 'u', 'д' => 'l', 'е' => 't', 'ж' => ';',
            'з' => 'p', 'и' => 'b', 'й' => 'q', 'к' => 'r', 'л' => 'k',
            'м' => 'v', 'н' => 'y', 'о' => 'j', 'п' => 'g', 'р' => 'h',
            'с' => 'c', 'т' => 'n', 'у' => 'e', 'ф' => 'a', 'х' => '[',
            'ц' => 'w', 'ч' => 'x', 'ш' => 'i', 'щ' => 'o', 'ъ' => ']',
            'ы' => 's', 'ь' => 'm', 'э' => "'", 'ю' => '.', 'я' => 'z',
            ' ' => '', '.' => '', '/' => '', ':' => '', '-' => '',
            '>' => '', '<' => '', '=' => '', '!' => '', '  ' => ' ',
            '(' => '', ')' => '', '[' => '', ']' => '', "'" => '',
            '$' => '',
        ];
        strtolower($str);

        return strtr($str, $tr);
    }

    public static function Replace($str)
    {
        $tr = [
            '.' => '', '/' => '', ':' => '', '-' => '',
            '>' => '', '<' => '', '=' => '', '!' => '', '  ' => ' ',
            '(' => '', ')' => '', '[' => '', ']' => '',
        ];
        strtolower($str);

        return strtr($str, $tr);
    }

    public static function TextClear($str)
    {
        $tr = [
            '(' => '', ')' => '', '-' => '', '_' => '', '/' => '', '\\' => '',
            '*' => '', '=' => '', '>' => '', '<' => '', '!' => '', '@' => '', '$' => '',
            ' ' => '', '—' => '', '+' => '', '№' => '', '~' => '', '&' => '', '–' => '',
            ',' => '', '.' => '',
        ];

        return trim(strtr($str, $tr));
    }

    public static function TextClearRussian($str)
    {
        $tr = [
            'Q' => '', 'W' => '', 'E' => '', 'R' => '',
            'T' => '', '}' => '', ';' => '', 'V' => '', '  ' => ' ',
            'u' => '', 'f' => '', 'x' => '', '2' => '', '9' => '',
            'Y' => '', '[' => '', 'F' => '', "'" => '', 'B' => '',
            'q' => '', 'i' => '', 'g' => '', 'c' => '', '3' => '', '0' => '',
            'U' => '', ']' => '', 'G' => '', ':' => '', 'N' => '',
            'w' => '', 'o' => '', 'h' => '', 'v' => '', '4' => '',
            'I' => '', '|' => '', 'H' => '', '"' => '',
            'e' => '', 'p' => '', 'j' => '', 'b' => '', '5' => '',
            'O' => '', 'A' => '', 'J' => '', 'Z' => '', 'M' => '',
            'r' => '', 'a' => '', 'k' => '', 'n' => '', '6' => '',
            'P' => '', 'S' => '', 'K' => '', 'X' => '', ',' => '',
            't' => '', 's' => '', 'l' => '', 'm' => '', '7' => '',
            '{' => '', 'D' => '', 'L' => '', 'C' => '', '.' => '',
            'y' => '', 'd' => '', 'z' => '', '1' => '', '8' => '',
            '(' => '', ')' => '', '-' => '', '_' => '', '/' => '', '\\' => '',
            '*' => '', '=' => '', '>' => '', '<' => '', '!' => '', '@' => '', '$' => '',
            ' ' => '', '—' => '', '+' => '', '№' => '', '~' => '', '&' => '', '–' => '',
        ];
        strtolower($str);

        return trim(strtr($str, $tr));
    }

    /**
     * Возвращает сумму прописью
     *
     * @uses Lib_Text::_morph(...)
     */
    public static function NumberToString($num)
    {
        list($rub, $kop) = explode(',', sprintf('%015.2f', floatval($num)));
        $out = [];

        if (intval($rub) > 0) {
            foreach (str_split($rub, 3) as $uk => $v) {
                if (!intval($v)) {
                    continue;
                }
                $uk = sizeof(self::$unit) - $uk - 1; // unit key
                $gender = self::$unit[$uk][3];
                list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
                // mega-logic
                $out[] = self::$hundred[$i1]; // 1xx-9xx
                if ($i2 > 1) {
                    $out[] = self::$tens[$i2] . ' ' . self::$ten[$gender][$i3]; // 20-99
                } else {
                    $out[] = $i2 > 0 ? self::$a20[$i3] : self::$ten[$gender][$i3];
                } // 10-19 | 1-9
                if ($uk > 1) {
                    $out[] = self::_morph($v, self::$unit[$uk][0], self::$unit[$uk][1], self::$unit[$uk][2]);
                }
            }
        } else {
            $out[] = self::$nul;
        }

        $out[] = self::_morph(intval($rub), self::$unit[1][0], self::$unit[1][1], self::$unit[1][2]);
        $out[] = $kop . ' ' . self::_morph($kop, self::$unit[0][0], self::$unit[0][1], self::$unit[0][2]);

        return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
    }

    /**
     * Склоняем словоформу
     *
     * @ author runcore
     */
    private static function _morph($n, $f1, $f2, $f5)
    {
        $n = abs(intval($n)) % 100;

        if ($n > 10 && $n < 20) {
            return $f5;
        }
        $n = $n % 10;

        if ($n > 1 && $n < 5) {
            return $f2;
        }

        if ($n == 1) {
            return $f1;
        }

        return $f5;
    }

    /**
     * @static
     *
     * @param $var
     *
     * @return array|string
     */
    public static function Escape($var)
    {
        if (is_array($var)) {
            $ret = [];

            foreach ($var as $k => $v) {
                $ret[$k] = self::Escape($v);
            }
        } else {
            $ret = addslashes($var);
        }

        return $ret;
    }

    /**
     * Escape unescaped single quotes.
     * Из Smarty modifier.escape.php
     *
     * @static
     *
     * @param string $string
     *
     * @return string
     */
    public static function EscapeQuotes($string)
    {
        return preg_replace("%(?<!\\\\)'%", "\\'", $string);
    }

    /**
     * From Smarty escape modifier plugin.
     * (modifier.escape.php)
     *
     * @static
     *
     * @param $string
     *
     * @return mixed
     */
    public static function EscapeTags($string)
    {
        return preg_replace(['@<@', '@>@'], ['&lt;', '&gt;'], $string);
    }

    /**
     * @static function Url
     *
     * @param string $string
     * @param bool $scheme
     *
     * @return mixed - получение урла
     */
    public static function Url($string, $scheme = true)
    {
        $string = trim($string);

        if ($scheme) {
            return preg_replace("~^(([^:]+)://)?(www\.)?([a-zа-я0-9\._/-]+).*~ie",
                "(('$2' == '') ? 'http://' : '$1') . '$3$4'", $string);
        }

        return preg_replace("~^(([^:]+)://)?(www\.)?([a-zа-я0-9\._/-]+).*~ie",
            "'$3$4'", $string);
    }

    public static function Word4NumberNewReturn($number, $after)
    {
        $cases = [2, 0, 1, 1, 1, 2];

        return $number . ' ' . $after[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }

    public static function Word4NumberNew($number, $after)
    {
        $cases = [2, 0, 1, 1, 1, 2];
        echo $number . ' ' . $after[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }

    public static function Word4NumberClear($number, $after)
    {
        $cases = [2, 0, 1, 1, 1, 2];

        return $after[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }

    /**
     * @static Выбрать склонение слова по числу
     *
     * @param  int $num - число
     * @param  string $f - "61 чайник"
     * @param  string $s - "24 чайника"
     * @param  string $t - "7 чайников"
     *
     * @return string $f | $s | $t
     */
    public static function Word4Number($num, $f, $s, $t)
    {
        $ps = [$f, $s, $t];

        $num = intval($num);

        return $ps[$num % 10 == 1 && $num % 100 != 11 ? 0 : $num % 10 >= 2 && $num % 10 <= 4 && ($num % 100 < 10 || $num % 100 >= 20) ? 1 : 2];
    }

    /**
     * @static function WithHref
     *
     * @param string $string - строка для проверки
     * @param bool $dont_reduce
     *
     * @return mixed
     */
    public static function WithHref($string, $dont_reduce = false)
    {
        // старый вариант
        //	return preg_replace("/(((http:\/\/)|(www\.))([^<>]+?))(<|>|&lt|&gt|\s|$)/e", "'<noindex><a href=\"http://$4$5\" target=\"_blank\">'.__ReduceUrl__('$1').'</a></noindex> $6'", $string);
        // новый, не трогает ссылки в тагах
        if ($dont_reduce === true) {
            return preg_replace(
                "/(?(?=\<)([^>]+>)|(((http:\/\/)|(www\.))([\w\.,_\-\@%;:~\/\?\=\&\+#]+[\w\-%\/\?\=\&\+#]{1})))/e",
                // Кто закоментил эту строку и написал следующую? Объясните мне, с какого перепугу это было сделано?! Фарид.
                //"/(?(?=\<)([^>]+>)|(((http:\/\/)|(www\.))([^\s]+)))/e",
                '"$6"?"<noindex><a href=\\"http://$5$6\\" target=\\"_blank\\" rel=\\"nofollow\\">$2</a></noindex>":"$1"',
                $string);
        } else {
            return preg_replace(
                "/(?(?=\<)([^>]+>)|(((http:\/\/)|(www\.))([\w\._\-\@%\/\?\=\&\+#]+[\w\-%\/\?\=\&\+#]{1})))/e",
                //"/(?(?=\<)([^>]+>)|(((http:\/\/)|(www\.))([^\s]+)))/e",
                '"$6"?"<noindex><a href=\\"http://$5$6\\" target=\\"_blank\\" rel=\\"nofollow\\">".self::__ReduceUrl__("$2")."</a></noindex>":"$1"',
                $string);
        }
    }

    /**
     * Возвращает случайную строку из $len символов
     *
     * @static
     *
     * @param int $len
     *
     * @return string
     */
    public static function RandomString($len = 32)
    {
        $str = '';

        for ($i = 1; $i <= $len; $i++) {
            $r = round(rand(0, 61)) + 48;

            if ($r > 57) {
                $r = $r + 7;
            }

            if ($r > 90) {
                $r += 6;
            }
            $str .= chr($r);
        }

        return $str;
    }

    /**
     * Возвращает случайную строку по правилам
     *
     * @static
     *
     * @param $rule
     *
     * @return string
     */
    public static function RandomStringByRule($rule)
    {
        $buf = '';
        // алгоритм пока один
        foreach ($rule as $v) {
            switch ($v['cmd']) {
                case self::GRS_CMD_CONST:
                    $buf .= $v['tset'];
                    break;
                case self::GRS_CMD_SYMBOL:
                    if ($v['max'] != $v['min']) {
                        $cnt = rand($v['min'], $v['max']);
                    } else {
                        $cnt = $v['min'];
                    }

                    for ($j = 0; $j < $cnt; $j++) {
                        $buf .= substr($v['tset'], rand(0, strlen($v['tset']) - 1), 1);
                    }
                    break;
                case self::GRS_CMD_RANGE:
                    if ($v['max'] != $v['min']) {
                        $cnt = rand($v['min'], $v['max']);
                    } else {
                        $cnt = $v['min'];
                    }
                    $scnt = -1;

                    if ($v['set'] & self::GRS_SET_ALPHA) {
                        $scnt += 26;
                    }

                    if ($v['set'] & self::GRS_SET_DIGIT) {
                        $scnt += 10;
                    }

                    if ($scnt == 0 || $cnt == 0) {
                        break;
                    }

                    for ($j = 0; $j < $cnt; $j++) {
                        $code = rand(0, $scnt);
                        $code += 48; // двинули до числа
                        if (!($v['set'] & self::GRS_SET_DIGIT) && $v['set'] & self::GRS_SET_ALPHA) {
                            $code += 49;
                        } elseif ($v['set'] & self::GRS_SET_DIGIT && $v['set'] & self::GRS_SET_ALPHA && $code > 57) {
                            $code += 39;
                        }
                        $buf .= chr($code);
                    }
                    break;
            }
        }

        return $buf;
    }

    /**
     * @static  function __ReduceUrl__
     *
     * @param string $full_url - URL
     *
     * @return string - ?????? ????
     */
    public static function __ReduceUrl__($full_url)
    {
        // if length of link more then threshold, then return only domain name
        if (strlen($full_url) > 30) {
            preg_match("/^((http:\/\/)|(www\.))([^\/]+)/i", $full_url, $matches);
            $full_url = $matches[0];
        }

        return $full_url;
    }

    /**
     * Форматирует телефон
     *
     * @static
     *
     * @param $phone
     *
     * @return string
     */
    public static function FormatPhone($phone)
    {
        if (!preg_match('/(?:8|\+7)\s*\((\d+)\)\s*([\d-]+)(?:\s*доб\.\s*(\d+))?/', $phone, $matches)) {
            return $phone;
        }

        $matches[2] = str_replace('-', '', $matches[2]);

        if (strlen($matches[2]) == 7) {
            $matches[2] = substr($matches[2], 0, 3) . '-' . substr($matches[2], 3, 2) . '-' . substr($matches[2], 5, 2);
        } elseif (strlen($matches[2]) == 6) {
            $matches[2] = substr($matches[2], 0, 2) . '-' . substr($matches[2], 2, 2) . '-' . substr($matches[2], 4, 2);
        } elseif (strlen($matches[2]) == 5) {
            $matches[2] = substr($matches[2], 0, 1) . '-' . substr($matches[2], 1, 2) . '-' . substr($matches[2], 3, 2);
        }

        return '8 (' . $matches[1] . ') ' . $matches[2] . ($matches[3] ? ' доб. ' . $matches[3] : '');
    }

    /**
     * Разбивает телефон на составные части: код, номер и добавочный
     *
     * @static
     *
     * @param $phone
     *
     * @return array|null
     */
    public static function ExplodePhone($phone)
    {
        if (preg_match('/(?:8|\+7)\s*\((\d+)\)\s*([\d-]+)(?:\s*доб\.\s*(\d+))?/', $phone, $matches)) {
            return [
                'Code' => $matches[1],
                'Number' => str_replace('-', '', $matches[2]),
                'Extra' => $matches[3] ?? '',
            ];
        } elseif (preg_match('/([\d-]+)/', $phone, $matches)) {
            return [
                'Code' => '',
                'Number' => str_replace('-', '', $matches[1]),
                'Extra' => '',
            ];
        } else {
            return [
            'Code' => '',
            'Number' => '',
            'Extra' => '',
        ];
        }
    }

    /**
     * Собирает телефон из составных частей: код, номер и добавочный
     *
     * @static
     *
     * @param $phone
     *
     * @return string
     */
    public static function ImplodePhone($phone)
    {
        if (empty($phone['Code']) || !is_string($phone['Code'])) {
            return '';
        }

        if (empty($phone['Number']) || !is_string($phone['Number'])) {
            return '';
        }

        if (!empty($phone['Extra']) && !is_string($phone['Extra'])) {
            return '';
        }

        $phone['Number'] = trim(str_replace('-', '', $phone['Number']));

        if (strlen($phone['Number']) == 7) {
            $phone['Number'] = substr($phone['Number'], 0, 3) . '-' . substr($phone['Number'], 3, 2) . '-' . substr($phone['Number'], 5, 2);
        } elseif (strlen($phone['Number']) == 6) {
            $phone['Number'] = substr($phone['Number'], 0, 2) . '-' . substr($phone['Number'], 2, 2) . '-' . substr($phone['Number'], 4, 2);
        } elseif (strlen($phone['Number']) == 5) {
            $phone['Number'] = substr($phone['Number'], 0, 1) . '-' . substr($phone['Number'], 1, 2) . '-' . substr($phone['Number'], 3, 2);
        }

        return '8 (' . trim($phone['Code']) . ') ' . $phone['Number'] . (trim($phone['Extra']) ? ' доб. ' . trim($phone['Extra']) : '');
    }

    /**
     * Проверяет наличие урла в тексте
     *
     * @static
     *
     * @param string $text
     *
     * @return bool
     */
    public static function IsUrlIncluded($text)
    {
        return (bool) preg_match('@\.(' . implode('|', self::$domains) . ')@', $text);
    }
}
