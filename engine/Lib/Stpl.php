<?php

/**
 * SimpleTPL.
 * Простой шаблонизатор
 *
 * @method static DateTimePicker
 * @method static Navigation_BreadCrumb_Menu
 * @method static Navigation_BreadCrumb_Simple
 * @method static Photos
 */
class STPL
{
    /** @var array $_paths Пути к библиотекам шаблонов */
    private static $_paths = [];

    /**
     * Инициализация шаблонизатора
     *
     * @static
     *
     * @param array $paths
     */
    public static function Init($paths = [])
    {
        self::$_paths = $paths;
    }

    /**
     * Проверка наличия шаблона
     *
     * @static
     *
     * @param string $template Шаблон
     *
     * @return int
     */
    public static function IsTemplate($template)
    {
        // Ищем шаблон в дефолтовой теме по всем зарегистрированным путям
        foreach (self::$_paths as $path) {
            if (is_file($path . $template . '.php')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Получение имени файла шаблона
     *
     * @static
     *
     * @param $template
     *
     * @return bool|string
     */
    private static function _getTemplate($template)
    {
        // Ищем шаблон в дефолтовой теме по всем зарегистрированным путям
        foreach (self::$_paths as $path) {
            $file = $path . $template . '.php';

            if (is_file($file)) {
                return $file;
            }
        }

        Lib_Trace::Backtrace('Template not found: ' . $template . ' in paths ' . var_export(self::$_paths, true));

        return false;
    }

    /**
     * Загрузка и выполнение шаблона
     * Возвращает результат выполнени
     *
     * @static
     *
     * @param string $template Шаблон
     * @param array $vars Контекст
     *
     * @return string
     */
    public static function Fetch($template, $vars = [])
    {
        $file = self::_getTemplate($template);

        if ($file !== false) {
            return self::_fetchTemplate($file, $vars);
        }

        return '';
    }

    /**
     * Загрузка и отображение шаблона
     *
     * @static
     *
     * @param string $template Шаблон
     * @param array $vars Контекст
     */
    public static function Display($template, $vars = [])
    {
        $file = self::_getTemplate($template);

        if ($file !== false) {
            self::_displayTemplate($file, $vars);
        }
    }

    /**
     * Выполняет шаблон
     *
     * @static
     *
     * @param $template
     * @param array $vars
     *
     * @return string
     */
    private static function _fetchTemplate($template, $vars)
    {
        ob_start();
        /** @noinspection PhpIncludeInspection */
        include $template;

        return ob_get_clean();
    }

    /**
     * Отображает шаблон
     *
     * @static
     *
     * @param $template
     * @param $vars
     *
     * @return bool
     */
    private static function _displayTemplate($template, $vars)
    {
        /** @noinspection PhpIncludeInspection */
        include $template;

        return true;
    }

    /**
     * Регистрирует путь к библиотьеке блоков
     *
     * @static
     *
     * @param string $path Путь к папке шаблонов
     *
     * @return bool
     *
     * @throws Lib_Exception_InvalidArgument_Backtraced
     */
    public static function PathRegister($path)
    {
        if (false === is_string($path)) {
            throw new Lib_Exception_InvalidArgument_Backtraced('Invalid STPL path');
        }

        if (false === array_search($path, self::$_paths) && is_dir($path)) {
            $old_paths = self::$_paths;
            array_unshift(self::$_paths, $path);

            return $old_paths;
        }

        return self::$_paths;
    }

    /**
     * Восстанавливает список путей к заданному состоянию
     *
     * @static
     *
     * @param $paths
     *
     * @throws Lib_Exception_InvalidArgument
     */
    public static function RestorePaths($paths)
    {
        if (false === is_array($paths)) {
            throw new Lib_Exception_InvalidArgument('Invalid STPL paths');
        }
        self::$_paths = $paths;
    }

    /**
     * Отображение результата работы контроллера
     *
     * @static
     *
     * @param \System\App $app
     * @param string $path Сервис/Контроллер
     * @param array $params Параметры
     *
     * @throws Lib_Exception_InvalidArgument_Backtraced
     *
     * @return mixed|string
     */
    public static function FetchAction($app, $path, $params = [])
    {
        list($service, $controller_name) = explode('/', $path, 2);

        if (empty($service)) {
            throw new Lib_Exception_InvalidArgument_Backtraced('Undefined Service');
        }

        if (empty($controller_name)) {
            throw new Lib_Exception_InvalidArgument_Backtraced('Undefined service controller');
        }

        try {
            $controller = \System\Service_Controller_Factory::getInstance($service, $controller_name, $app);
        } catch (\System\Service_Controller_Exception_MissingController $e) {
            return '';
        }

        // Register templates path
        $old_paths = self::PathRegister(ENGINE_PATH . 'Service/' . $service . '/Template/');

        // Launch controller
        $result = '';

        try {
            $result = $controller->Action($params);
        } catch (\Lib_Exception $e) {
            if (false === ($e instanceof Lib_Exception_Backtrace_Interface)) {
                Lib_Trace::BacktraceException($e);
            }
        }

        // Restore templates paths
        self::RestorePaths($old_paths);

        return $result;
    }

    /**
     * Получить контент хелпера
     *
     * @static
     *
     * @param array $vars Параметры хелпера
     *
     * @return string
     */
    public static function PagesLink($vars = [])
    {
        if (empty($vars['active_page_class'])) {
            $vars['active_page_class'] = 'active';
        }

        if (empty($vars['aclass'])) {
            $vars['aclass'] = '';
        }

        if (empty($vars['class'])) {
            $vars['class'] = 'pagination';
        }

        if (false === is_array($vars['pageslink']) || count($vars['pageslink']['btn']) == 0) {
            return '<br clear = "both"/>';
        }

        $out = '<nav><ul class="' . $vars['class'] . '">';

        if (isset($vars['pageslink']['first']) && $vars['pageslink']['first'] != '' && $vars['pageslink']['first'] != $vars['pageslink']['current']) {
            $out .= '<li><a href="' . $vars['pageslink']['first'] . '"';

            if ($vars['aclass']) {
                $out .= 'class="' . $vars['aclass'] . '"';
            }
            $out .= ' >первая</a></li>';
        }

        if ($vars['pageslink']['back'] != '') {
            $out .= '<li><a href = "' . $vars['pageslink']['back'] . '" title="предыдущая страница"';

            if ($vars['aclass']) {
                $out .= 'class="' . $vars['aclass'] . '"';
            }
            $out .= '>&lt;&lt;</a></li>';
        }

        foreach ($vars['pageslink']['btn'] as $l) {
            $out .= '<li' . ($l['active'] ? ' class="active"' : '') . '><a  href = "' . $l['link'] . '"';
            $out .= '>' . $l['text'] . '</a></li>';
        }

        if ($vars['pageslink']['next'] != '') {
            $out .= '<li><a href = "' . $vars['pageslink']['next'] . '" title="следующая страница"';

            if ($vars['aclass']) {
                $out .= 'class="' . $vars['aclass'] . '"';
            }
            $out .= '>&gt;&gt;</a></li>';
        }

        if (isset($vars['pageslink']['last']) && $vars['pageslink']['last'] != '' && $vars['pageslink']['last'] != $vars['pageslink']['current']) {
            $out .= '<li><a href = "' . $vars['pageslink']['last'] . '"';

            if ($vars['aclass']) {
                $out .= 'class="' . $vars['aclass'] . '"';
            }

            $out .= '>последняя</a></li>';
        }
        $out .= '</ul ></nav>';

        return $out;
    }
}
