<?php

namespace System;

/**
 * Класс HTTP-клиента.
 *
 * @package System
 */
class HttpClient
{
    /** @var int $_timeout */
    private $_timeout = 0;
    /** @var bool $_follow_redirects */
    private $_follow_redirects = false;
    /** @var bool $_debug */
    private $_debug = false;

    /** @var array */
    protected $_info = [];

    /**
     * Выполняет GET-запрос на заданный URL
     *
     * @param string $uri URL запроса
     * @param int $timeout Таймаут запроса.
     *    Если указан 0 - будет использоваться таймаут по умолчанию.
     * @param string $encoding Кодировка
     *
     * @return HttpResponse Объект ответа
     */
    public static function Get($uri, $timeout = 0, $encoding = HttpRequest::DEFAULT_EXTERNAL_ENCODING)
    {
        $request = new HttpRequest($uri);
        $client = new self();

        return $client->setTimeout($timeout)->query(
            $request->setExternalEncoding($encoding)
        );
    }

    /**
     * Выполняет POST-запрос на заданный URL
     *
     * @param string $uri URL запроса
     * @param string $body Тело запроса
     * @param int $timeout Таймаут запроса.
     *    Если указан 0 - будет использоваться таймаут по умолчанию.
     * @param string $encoding Кодировка
     *
     * @return HttpResponse Объект ответа
     */
    public static function Post($uri, $body, $timeout = 0, $encoding = HttpRequest::DEFAULT_EXTERNAL_ENCODING)
    {
        $request = new HttpRequest($uri);
        $client = new self();

        $request
            ->setMethod(HttpRequest::POST)
            ->setBody($body)
            ->setExternalEncoding($encoding);

        return $client->setTimeout($timeout)->query($request);
    }

    /**
     * Выполняет GET-запрос, декодируя данные из json-формата
     *
     * @param string $uri URL запроса
     *
     * @return array Данные ответа
     */
    public static function GetJson($uri)
    {
        return \Lib_Json::decode(self::Get($uri)->getBody());
    }

    /**
     * Выполняет POST-запрос, декодируя данные из json-формата
     *
     * @param string $uri URL запроса
     * @param array $data Данные запроса
     * @param bool $encode_data
     *
     * @return array Данные ответа
     */
    public static function PostJson($uri, array $data, $encode_data = true)
    {
        if (true === $encode_data) {
            mb_convert_variables('utf-8', HttpRequest::GetInternalEncoding(), $data);
        }

        return \Lib_Json::decode(
            self::Post(
                $uri,
                empty($data) ? '' : \Lib_Json::encode($data),
                0,
                'utf-8'
            )->getBody()
        );
    }

    /**
     * Выполняет запрос
     *
     * @param HttpRequest $request
     *
     * @throws \Lib_Exception_Runtime Произошла ошибка при выполнеии запроса
     *
     * @return HttpResponse Объект ответа
     */
    public function query(HttpRequest $request)
    {
        $ch = curl_init();
        $this->_setOptions($ch, $request);

        $result = curl_exec($ch);

        if ($this->_debug) {
            echo $result . "\n\n\n";
        }

        $errno = curl_errno($ch);

        if ($errno != 0 || $result === false) {
            throw new \Lib_Exception_Runtime('Curl error: ' . curl_error($ch) . ' errno: ' . $errno . ' on connect to ' . $request->getUrl());
        }

        if (false === $info = curl_getinfo($ch)) {
            throw new \Lib_Exception_Runtime("Can't get curl info");
        }

        curl_close($ch);

        $this->_info = $info;

        return $this->_makeResponse($result, $info);
    }

    /**
     * Выполняет множественный запрос.
     * Метод принимает массив объектов HttpRequest и отправляет их в стек, затем ожидает выполнения всех запросов
     * и возвращает массив ответов с теми же ключами, что и массив запросов.
     *
     * @param array $requests Массив запросов
     *
     * @return HttpResponse[]
     *
     * @throws \Lib_Exception_InvalidArgument
     */
    public function multiQuery(array $requests)
    {
        $mh = curl_multi_init();
        $ch = [];

        foreach ($requests as $k => $request) {
            if (false === $request instanceof HttpRequest) {
                throw new \Lib_Exception_InvalidArgument('Member of requests array is not an instance of HttpRequest');
            }
            $ch[$k] = curl_init();
            $this->_setOptions($ch[$k], $request);
            curl_multi_add_handle($mh, $ch[$k]);
        }

        $active = null;
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($mh) == -1) {
                continue;
            }
            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }

        $responses = [];

        foreach ($ch as $k => $_ch) {
            $errno = curl_errno($_ch);
            $result = curl_multi_getcontent($_ch);

            if ($errno != 0 || false === $result || false === ($info = curl_getinfo($_ch))) {
                $responses[$k] = null;
            } else {
                $info = curl_getinfo($_ch);
                $responses[$k] = $this->_makeResponse($result, $info);
            }
            curl_close($_ch);
        }

        curl_multi_close($mh);

        return $responses;
    }

    /**
     * Устанавливает значение timeout-а запроса
     *
     * @param int $timeout Величина timeout-а
     *
     * @throws \Lib_Exception_InvalidArgument_Type
     * @throws \Lib_Exception_InvalidArgument
     *
     * @return \System\HttpClient $this Данный объект
     */
    public function setTimeout($timeout)
    {
        if (false === is_int($timeout)) {
            throw new \Lib_Exception_InvalidArgument_Type($timeout, 'int');
        }

        if ($timeout < 0) {
            throw new \Lib_Exception_InvalidArgument('Timeout must have non-negative integer value');
        }

        $this->_timeout = $timeout;

        return $this;
    }

    /**
     * Возвращает значение timeout-а запроса
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->_timeout;
    }

    /**
     * Устанавливает значение флага следования редиректам
     *
     * @param bool $follow_redirects Значение флага следования редиректам
     *
     * @throws \Lib_Exception_InvalidArgument_Type
     *
     * @return \System\HttpClient $this
     */
    public function setFollowRedirects($follow_redirects)
    {
        if (false === is_bool($follow_redirects)) {
            throw new \Lib_Exception_InvalidArgument_Type($follow_redirects, 'bool');
        }
        $this->_follow_redirects = $follow_redirects;

        return $this;
    }

    /**
     * Получение значения флага следования редиректам
     *
     * @return bool
     */
    public function getFollowRedirects()
    {
        return $this->_follow_redirects;
    }

    /**
     * Установка режима отладки
     *
     * @param bool $debug
     *
     * @return HttpClient $this
     */
    public function setDebug($debug)
    {
        $this->_debug = $debug;

        return $this;
    }

    /**
     * Получение состояния режима отладки
     *
     * @return bool
     */
    public function getDebug()
    {
        return $this->_debug;
    }

    /**
     * @internal
     *
     * @return array
     */
    public function _getInfo()
    {
        return $this->_info;
    }

    /**
     * Установка опций запроса
     *
     * @param resource $ch Curl-handler
     * @param HttpRequest $request Данный объект
     */
    private function _setOptions($ch, HttpRequest $request)
    {
        curl_setopt($ch, CURLOPT_URL, $request->getUrl());

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if ($this->_timeout > 0) {
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeout);
        }

        if ($this->_follow_redirects) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // allow redirects
            curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        }

        $headers = $request->getHeaders();

        if (count($headers) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if (isset($headers['User-Agent'])) {
            curl_setopt($ch, CURLOPT_USERAGENT, $headers['User-Agent']);
        }

        if (isset($headers['Referer'])) {
            curl_setopt($ch, CURLOPT_REFERER, $headers['Referer']);
        }
        curl_setopt($ch, CURLOPT_HEADER, 1);

        $cookies = $request->getCookies();

        if (count($cookies) > 0) {
            curl_setopt($ch, CURLOPT_COOKIE, $this->_buildCookies($cookies));
        }

        $body = $request->getBody();

        if ($request->getMethod() == HttpRequest::POST && $body != '') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request->getBody());
        }
    }

    /**
     * Генерация заголовка Cookie
     *
     * @param array $cookies
     *
     * @return string
     */
    private function _buildCookies(array $cookies)
    {
        $cookie_strings = [];

        foreach ($cookies as $cookie) {
            $cookie_strings[] = urlencode($cookie['name']) . '=' . urlencode($cookie['value']);

            if (isset($cookie['expire'])) {
                $cookie_strings[] = 'expires=' . gmdate('D, d M Y H:i:s T', $cookie['expire']);
            }

            if (isset($cookie['path'])) {
                $cookie_strings[] = 'path=' . $cookie['path'];
            }

            if (isset($cookie['domain'])) {
                $cookie_strings[] = 'domain=' . $cookie['domain'];
            }

            if (isset($cookie['secure'])) {
                $cookie_strings[] = 'secure';
            }
        }

        if ($this->_debug) {
            echo implode('; ', $cookie_strings) . ';';
        }

        return implode('; ', $cookie_strings) . ';';
    }

    /**
     * Парсинг заголовка Set-Cookie
     *
     * @param string $cookie_string Заголовок Set-Cookie
     * @param HttpResponse $response Объект HTTP-ответа, в который устанавливаются cookies
     */
    private function _parseCookies($cookie_string, HttpResponse $response)
    {
        $cookie_strings = explode(';', $cookie_string);
        $cookie = null;

        foreach ($cookie_strings as $part) {
            list($name, $value) = @explode('=', $part, 2);
            $name = urldecode(trim($name));
            $value = urldecode(trim($value));

            if ($cookie !== null) {
                if ($name == 'expires') {
                    $cookie['expire'] = strtotime($value);
                } elseif ($name == 'path') {
                    $cookie['path'] = $value;
                } elseif ($name == 'domain') {
                    $cookie['domain'] = $value;
                } elseif ($name == 'secure') {
                    $cookie['secure'] = true;
                } else {
                    $response->setCookie(
                        $cookie['name'],
                        $cookie['value'],
                        $cookie['expire'] ?? null,
                        $cookie['path'] ?? '/',
                        $cookie['domain'] ?? '',
                        isset($cookie['secure'])
                    );
                    $cookie = null;
                }

                continue;
            }
            $cookie = [
                'name' => $name,
                'value' => $value,
            ];
        }

        if (null !== $cookie) {
            $response->setCookie(
                $cookie['name'],
                $cookie['value'],
                $cookie['expire'] ?? null,
                $cookie['path'] ?? '/',
                $cookie['domain'] ?? '',
                isset($cookie['secure'])
            );
        }
    }

    private function _makeResponse($result, array $info)
    {
        $response = new HttpResponse();

        if (isset($info['content_type'])) {
            if (false !== strpos($info['content_type'], ';')) {
                list($content_type, $encoding) = explode(';', $info['content_type'], 2);
                $response->setContentType($content_type)->setExternalEncoding($encoding);
            } else {
                $response->setContentType($info['content_type']);
            }
        }
        $response->setStatus($info['http_code']);

        if ($body = substr($result, $info['header_size'])) {
            $response->setBody($body);
        }

        $headers = [];
        $raw_headers = explode("\r\n", substr($result, 0, $info['header_size']));

        foreach ($raw_headers as &$raw_header) {
            if (strpos($raw_header, ':') === false) {
                continue;
            }
            list($name, $value) = explode(':', $raw_header, 2);
            $headers[$name] = trim($value);
        }

        if (isset($headers['Set-Cookie'])) {
            $this->_parseCookies($headers['Set-Cookie'], $response);
            unset($headers['Set-Cookie']);
        }

        $response->setHeaders($headers);

        return $response;
    }
}
