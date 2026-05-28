<?php

class Lib_Curl
{
    /** @var array Параметры запросов по умолчанию */
    private static $default_params = [
        'follow_redirect' => true,
        'silent' => true,
        'no_error' => true,
        'agent' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)',
        'cookie' => '', // cookie data || cokie file name
        'referrer' => '',
        'httpheader' => [
            'Accept-Language: ru,en-us;q=0.7,en;q=0.3',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Encoding: none',
            'Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.7',
        ],
        'get_headers' => false, // get headers too
        'print_command' => false, // print curl command
        'use_proxy' => false, // get using proxy from proxy list
        'query' => null, // array of post data
        'dont_encode' => false, // don't urlencode $params['query']
        'url' => '',
        'retry' => 1, // retry count
        'timeout' => 30,
        'only_proxy' => false, // get only via proxy!
        'username' => null, // user name for http-autentificalion
        'password' => null, // password for http-autentification
    ];

    /**
     * Генерация запроса
     *
     * @static
     *
     * @param array $params
     *
     * @throws Lib_Exception_InvalidArgument_Backtraced
     *
     * @return bool|string
     */
    public static function GenerateQuery($params = [])
    {
        if (!is_array($params)) {
            throw new Lib_Exception_InvalidArgument_Backtraced('Invalid CURL params');
        }

        $post = '';

        foreach ($params as $k => $v) {
            $post .= ($post == '' ? '' : '&') . $k . '=' . urlencode($v);
        }

        unset($k, $v, $params);

        return $post;
    }

    /**
     * Генерация кук
     *
     * @static
     *
     * @param array $params
     * @param bool $dont_encode
     *
     * @throws Lib_Exception_InvalidArgument_Backtraced
     *
     * @return bool|string
     */
    public static function GenerateCookies($params = [], $dont_encode = false)
    {
        if (!is_array($params)) {
            throw new Lib_Exception_InvalidArgument_Backtraced('Invalid CURL params');
        }

        $post = '';

        foreach ($params as $k => $v) {
            $post .= ($post == '' ? '' : '; ') . urlencode($k) . '=' . ($dont_encode ? $v : urlencode($v));
        }

        unset($k, $v, $params);

        return $post;
    }

    /**
     * Выполнить запрос
     *
     * @static
     *
     * @param array $params
     *
     * @throws Lib_Exception_InvalidArgument_Backtraced
     * @throws Lib_Exception_Runtime_Backtraced
     *
     * @return Lib_Curl_Response
     */
    public static function Query($params = [])
    {
        if (!is_array($params)) {
            throw new Lib_Exception_InvalidArgument_Backtraced('Invalid CURL params');
        }

        $params = Lib_Array::MergeRecursive(self::$default_params, $params);

        if ($params['url'] == '') {
            throw new Lib_Exception_Runtime_Backtraced('Please specify the URL');
        }

        if (!extension_loaded('curl')) {
            throw new Lib_Exception_Runtime_Backtraced('Curl php module not found');
        }

        if (is_array($params['query'])) {
            //$params['query'] = http_build_query( $params['query'] );
            //$params['query'] = self::GenerateQuery( $params['query'] );
        } elseif (!is_string($params['query'])) {
            $params['query'] = '';
        }

        if (is_array($params['cookie'])) {
            $params['cookie'] = self::GenerateCookies($params['cookie']);
        } elseif (!is_string($params['cookie'])) {
            $params['cookie'] = '';
        }

        return new Lib_Curl_Response($params);
    }
}
