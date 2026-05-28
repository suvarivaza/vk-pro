<?php

/**
 * Класс для работы с УРЛ-ами.
 * При работе с ссылками, которые должны отображаться в Punycode, важно чтобы кодировка была установлена правильно.
 * Пример работы:
 *  $url = new \Lib_URL('http://example.com/path/to/object?get=params');  // Содаем объект
 *  $url->path = array( 'another', 'way', 'to', 'target' );               // Меняем путь
 *  $url->query['get'] = 'test';                                          // Меняем параметры
 *  $url->user = 'devel';                                                 // Добавляем пользователя
 *  echo $url;                                                            // Делаем вывод
 *  Получаем: http://devel@example.com/another/way/to/target?get=test
 * Класс содержит методы:
 *  - для удалаения devel-суффикса из домена
 *  - для получения оригинального (немодифицированного) УРЛ-а
 *
 * @property $host string Хост, автоматически конвертируется из Punycode при установке если есть префикс 'xn--', при чтении возвращает чистый host.
 * При приведении к сторке конвертируется в Punicode, если кодировка не ASCII
 */
class Lib_Url
{
    /** @var bool */
    private $_malformed = false;

    /** @var */
    public $encoding;

    /** @var string */
    private $_url;

    /** @var string */
    public $sheme;

    /** @var string */
    private $_host;

    /** @var int */
    public $port;

    /** @var string */
    public $user;

    /** @var string */
    public $pass;

    /** @var array */
    public $path = [];

    /** @var array */
    public $query = [];

    /** @var string */
    public $fragment;

    /** @var \Lib_Punycode */
    private static $_punycoder;

    /**
     * Создает объект.
     * Разбирает строковый $url по частям:
     *  - разбивает путь на части
     *  - разбирает переменные QUERY_STRING
     *
     * @param $url string
     * @param string $encoding
     */
    public function __construct($url, $encoding = 'ASCII')
    {
        $this->encoding = $encoding;
        $this->_url = $url;
        $temp = @\parse_url($url);

        if ($temp) {
            $this->sheme = $temp['scheme'] ?? '';
            $this->host = $temp['host'] ?? '';
            $this->port = $temp['port'] ?? '';
            $this->user = $temp['user'] ?? '';
            $this->pass = $temp['pass'] ?? '';

            if (isset($temp['path'])) {
                $path = explode('/', $temp['path']);

                foreach ($path as $part) {
                    switch ($part) {
                        case '':
                        case '.':
                            continue;
                        case '..':
                            array_pop($this->path);

                            continue;
                        default:
                            array_push($this->path, $part);
                    }
                }
            }

            if (isset($temp['query'])) {
                parse_str($temp['query'], $this->query);
            }
            $this->fragment = $temp['fragment'] ?? '';
        } else {
            $this->_malformed = true;
        }
    }

    /**
     * Проверяет была ли ошибка при разборе URL
     *
     * @return bool
     */
    public function isMalformed()
    {
        return $this->_malformed;
    }

    /**
     * Проверяет пустой путь или нет
     *
     * @return bool
     */
    public function hasEmptyPath()
    {
        return count($this->path) == 0;
    }

    /**
     * Возвращает URL переданный в конструктор
     *
     * @return string
     */
    public function getOrigin()
    {
        return $this->_url;
    }

    /**
     * Приведение к строке
     *
     * @return string
     */
    public function __toString()
    {
        $url = ($this->sheme ? $this->sheme . '://' : '');

        if ($this->user) {
            $url .= $this->user;

            if ($this->pass) {
                $url .= ':' . $this->pass;
            }
            $url .= '@';
        }

        if ($this->_host) {
            if ('ASCII' != $this->encoding) {
                if (null === self::$_punycoder) {
                    self::$_punycoder = new \Lib_Punycode();
                }

                $url .= self::$_punycoder->encode($this->encoding != 'UTF-8' ? iconv($this->encoding, 'UTF-8', $this->_host) : $this->_host);
            } else {
                $url .= $this->_host;
            }

            if ($this->port) {
                $url .= ':' . $this->port;
            }
            $url .= '/';
        }

        if (count($this->path)) {
            $url .= join('/', $this->path);
        }

        if (count($this->query)) {
            $url .= '?' . http_build_query($this->query);
        }

        if ($this->fragment) {
            $url .= '#' . $this->fragment;
        }

        return $url;
    }

    /**
     * Возвращает усеченное имя хоста
     *
     * @param string $subdomain поддомен
     * @param int $offset смещение от поддомена
     *
     * @return string
     */
    public function getSliceDomain($subdomain, $offset = 0)
    {
        $domains = explode('.', $this->_host);

        foreach ($domains as $k => $domain) {
            if ($domain == $subdomain) {
                $domains = array_slice($domains, 0, $k + $offset);
                break;
            }
        }

        return join('.', $domains);
    }

    /**
     * Getter, нужен для доступа к "по-умному" установленным свойствам
     *
     * @param $name
     *
     * @return string
     *
     * @throws Lib_Exception_Logic_Backtraced
     */
    public function __get($name)
    {
        switch ($name) {
            case 'host':
                return $this->_host;
            default:
                throw new \Lib_Exception_Logic_Backtraced('Trying to get unknow property: ' . $name);
        }
    }

    public function __isset($name)
    {
        switch ($name) {
            case 'host':
                return isset($this->_host);
            default:
                throw new \Lib_Exception_Logic_Backtraced('Trying to get unknow property: ' . $name);
        }
    }

    /**
     * Setter, нужен для "умной" установки публичных свойств
     *
     * @param $name
     * @param $value
     *
     * @throws Lib_Exception_Logic_Backtraced
     */
    public function __set($name, $value)
    {
        switch ($name) {
            case 'host':
                if (strncmp($value, 'xn--', 4) === 0) {
                    if (null === self::$_punycoder) {
                        self::$_punycoder = new \Lib_Punycode();
                    }
                    $this->_host = self::$_punycoder->decode($value);

                    if ('ASCII' !== $this->encoding) {
                        if ('UTF-8' != $this->encoding) {
                            $this->_host = iconv('UTF-8', $this->encoding, $this->_host);
                        }
                    } else {
                        $this->encoding = 'UTF-8';
                    }
                } else {
                    $this->_host = $value;
                }
                break;
            default:
                throw new \Lib_Exception_Logic_Backtraced('Trying to set unknow property: ' . $name . ' with value: ' . $value);
        }
    }
}
