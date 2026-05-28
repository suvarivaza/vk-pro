<?php

/**
 * Управление заголовками страницы и путями для хлебных крошек.
 *
 * @property string $Title
 * @property string $Keywords
 * @property string $Description
 * @property string $Path
 * @property array $Styles
 * @property array $Scripts
 * @property string $Head
 */
class Lib_Html_Titles
{
    /**
     * @name Константы , определяющие место добавления элемента (битовая маска)
     * @{
     */
    /** Добавить в заголовок страницы */
    public const APPEND_TITLE = 1;
    /** Добавить в ключевые слова */
    public const APPEND_KEYWORDS = 2;
    /** Добавить в описание страницы */
    public const APPEND_DESCRIPTION = 4;
    /** Добавить везде */
    public const APPEND_ALL = 7;
    /** @} */

    /** @var array $default_script_attrs Атрибуты тега <script> по умолчанию */
    private static $default_script_attrs = [
        'type' => 'text/javascript',
        'language' => 'javascript',
        'src' => '',
        'charset' => 'utf-8',
    ];

    private static $version_key = null; // Ключ версии статики

    // TODO Ограничить доступ
    private $_tags = [];

    private $_titleDelimiter; // Разделитель заголовков

    /**
     * @param bool $default_titles Добавлять ли заголовки по умолчанию
     * @param string $delimiter Разделитель заголовка страницы
     */
    public function __construct($default_titles = true, $delimiter = ' - ')
    {
        $this->_titleDelimiter = $delimiter;
    }

    /**
     * Добавка тега в заголовок.
     *
     * @param array|string $tag Тег
     * @param array $attr Атрибуты тега
     * @param string|array $values Значения атрибутов тега
     * @param bool $before Добавить перед всеми тегами
     *
     * @throws Lib_Exception_InvalidArgument_Type
     * @throws Lib_Exception_InvalidArgument
     */
    public function add($tag, $attr = [], $values = '', $before = false)
    {
        if (empty($tag)) {
            throw new Lib_Exception_InvalidArgument('Empty header tag');
        }

        if (false === is_array($tag)) {
            $this->_add([$tag], [$attr], [$values], $before);
        } else {
            $this->_add($tag, $attr, $values, $before);
        }
    }

    private function _add(array $tags, $attr, $values, $before)
    {
        foreach ($tags as $k => $tag) {
            $tag = strtolower($tag);

            if (false === isset($this->_tags[$tag])) {
                $this->_tags[$tag] = [];
            }

            $value = is_array($values) ? $values[$k] : (string) $values;

            switch ($tag) {
                case 'script':

                    if (is_string($attr[$k])) {
                        $attr[$k] = ['src' => $attr[$k]];
                    } elseif (!is_array($attr[$k])) {
                        continue;
                    }

                    $attr[$k] = array_merge(self::$default_script_attrs, $attr[$k]);
                    $attr[$k]['cdata'] = $value;

                    $this->_tags[$tag][$attr[$k]['src']] = $attr[$k];
                    $this->_tags['scripts'] = &$this->_tags[$tag];
                    break;

                case 'link':
                    $attr[$k]['cdata'] = $value;

                    if (in_array($attr[$k]['rel'], ['icon', 'shortcut icon'])) {
                        $this->_tags['link'][strtolower($attr[$k]['rel'])] = $attr[$k];
                    } else {
                        $this->_tags['link'][strtolower($attr[$k]['rel']) . $attr[$k]['href']] = $attr[$k];
                    }

                    break;

                case 'meta':
                    $attr[$k]['cdata'] = $value;

                    $this->_tags[$tag][strtolower(implode(',', $attr[$k]))] = $attr[$k];
                    break;

                case 'path':
                    $attr[$k] = (is_array($attr[$k])) ? $attr[$k]['link'] : $attr[$k];

                    if (true !== $before) {
                        $this->_tags[$tag][] = [
                            'name' => $value,
                            'link' => $attr[$k],
                        ];
                    } else {
                        array_unshift($this->_tags[$tag], [
                            'name' => $value,
                            'link' => $attr[$k],
                        ]);
                    }
                    break;

                case 'title':
                case 'keywords':
                case 'description':
                    if (true !== $before) {
                        $this->_tags[$tag][] = $value;
                    } else {
                        array_unshift($this->_tags[$tag], $value);
                    }
                    break;

                default:
                    $attr[$k] = (array) $attr[$k];
                    $attr[$k]['cdata'] = $value;

                    if (true !== $before) {
                        $this->_tags[$tag][] = $attr[$k];
                    } else {
                        array_unshift($this->_tags[$tag], $attr[$k]);
                    }
            }
        }

        return true;
    }

    /**
     * Добавка к заголовку страницы в конец.
     *
     * @param string $value
     * @param null $to
     */
    public function append($value, $to = null)
    {
        if ($to === null) {
            $to = self::APPEND_TITLE;
        }

        $value = strip_tags($value);

        if ($to & self::APPEND_TITLE) {
            $this->_add(['title'], [], [$value], false);
        }

        if ($to & self::APPEND_KEYWORDS) {
            $this->_add(['keywords'], [], [$value], false);
        }

        if ($to & self::APPEND_DESCRIPTION) {
            $this->_add(['description'], [], [$value], false);
        }
    }

    /**
     * @param string $value
     * @param null $to
     */
    public function appendBefore($value, $to = null)
    {
        if ($to === null) {
            $to = self::APPEND_TITLE;
        }

        if ($to & self::APPEND_TITLE) {
            $this->_add(['title'], [], [$value], true);
        }

        if ($to & self::APPEND_KEYWORDS) {
            $this->_add(['keywords'], [], [$value], true);
        }

        if ($to & self::APPEND_DESCRIPTION) {
            $this->_add(['description'], [], [$value], true);
        }
    }

    public function delStyles()
    {
        $this->_tags['link'] = [];
    }

    public function delScript($name)
    {
        unset($this->_tags['script'][$name]);
    }

    /**
     * Добавить скрипт
     *
     * @param string $value
     *
     * @throws Lib_Exception_InvalidArgument_Type
     * @throws Lib_Exception_InvalidArgument
     */
    public function addScript($value)
    {
        if (false === is_string($value)) {
            throw new Lib_Exception_InvalidArgument_Type($value, 'string');
        }

        if (empty($value)) {
            throw new Lib_Exception_InvalidArgument('Empty script value');
        }

        $this->add('script', $value);
    }

    /**
     * Добавить массив скриптов
     *
     * @param array $values
     *
     * @throws Lib_Exception_InvalidArgument_Type
     * @throws Lib_Exception_InvalidArgument
     */
    public function addScripts(array $values)
    {
        foreach ($values as $v) {
            $this->addScript($v);
        }
    }

    /**
     * Добавить стиль
     *
     * @param string $value
     * @param string $media
     *
     * @throws Lib_Exception_InvalidArgument_Type
     * @throws Lib_Exception_InvalidArgument
     */
    public function addStyle($value, $media = 'all')
    {
        if (false === is_string($value)) {
            throw new Lib_Exception_InvalidArgument_Type($value, 'string');
        }

        if (empty($value)) {
            throw new Lib_Exception_InvalidArgument('Empty style value');
        }

        $attr = [
            'rel' => 'stylesheet',
            'type' => 'text/css',
            'href' => $value,
        ];

        if ('all' !== strtolower($media)) {
            $attr['media'] = $media;
        }

        $this->add('link', $attr);
    }

    /**
     * Добавить массив стилей.
     *
     * @param array $values
     *
     * @throws Lib_Exception_InvalidArgument_Type
     * @throws Lib_Exception_InvalidArgument
     */
    public function addStyles(array $values)
    {
        foreach ($values as $v) {
            $this->addStyle($v);
        }
    }

    /**
     * Добавить в конец пути
     *
     * @param string $name Элемент
     * @param string $link Ссылка
     */
    public function addPath($name, $link = null)
    {
        $this->add('path', $link, $name);
    }

    /**
     * Добавить в начало пути
     *
     * @param string $name Элемент
     * @param string $link Ссылка
     */
    public function addPathBefore($name, $link)
    {
        $this->add('path', $link, $name, true);
    }

    /**
     * Подготовка html-кода для тега
     *
     * @param string $name Имя тега
     * @param bool $close Требуется ли закрывать тег
     *
     * @return string
     */
    protected function prepareHTML($name, $close = false)
    {
        $version_key = '';

        if ($name == 'link' || $name == 'script') {
            $version_key = self::_getVersionKey();
        }

        $result = '';

        if (isset($this->_tags[$name]) && is_array($this->_tags[$name]) && sizeof($this->_tags[$name])) {
            foreach ($this->_tags[$name] as $data) {
                $result .= "\n<{$name}";

                foreach ($data as $k => $v) {
                    // если это стиль или скрипт - добавляем ключик версии параметром к файлу
                    if (($name == 'link' || $name == 'script') && ($k == 'src' || $k == 'href')) {
                        $v .= (strpos($v, '?') === false ? '?' : '&') . $version_key;
                    }

                    if ($k != 'cdata') {
                        $result .= ' ' . $k . '="' . Lib_Html::ChangeQuotes($v) . '"';
                    }
                }

                if ($data['cdata']) {
                    $result .= '>' . $data['cdata'] . '</' . $name . '>';
                } elseif ($close) {
                    $result .= '></' . $name . '>';
                } else {
                    $result .= ' />';
                }
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    private static function _getVersionKey()
    {
        if (self::$version_key !== null) {
            return self::$version_key;
        }

        return 1.2;
    }

    public function __get($name)
    {
        $name = strtolower($name);

        if (isset($this->_tags[$name]) && is_array($this->_tags[$name]) && isset($this->_tags[$name][0]) && !is_array($this->_tags[$name][0])) {
            $this->_tags[$name] = array_unique($this->_tags[$name]);
        }

        switch ($name) {
            case 'tags':
                return $this->_tags;
            case 'title':
                if (isset($this->_tags['title']) && is_array($this->_tags['title'])) {
                    return implode($this->_titleDelimiter, $this->_tags['title']);
                }
                break;
            case 'keywords':
                if (isset($this->_tags['keywords']) && is_array($this->_tags['keywords'])) {
                    return implode(', ', $this->_tags['keywords']);
                }
                break;
            case 'description':
                if (isset($this->_tags['description']) && is_array($this->_tags['description'])) {
                    return implode(' ', $this->_tags['description']);
                }
                break;
            case 'styles':
                if (isset($this->_tags['link'])) {
                    return $this->_tags['link'];
                }

                return [];
            case 'scripts':
                if (isset($this->_tags['script'])) {
                    return $this->_tags['script'];
                }

                return [];
            case 'head':
                $title = $this->Title;
                $keywords = $this->Keywords;
                $description = $this->Description;

                $old_tags = $this->_tags;

                $this->add('meta', [
                    'name' => 'keywords',
                    'content' => $keywords,
                ]);
                $this->add('meta', [
                    'name' => 'description',
                    'content' => $description,
                ]);

                $head = $this->prepareHTML('meta')
                    . '<title>' . $title . '</title>'
                    . $this->prepareHTML('link')
                    . $this->prepareHTML('script', true);

                $this->_tags = $old_tags;

                return $head;

            default:
                if (isset($this->_tags[$name])) {
                    return $this->_tags[$name];
                } else {
                    return null;
                }
                break;
        }

        return null;
    }

    public function __set($name, $value)
    {
        $name = strtolower($name);

        switch ($name) {
            case 'title':
            case 'keywords':
            case 'description':
                return $this->_tags[$name] = [$value];
                break;
        }

        return null;
    }
}
