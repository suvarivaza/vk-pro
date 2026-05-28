<?php

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Service\Users\Model_Factory;
use GuzzleHttp\Client;


/*
 * @property Model_Factory $factory
 */

class Lib_VK
{



    //private  $v = '5.65'; //старая версия апи
    private static $v = '5.131';

    public $i;

    private static $tempProxies = [];

    /** @var \Service\Users\Model_Users_User */
    private static $user = null;

    /** @var \Service\Tasks\Model_Tasks_Task */
    protected $task = null;

    function __construct(\Service\Tasks\Model_Tasks_Task $task = null)
    {
        $this->task = $task;
    }


    public static function init(\Service\Users\Model_Users_User $user, \Service\Tasks\Model_Tasks_Task $task = null)
    {
        self::$user = $user;
    }

    public function setTask(\Service\Tasks\Model_Tasks_Task $task = null)
    {
        $this->task = $task;
    }

    private function errorLog($params)
    {

        if (!isset(self::$user->userId)) return;

        $factory = new \Service\Logs\Model_Factory();
        $logs = $factory->getLogs();
        $logs->Log(\Service\Logs\Model_Config::VK_API_ERROR, 0, self::$user->userId, $params);
    }

    private function successEmptyLog($params)
    {

        if (!isset(self::$user->userId)) return;

        $factory = new \Service\Logs\Model_Factory();
        $logs = $factory->getLogs();
        $logs->Log(\Service\Logs\Model_Config::VK_API_EMPTY, 0, self::$user->userId, $params);
    }

    private function UnknownErrorLog($params)
    {

        if (!isset(self::$user->userId)) return;

        $factory = new \Service\Logs\Model_Factory();
        $logs = $factory->getLogs();
        $logs->Log(\Service\Logs\Model_Config::VK_API_UNKNOWN_ERROR, 0, self::$user->userId, $params);
    }

    private function successLog($params)
    {
        if (!isset(self::$user->userId)) return;

        $factory = new \Service\Logs\Model_Factory();
        $logs = $factory->getLogs();

        if (isset($this->task->taskId)) $objectId = $this->task->taskId;
        else $objectId = 0;

        $logs->Log(\Service\Logs\Model_Config::VK_API_SUCCESS, $objectId, self::$user->userId, $params);
    }



    /*
     * Делаем запрос к API
     * https://vk.com/dev/manuals
     * https://vk.com/dev/methods
     */
    public function get($action, $params)
    {

        //часто нет access_token!
        //нужно разобраться в чем дело!
        if (!$params['access_token']) {

            $response = [
                'error' => 1,
                'errorText' => 'Ошибка! Не передан access_token!',
            ];

            //залогируем ошибки
            $this->errorLog([[], $action, $params, $response, debug_backtrace()]);

            return $response;
        }

        $proxies = Lib_Proxy::getProxy();


        //делаем три попытки на случай не рабочих прокси
        // с задержкой 0,1 секунда (у вк есть ограничение на количество запросов в секунду!)
        $i = 0;
        do {

            if ($i > 0) sleep(1);
            $i++;

            //берем рандомный прокси
            //берем рандомный прокси
            $key = rand(0, count($proxies) - 1);
            $proxy = $proxies[$key];
            unset($proxies[$key]); //удалим использованный прокси из массива
            $proxies = array_values($proxies); //сделаем чтобы в массиве ключи снова шли по порядку


            $query = http_build_query($params); //генерируем строку запроса
            $url = 'https://api.vk.com/method/' . $action . '?' . $query . '&v=' . self::$v;

            //делаем запрос - лучше это переделать на curl!
            if (!empty($proxy)) {

                $auth = base64_encode($proxy->username . ':' . $proxy->password);

                $aContext = [
                    'http' => [
                        'proxy' => 'tcp://' . $proxy->ip . ':' . $proxy->port_http,
                        'request_fulluri' => true,
                        'header' => 'Proxy-Authorization: Basic ' . $auth,
                    ],
                ];
                $cxContext = stream_context_create($aContext);
                $data = file_get_contents($url, false, $cxContext);
            }

            $result = json_decode($data, true);
            if (!$result) continue;

            //Если Успешный запрос
            if (isset($result['response'])) {

                $response = $result['response'];
                $response['error'] = 0;

                //Запишем в лог - Успешный запрос
                //Запишем в лог - Успешный ответ
                $this->successLog([$proxy, $action, $params, $result, debug_backtrace()]);

            } else if (!isset($result['response']) and isset($result['error'])) {

                $response = [
                    'error' => $result['error']['error_code'],
                    'errorText' => $result['error']['error_msg'],
                ];


                //залогируем ошибки
                $this->errorLog([$proxy, $action, $params, $result, debug_backtrace()]);

            }

            //Ошибка 6 это - Слишком много запросов в секунду. https://vk.com/dev/errors
//            if (self::$user !== null && $json['response']['error'] == 6) {
//                $logs->Log(\Service\Logs\Model_Config::VK_API_PROXY, 0, self::$user->userId, [$proxy, $action, $params, $json, debug_backtrace()]);
//            }


        } while ((isset($result['response']['error']) and $result['response']['error'] == 6) and $i < 3);
        //сделаем 3 попытки запроса в случае если возвращается ошибка 6 - Слишком много запросов в секунду. https://vk.com/dev/errors

        if (!$result) {
            //Не удалось получить ответ от API BK за 3 попытки
            logMail('Vk-Pro.top request to API VK error', 'Не удалось получить ответ от API BK за 3 попытки');
            return null;
        }


        usleep(100000);
        if (isset($response)) return $response;
        else return null;

    }


    /**
     * http client Guzzle - https://docs.guzzlephp.org/en/stable/quickstart.html
     * и тут https://sheensay.ru/guzzle/quickstart
     * @param $method
     * @param $params
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function Guzzle($method, $params, $proxyData)
    {

//        dd($proxyData);
        try {

            $data = [
                'base_uri' => 'https://api.vk.com/method/', // Base URI is used with relative requests
                'headers' => [
                    'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                    'accept-encoding' => 'gzip, deflate, br',
                    'accept-language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
                    'cache-control' => 'max-age=0',
                    'referer' => 'https://vk-pro.top/',
                    'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.72 Safari/537.36',
                    //'Accept' => 'application / json',
                    //'X-Foo' => ['Bar', 'Baz'] //можно так передать данные в заголовках
                ],
                'allow_redirects' => true,
                'timeout' => 2.0, //устанавливает таймаут ожидания ответа
                //'debug' => true, //покажет полную инфу
                //'http_errors'  =>  false, //отключить выдачу исключений при ошибках протокола HTTP
//                'proxy' => $proxyData ?? '', //нужно передать строку вида "http://username:password@192.168.16.1:10"
//                'proxy' => "42-36-42_mail_ru:103f633e7f@45.128.230.122:30009"
//                'proxy' => "SrZGK26k:dTQasHJ7@45.152.215.171:64780/64781"
            ];

            if($proxyData) $data[] = $proxyData; //нужно передать строку вида "username:password@192.168.16.1:10"

            $client = new Client($data);


            //$request = new  Request ('POST', $method);
            //$response = $client->send($request, [
            //'query' => $params,

            //on_headers - вызывается, когда HTTP-заголовки ответа получены, но тело еще не начало загружаться. https://docs.guzzlephp.org/en/stable/request-options.html#on-headers
//            'on_headers' => function (ResponseInterface $response){
//                $code = $response->getStatusCode();
//                if ($code !== 200) {
//                }
//            },

            //Показыает детальную информацию о статусе запроса
//            'on_stats' => function (TransferStats $stats) {
//                echo $stats->getEffectiveUri() . " \ n "; //полная ссылка запроса
//                echo $stats->getTransferTime() . " \ n "; //время запроса
//                print_r($stats->getHandlerStats()); //много полезных данных о запросе
//                // Вы должны проверить, был ли получен ответ, прежде чем использовать объект ответа.
//                if ($stats->hasResponse()) {
//                    echo $stats->getResponse()->getStatusCode();
//                } else {
//                    // Данные об ошибках зависят от обработчика.
//                    //Перед использованием этого значения
//                    var_dump($stats->getHandlerErrorData()); //вам необходимо знать, какой // тип данных об ошибках использует ваш обработчик .
//                }
//            },
            // ]);

            $response = $client->request('GET', $method, ['query' => $params]);

            $code = $response->getStatusCode(); // 200
//            dd($code);
            //$reason = $response->getReasonPhrase(); // OK

            if ($code !== 200) throw new Exception ('Ошибка! Код ответа сервера ВК ' . $code);

            // Проверка, есть ли определенный заголовок
            //if ($response->hasHeader('Content-Length')) echo "It exists";

            // Получаем заголовок из ответа
            //echo $response->getHeader('Content-Length')[0];

            // Получаем все заголовки ответа
//        foreach ($response->getHeaders() as $name => $values) {
//            echo $name . ': ' . implode(', ', $values) . "\r\n";
//        }


            //метод getBody вернет поток Streams! нужно приводить к строке или использовать $contents = $response->getBody()->getContents()
            //https://docs.guzzlephp.org/en/latest/psr7.html#responses
            //https://stackoverflow.com/questions/30549226/guzzlehttp-how-get-the-body-of-a-response-from-guzzle-6
            if (is_numeric($response->getBody())) $result = $response->getBody(); //может быть получено число
            else $result = json_decode($response->getBody(), true); //или json

        } catch (GuzzleException $e) {
            //нужно обязательно ловить исключения от Guzzle иначе будет ошибку неперехваченное исключение

            $errorMassage = $e->getMessage();
            dd($errorMassage);
            $line = $e->getLine();
            $file = $e->getFile();
            //здесь нужно обрабатывать исключения от Guzzle
            log_info('Guzzle', "GuzzleException errorMassage: {$errorMassage} file: {$file} line: {$line}", 'logGuzzleException');
            return false;
        }

        return $result;

    }


    public function api($action, $params)
    {

        //часто нет access_token!
        //нужно разобраться в чем дело!
        if (!$params['access_token']) {

            $response = [
                'error' => 1,
                'errorText' => 'Ошибка! Не передан access_token!',
            ];

            //залогируем ошибки
            $this->errorLog([[], $action, $params, $response, debug_backtrace()]);

            return $response;
        }

        $params['v'] = self::$v; //добавляем версию API VK в параметры запроса (обязательный параметр)

        $proxy = Lib_Proxy::getCurrentTempProxy(); //берем текущий прокси из кэша

        $i = 0;
        do {
            if ($i > 0) sleep(1);
            $i++;

            if (!empty($proxy)) {
                $proxyData = "{$proxy->username}:{$proxy->password}@{$proxy->ip}:{$proxy->port_http}";
            }

            $result = $this->Guzzle($action, $params, $proxyData);

            $toMatchRequests = (!empty($result['error']) and $result['error'] == 6);

            if ($result and empty($result['error'])) break;
            else $proxy = Lib_Proxy::getNewTempProxy(); //если ошибка то попробуем с другим прокси

        } while ($i < 3);
        //делаем 3 попытки запроса в случае если от ВК возвращается ошибка 6 = Слишком много запросов в секунду. https://vk.com/dev/errors

        if (!$result) return false;

        // возможно 3 варианта
        // 1 - в ответе массив в котором есть ключ response (успех)
        // 2 - в ответе массив в котором есть ключ error (ошибка)
        // 3 - в ответе число (успех)

        if (isset($result['response'])) {

            $this->successLog([$proxy, $action, $params, $result, debug_backtrace()]);
            return $result['response'];

        } else if (isset($result['error'])) {

            $this->errorLog([$proxy, $action, $params, $result, debug_backtrace()]);

            return [
                'error' => $result['error']['error_code'],
                'errorText' => $result['error']['error_msg'],
            ];

        } else if (is_numeric($result)) {

            $this->successLog([$proxy, $action, $params, $result, debug_backtrace()]);
            return $result;

        } else {

            $this->UnknownErrorLog([$proxy, $action, $params, $result, debug_backtrace()]);
            return $result;

        }

    }

    /*
     * здесь не используются прокси! нужно настроить!
     */
    public function post($action, $query)
    {

        $query['v'] = self::$v;

        $curl = new \Lib_Curl();

        $params = [
            'httpheader' => [
                '"Content-Type:multipart/form-data"',
            ],
            'follow_redirect' => true,
            'url' => 'https://api.vk.com/method/' . $action,
            'get_headers' => true,
            'query' => $query,
        ];


        $result = $curl->Query($params);

        $json = json_decode($result->body(), true);

        if (!isset($json['response'])) {
            return [
                'error' => $json['error']['error_code'],
                'errorText' => $json['error']['error_msg'],
            ];
        }

        if (is_array($json['response'])) {
            $json['response']['error'] = 0;
        }

        return $json['response'];
    }


    /*
     * этот метод не дописан
     */
    public function curl_request($method, $data = null, $new_api = false)
    {

        $url = 'https://api.vk.com/method/' . $method;

        //если нужно подключить еще один аккаунт КК
        //$data['apicode'] = $new_api ? $this->apiKeyNew : $this->apiKey;

        //$data['limit'] = 100; //кк позволяет получить лимитированное кол-во записей

        //Делаем 3 попытки запроса к API (на случай сбоев)
        $count = 0;
        do {
            $count++;

            $curlObj = null;
            if ($curlObj === null) {
                // инициализируем curl только один раз
                $curlObj = curl_init();
                curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curlObj, CURLOPT_HEADER, 0);
                curl_setopt($curlObj, CURLOPT_POST, 1);
            }

            curl_setopt($curlObj, CURLOPT_CONNECTTIMEOUT, 10); //Максимальное время соединения в секундах
            curl_setopt($curlObj, CURLOPT_TIMEOUT, 10); //Максимальнове время выполнения в секундах
            curl_setopt($curlObj, CURLOPT_URL, $url);
            curl_setopt($curlObj, CURLOPT_POSTFIELDS, http_build_query($data));
            $response = curl_exec($curlObj);

            //Если успешно прерываем цикл
            if ($response) break;

        } while ($count <= 3);

        //Проверим код ответа сервера
        $httpCode = curl_getinfo($curlObj, CURLINFO_HTTP_CODE);
        //dd($response);
        //curl_close($curlObj); //переиспользуем открытое соединение поэтому не закрываем!
        if ($httpCode !== 200) throw new Exception("Не удалось выполнить запрос к АПИ! Код ответа = $httpCode", $httpCode);


        //Если не удалось выполнить запрос
        if ($response === false) {
            $curlError = curl_error($curlObj);
            logMail('Vk-Pro.top API VK Error!', 'Curl вернул ошибку: ' . print_r($curlError, true));
            throw new Exception("Curl вернул ошибку: " . print_r($curlError, true));
        }


        $result = json_decode($response, true);

        if (!$result or !is_array($result)) {

            throw new Exception("Не получилось распарсить ответ от Qcomment! Ответ: " . print_r($response, true) . ' После json_decode: ' . print_r($result, true), $httpCode);

        } else if (isset($result['error_code']) or (isset($result['status']) and $result['status'] === 'no')) {


            throw new Exception($result['reason'], $result['error_code']);

        } else {

            return $result;
        }

    }





    //оставил для примера работы с БД вне движка
    private function getTask($taskId)
    {

        $factoryTasks = new \Service\Tasks\Model_Factory();
        $query = $factoryTasks->tasks
            ->query()->sqlCalcFoundRows(true)
            ->limit(1)
            ->sort('taskId', 'DESC');
        $query->filter->fieldValue('taskId', '=', $taskId);
        $it = $query->iterator();
        $total = $it->getTotal();
        $task = $it->current();

        //или просто так в одну строчку)
        //$task = $factoryTasks->tasks->getById($taskId);

        return $task;
    }


    /*
     * Запрещает показывать новости от заданных пользователей и групп в ленте новостей текущего пользователя c access_toke
     * !можно вызвать ТОЛЬКО с ключом доступа пользователя, полученным в Standalone-приложении через Implicit Flow.
     */
    public function newsfeedAddBan($group_id, $access_token)
    {
        return $this->api('newsfeed.addBan', [
            'group_ids' => abs($group_id),
            'access_token' => $access_token,
        ]);
    }



    /********************************************
     ** Действия от лица пользователя (ДЛЯ БОТА!) **
     * поставить лайк, вступуть, сделать репост и тд**
     ********************************************/


    /*
     * вступить в группу, публичную страницу, а также подтвердить участие во встрече. пользователем access_token
     * !можно вызвать ТОЛЬКО с ключом доступа пользователя, полученным в Standalone-приложении через Implicit Flow.
     */
    public function groupsJoin($group_id, $access_token)
    {
        return $this->api('groups.join', [
            'group_id' => abs($group_id),
            'access_token' => $access_token,
        ]);
    }

    /*
     * Одобряет или создает заявку на добавление в друзья.
     * !можно вызвать ТОЛЬКО с ключом доступа пользователя, полученным в Standalone-приложении через Implicit Flow.
     */
    public function friendsAdd($user_id, $access_token)
    {
        return $this->api('friends.add', [
            'user_id' => abs($user_id),
            'text' => '',
            'access_token' => $access_token,
        ]);
    }


    /*
     * Добавляет комментарий к записи на стене.
     * можно вызвать с ключом доступа пользователя, полученным в Standalone-приложении через Implicit Flow.
     * можно вызвать с ключом доступа сообщества
     */
    public function createComment($ownerId, $itemId, $ownerType, $vkType, $message, $guid, $access_token)
    {

        if ($ownerType == 1) $ownerId = abs($ownerId);
        else if ($ownerType == 2) $ownerId = '-' . abs($ownerId); //для групп нужно указать с минусом!

        $data = [
            'owner_id' => $ownerId,
            'message' => $message,
            'guid' => $guid,
            'access_token' => $access_token,
        ];

        if ($vkType == 'post') {
            $method = 'wall.createComment';
            $data['post_id'] = $itemId;
        } else if ($vkType == 'photo') {
            $method = 'photos.createComment';
            $data['photo_id'] = $itemId;
        } else if ($vkType == 'video') {
            $method = 'video.createComment';
            $data['video_id'] = $itemId;
        }

        return $this->api($method, $data);
    }


    /*
     * Добавляет указанный объект в список Мне нравится пользователя access_token
     * !можно вызвать ТОЛЬКО с ключом доступа пользователя, полученным в Standalone-приложении через Implicit Flow.
     */
    public function likesAdd($type, $ownerId, $itemId, $ownerType, $access_token)
    {
        if ($ownerType == 1) $ownerId = abs($ownerId);
        else if ($ownerType == 2) $ownerId = '-' . abs($ownerId); //для групп нужно указать с минусом!

        return $this->api('likes.add', [
            'type' => $type,
            'owner_id' => $ownerId, //для групп с минусом нужно передавать???
            'item_id' => $itemId,
            'access_token' => $access_token,
        ]);
    }

    /*
     * Создает опросы
     * с ключом доступа пользователя, полученным в Standalone-приложении через Implicit Flow.
     * c ключом доступа пользователя, полученным в VK Apps через событие VKWebAppGetAuthToken.
     */
    public function pollsCreate($question, $ownerId, $ownerType, $add_answers, $access_token)
    {
        if ($ownerType == 1) $ownerId = abs($ownerId);
        else if ($ownerType == 2) $ownerId = '-' . abs($ownerId); //для групп нужно указать с минусом!

        return $this->api('polls.create', [
            'question' => $question,
            'owner_id' => $ownerId,
            'add_answers' => $add_answers,
            'access_token' => $access_token,
        ]);
    }

    /*
    * Копирует объект на стену пользователя или сообщества с ключем access_token
    * !можно вызвать ТОЛЬКО с ключом доступа пользователя, полученным в Standalone-приложении через Implicit Flow.
    */
    public function makeRepost($type, $ownerId, $itemId, $ownerType, $access_token)
    {

        if ($ownerType == 1) $ownerId = abs($ownerId);
        else if ($ownerType == 2) $ownerId = '-' . abs($ownerId); //для групп нужно указать с минусом!

        return $this->api('wall.repost', [
            'object' => $type . $ownerId . '_' . $itemId,
            'access_token' => $access_token,
        ]);
    }


    /*
     * Отдает голос текущего пользователя (владельца access_token) за выбранный вариант ответа в указанном опросе.
     * Можно вызывать с ключем доступа любого пользователя
     */
    public function addVote($ownerId, $ownerType, $pollId, $answer_ids, $access_token)
    {

        if ($ownerType == 1) $ownerId = abs($ownerId);
        else if ($ownerType == 2) $ownerId = '-' . abs($ownerId); //для групп нужно указать с минусом!

        return $this->api('polls.addVote', [
            'owner_id' => $ownerId,
            'poll_id' => $pollId,
            'answer_ids' => $answer_ids,
            'access_token' => $access_token,
        ]);
    }

    public function addPost($params)
    {
        return $this->api('wall.post', $params);
    }



    /******************
     ** ЗАГРУЗКА ФОТО **
     ******************/

    /*
    * Возвращает адрес сервера для загрузки фотографии на стену пользователя или сообщества.
    * После успешной загрузки Вы можете сохранить фотографию, воспользовавшись методом photos.saveWallPhoto.
    * Этот метод можно вызвать с ключом доступа пользователя. Требуются права доступа: photos.
    */
    public function getWallUploadServer($group_id, $access_token)
    {
        return $this->api('photos.getWallUploadServer', [
            'group_id' => $group_id,
            'access_token' => $access_token,
        ]);
    }

    /*
     * Сохраняет фотографии после успешной загрузки на URI, полученный методом photos.getWallUploadServer.
     * можно вызвать с ключом доступа пользователя
     */
    public function saveWallPhoto($group_id, $photo, $server, $hash, $access_token)
    {
        return $this->api('photos.saveWallPhoto', [
            'group_id' => $group_id,
            'photo' => $photo,
            'server' => $server,
            'hash' => $hash,
            'access_token' => $access_token,
        ]);
    }

    public function uploadPhotoToServerVk($upload_url, $filedata)
    {

        if (function_exists('curl_file_create')) { // php 5.5+
            $cFile = curl_file_create($filedata);
        } else {
            $cFile = '@' . realpath($filedata);
        }

        $params = [
            'httpheader' => [
                'Content-Type: multipart/form-data',
                'Accept: application/json, text/javascript, */*; q=0.01',
                'Accept-Language: en-US,en;q=0.5',
                'Accept-Encoding: gzip, deflate, br',
                'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:65.0) Gecko/20100101 Firefox/65.0',
                'Content-Type: application/json; encoding=utf-8',
            ],
            'follow_redirect' => true,
            'url' => $upload_url,
            'get_headers' => true,
            'query' => ['photo' => $cFile]
        ];

        $result = Lib_Curl::Query($params);
        return json_decode($result->body(), true);
    }


    /**********************
     ** ПОЛУЧЕНИЕ ДАННЫХ **
     *********************/

    public function getCallbackConfirmationCode($group_id, $access_token)
    {
        return $this->api('groups.getCallbackConfirmationCode', [
            'group_id' => $group_id,
            'access_token' => $access_token,
        ]);
    }

    public function addCallbackServer($group_id, $access_token)
    {
        return $this->api('groups.addCallbackServer', [
            'group_id' => $group_id,
            'url' => 'http://callback.vk-pro.top/auto/callback',
            'title' => 'vk-pro.top',
            'access_token' => $access_token,
        ]);
    }

    public function setCallbackSettings($group_id, $access_token)
    {
        return $this->api('groups.setCallbackSettings', [
            'group_id' => $group_id,
            'wall_post_new' => 1,
            'wall_repost' => 1,
            'access_token' => $access_token,
        ]);
    }

    /**
     * Возвращает список записей со стен пользователей или сообществ по их идентификаторам.
     * можно вызвать с сервисным ключом доступа
     * можно вызвать с ключом доступа пользователя
     * если пост не найден вернет пустой массив!
     *
     * @param $ownerId
     * @param $itemId
     * @param $ownerType - тип владельца страницы (1 = пользователь 2 = сообщество)
     * @param $access_token - наш токен для провеки
     * @return array|mixed
     */
    public function getPost($ownerId, $itemId, $ownerType, $access_token)
    {
        if ($ownerType == 1) $ownerId = abs($ownerId);
        else if ($ownerType == 2) $ownerId = '-' . abs($ownerId); //для групп нужно указать с минусом!

        return $this->api('wall.getById', [
            'posts' => $ownerId . '_' . $itemId,
            'access_token' => $access_token,
        ]);
    }


    /**
     * Возвращает список записей со стены пользователя или сообщества.
     * можно вызвать с сервисным ключом доступа
     * можно вызвать с ключом доступа пользователя
     *
     * @param $ownerId - идентификатор пользователя или сообщества (для групп должен быть указан со знаком минус!)
     * @param $ownerType - тип владельца страницы (1 = пользователь 2 = сообщество)
     * @param $access_token - наш токен для провеки
     * @param int $count
     * @return array|mixed
     */
    public function getPosts($ownerId, $ownerType, $access_token, $count = 100)
    {
        if ($ownerType == 1) $ownerId = abs($ownerId);
        else if ($ownerType == 2) $ownerId = '-' . abs($ownerId); //для групп нужно указать с минусом

        return $this->api('wall.get', [
            'owner_id' => $ownerId,
            'count' => $count,
            'access_token' => $access_token,
            'extended' => 1
        ]);
    }


    /*
     *
     */
    public function getGroup($group_id, $access_token)
    {

        //группа может быть получена по id или по короткому имени сообществ
        //по этому проверяем если передано число то приводим к абсолютному значению, если не число то подставляем как есть
        if (intval($group_id)) $group_id = abs($group_id);

        return $this->api('groups.getById', [
            'group_id' => $group_id,
            'fields' => 'activity,age_limits,members_count,is_closed,deactivated',
            'access_token' => $access_token,
        ]);
    }

    /*
     * Возвращает группы где указанный пользователь админ
     */
    public function getGroups($user_id, $access_token)
    {

        return $this->api('groups.get', [
            'user_id' => $user_id,
            'filter' => 'admin',
            'count' => 1000,
            'extended' => 1,
            'access_token' => $access_token,
        ]);
    }


    /*
     * Возвращает информацию о видеозаписях.
     * можно вызвать с сервисным ключом доступа
     * можно вызвать с ключом доступа пользователя
     *
     * ВАЖНО!!! Если видео пренадлежит группе то нужно указывать со знаком - а если пользователю то положительное число
     * ВОСПРОС! Как понять кому пренадлежит видео??? Без дополнительного запроса к апи Сейчас получаем только для групп
     */
    public function getVideo($ownerId, $itemId, $ownerType, $access_token, $limit = 1, $offset = 0)
    {

        if ($ownerType == 1) $ownerId = abs($ownerId);
        else if ($ownerType == 2) $ownerId = '-' . abs($ownerId); //для групп нужно указать с минусом

        return $this->api('video.get', [
            'owner_id' => $ownerId,
            'videos' => $ownerId . '_' . $itemId,
            'extended' => 1,
            'limit' => $limit,
            'offset' => $offset,
            'access_token' => $access_token,
        ]);
    }

    /*
     * Возвращает расширенную информацию о пользователях.
     * можно вызвать с сервисным ключом доступа
     * можно вызвать с ключом доступа пользователя
     * если user_ids не указан вернет пользователя access_token
     *
     * если пользователь не найден вернет пустой массив!
     */
    public function getUser($user_id, $access_token, $fields = 'photo_50,photo_200,has_photo')
    {

        //если $user_id число то приведем его к абсолютному значению на случай если передано отрицательное
        //$user_id может быть передан как строка (алиас пользователя) в этом случае оставляем как есть
        if (abs($user_id)) $user_id = abs($user_id);

        return $this->api('users.get', [
            'user_ids' => $user_id,
            'access_token' => $access_token,
            'fields' => $fields
        ]);

    }

    /*
     * возвращает список подписчиков пользователя
     * можно вызвать с сервисным ключом доступа
     * можно вызвать с ключом доступа пользователя
     * если user_id не указан вернет подписки пользователя access_token
     */
    public function getFollowers($ownerId, $offset, $access_token)
    {
        return $this->api('users.getFollowers', [
            'user_id' => abs($ownerId),
            'offset' => $offset,
            'count' => 1000,
            'access_token' => $access_token,
        ]);

    }


    /*
     * возвращает список подписок пользователя
     * можно вызвать с сервисным ключом доступа
     * можно вызвать с ключом доступа пользователя
     * если user_id не указан вернет подписки пользователя access_token
     */
    public function getSubscriptions($user_id, $access_token)
    {

        return $this->api('users.getSubscriptions', [
            'user_id' => $user_id,
            'access_token' => $access_token,
        ]);

    }

    /*
    * получает друзей пользователя
    * можно вызвать с сервисным ключом доступа.
    * можно вызвать с ключом доступа пользователя
    * user_id - если указан то получаем друзей указанного пользователя, иначе получаем друзей пользователя access_token
    * !Можно вызывать с ключем для проверки заданий - check_access_token
    */
    public function getFriends($uid, $access_token)
    {
        return $this->api('friends.get', [
            'user_id' => abs($uid),
            'access_token' => $access_token,
        ]);
    }


    /*
     * Получает список идентификаторов пользователей, которые выбрали определенные варианты ответа в опросе.
     * answer_ids - идентификаторы вариантов ответа
     * !Можно вызывать ТОЛЬКО с ключем доступа ПРОГОЛОСОВАВШЕГО пользователя - user_access_token
     * нужно предварительно проголосовать с нашего аккаунта и потом проверить этим токеном!
     */
    public function getVoters($ownerId, $ownerType, $poll_id, $answer_ids, $access_token)
    {

        if ($ownerType == 1) $ownerId = abs($ownerId);
        else if ($ownerType == 2) $ownerId = '-' . abs($ownerId); //для групп нужно указать с минусом!

        return $this->api('polls.getVoters', [
            'owner_id' => $ownerId,
            'poll_id' => $poll_id,
            'answer_ids' => $answer_ids,
            'offset' => 0,
            'count' => 1000,
            'sort' => 'desc',
            'access_token' => $access_token,
        ]);

    }


    /*
     * Возвращает детальную информацию об опросе по его идентификатору.
     * !Можно вызывать с ключем для проверки заданий - check_access_token
     */
    public function getVoteById($ownerId, $ownerType, $poll_id, $access_token)
    {

        if ($ownerType == 1) $ownerId = abs($ownerId);
        else if ($ownerType == 2) $ownerId = '-' . abs($ownerId); //для групп нужно указать с минусом!

        return $this->api('polls.getById', [
            'owner_id' => $ownerId,
            'poll_id' => abs($poll_id),
            'access_token' => $access_token,
        ]);
    }

    /*
     * Возвращает все фотографии пользователя или сообщества owner_id
     */
    public function getAllPhotosUser($ownerId, $access_token)
    {

        return $this->api('photos.getAll', [
            'owner_id' => abs($ownerId),
            'access_token' => $access_token,
        ]);
    }

    /*
     * Возвращает список фотографий в альбоме.
     * можно вызвать с сервисным ключом доступа
     * можно вызвать с ключом доступа пользователя
     * !Можно вызывать с ключем для проверки заданий - check_access_token
     */
    public function getPhotos($ownerId, $ownerType, $access_token)
    {

        if ($ownerType == 1) $ownerId = abs($ownerId);
        else if ($ownerType == 2) $ownerId = '-' . abs($ownerId); //для групп нужно указать с минусом!

        return $this->api('photos.get', [
            'owner_id' => $ownerId,
            'album_id' => 'profile',
            'access_token' => $access_token,
        ]);
    }

    /*
     * Возвращает список участников сообщества.
     * можно вызвать с ключом доступа пользователя.
     * можно вызвать с ключом доступа сообщества.
     * !Можно вызывать с ключем для проверки заданий - check_access_token
     */
    public function getMembers($group_id, $access_token, $count = 1000, $offset = 0)
    {
        return $this->api('groups.getMembers', [
            'group_id' => abs($group_id),
            'sort' => 'id_asc',
            'offset' => $offset,
            'count' => $count,
            'fields' => 'deactivated',
            'access_token' => $access_token,
        ]);
    }


    /**
     * Возвращает информацию о фотографиях по их идентификаторам.
     * можно вызвать с сервисным ключом доступа
     * можно вызвать с ключом доступа пользователя.
     * !Можно вызывать с нашим ключем для проверки заданий - check_access_token
     *
     * @param $ownerId - иденитфикатор пользователя или группы (для групп должен быть указан со знаком минус!)
     * @param $itemId - идетнификатор фото
     * @param $ownerType - тип владельца фото 1 - пользователь 2 - группа
     * @param $access_token - наш токен для проверки
     * @return array|mixed
     */
    public function getPhotoById($ownerId, $itemId, $ownerType, $access_token)
    {

        if ($ownerType == 1) $ownerId = abs($ownerId);
        else if ($ownerType == 2) $ownerId = '-' . abs($ownerId); //для групп нужно указать с минусом!

        return $this->api('photos.getById', [
            'photos' => $ownerId . '_' . $itemId,
            'extended' => 1,
            'access_token' => $access_token,
        ]);
    }


    /**
     * Возвращает список комментариев к записи на стене.
     * можно вызвать с сервисным ключом доступа
     * можно вызвать с ключом доступа пользователя
     *
     * @param $ownerId - идентификатор владельца страницы (пользователь или сообщество) (для групп должен быть указан со знаком минус!)
     * @param $post_id - идентификатор записи на стены
     * @param $ownerType - тип владельца страницы (1 = пользователь 2 = сообщество)
     * @param $access_token - наш токен для проверки
     * @param int $count
     * @param int $offset
     * @return array|mixed
     */
    public function getPostComments($ownerId, $post_id, $ownerType, $access_token, $count = 100, $offset = 0)
    {

        if ($ownerType == 1) $ownerId = abs($ownerId);
        else if ($ownerType == 2) $ownerId = '-' . abs($ownerId); //для групп нужно указать с минусом!

        return $this->api('wall.getComments', [
            'owner_id' => $ownerId,
            'post_id' => $post_id,
            'offset' => $offset,
            'count' => $count,
            'sort' => 'desc',
            'access_token' => $access_token,
        ]);
    }


    /**
     * Возвращает список комментариев к фотографии.
     * Этот метод можно вызвать с ключом доступа пользователя
     *
     * @param $ownerId - идентификатор пользователя или сообщества, которому принадлежит фотография. (для групп должен быть указан со знаком минус!)
     * @param $photo_id - идентификатор фотографии.
     * @param $ownerType - тип владельца страницы (1 = пользователь 2 = сообщество)
     * @param $access_token - наш токен для проверки
     * @param int $count
     * @param int $offset
     * @return array|mixed
     */
    public function getPhotoComments($ownerId, $photo_id, $ownerType, $access_token, $count = 100, $offset = 0)
    {
        if ($ownerType == 1) $ownerId = abs($ownerId);
        else if ($ownerType == 2) $ownerId = '-' . abs($ownerId); //для групп нужно указать с минусом!

        return $this->api('photos.getComments', [
            'owner_id' => $ownerId,
            'photo_id' => $photo_id,
            'offset' => $offset,
            'count' => $count,
            'sort' => 'desc',
            'access_token' => $access_token,
        ]);
    }


    /**
     * Возвращает список комментариев к видеозаписи.
     * можно вызвать с ключом доступа пользователя
     *
     * @param $ownerId - идентификатор пользователя или сообщества, которому принадлежит видеозапись. (для групп должен быть указан со знаком минус!)
     * @param $video_id - идентификатор видеозаписи
     * @param $ownerType - тип владельца страницы (1 = пользователь 2 = сообщество)
     * @param $access_token - наш токен для проверки
     * @param int $count
     * @param int $offset
     * @return array|mixed
     */
    public function getVideoComments($ownerId, $video_id, $ownerType, $access_token, $count = 100, $offset = 0)
    {
        if ($ownerType == 1) $ownerId = abs($ownerId);
        else if ($ownerType == 2) $ownerId = '-' . abs($ownerId); //для групп нужно указать с минусом!

        return $this->api('video.getComments', [
            'owner_id' => $ownerId,
            'video_id' => $video_id,
            'offset' => $offset,
            'count' => $count,
            'sort' => 'desc',
            'access_token' => $access_token,
        ]);
    }

    /**
     * Получает список идентификаторов пользователей, которые добавили заданный объект в свой список Мне нравится.
     * можно вызвать с сервисным ключом доступа
     * можно вызвать с ключом доступа пользователя
     *
     * @param $type - тип объекта
     * @param $ownerId - идентификатор владельца Like-объекта (для групп должен быть указан со знаком минус!)
     * @param $itemId - идентификатор Like-объекта
     * @param $ownerType - тип владельца страницы (1 = пользователь 2 = сообщество)
     * @param $access_token - наш токен для проверки
     * @param int $count
     * @param int $offset
     * @return array|mixed
     */
    public function getLikes($type, $ownerId, $itemId, $ownerType, $access_token, $count = 1000, $offset = 0)
    {
        if ($ownerType == 1) $ownerId = abs($ownerId);
        else if ($ownerType == 2) $ownerId = '-' . abs($ownerId); //для групп нужно указать с минусом!

        return $this->api('likes.getList', [
            'type' => $type,
            'owner_id' => $ownerId,
            'item_id' => $itemId,
            'filter' => 'likes',
            'offset' => $offset,
            'count' => $count,
            'access_token' => $access_token,
        ]);
    }


    /**************
     ** ПРОВЕРКИ **
     **************/

    /*
      * Возвращает информацию о том, добавлен ли текущий пользователь с ключем access_token в друзья у указанных пользователей.
      * обязательно нужно передавать ключ пользователя которого проверяем!
      * работает так же с закрытыми профилями
     */
    public function areFriends($user_ids, $access_token)
    {
        return $this->api('friends.areFriends', [
            'user_ids' => $user_ids,
            'access_token' => $access_token,
        ]);
    }

    /*
     * можно вызвать с сервисным ключом доступа.
     * можно вызвать с ключом доступа пользователя
     * Возвращает информацию о том, является ли пользователь участником сообщества.
     * !Можно вызывать с ключем для проверки заданий - check_access_token
     */
    public function isMembers($group_id, $user_ids, $access_token)
    {
        return $this->api('groups.isMember', [
            'group_id' => abs($group_id),
            'extended' => true,
            'user_ids' => $user_ids,
            'access_token' => $access_token
        ]);
    }

    /*
    * можно вызвать с сервисным ключом доступа.
    * можно вызвать с ключом доступа пользователя
    * Возвращает информацию о том, является ли пользователь участником сообщества.
    * !Можно вызывать с ключем для проверки заданий - check_access_token
    */
    public function isMember($group_id, $user_id, $access_token)
    {
        return $this->api('groups.isMember', [
            'group_id' => abs($group_id),
            'extended' => true,
            'user_id' => $user_id,
            'access_token' => $access_token
        ]);
    }

    /*
     * чекаем валидность токена
     */
    public function checkUserAccessToken($access_token)
    {
        return $this->api('users.get', [
            'access_token' => $access_token,
        ]);
    }


    /*
     * проверяет наличие указанного обекта owner_id в списке "мне нравится" указанного пользователя user_id
     * если user_id не указан то проверяет пользователя которому принедлежит токен

     * !Можно вызывать с ключем для проверки заданий - check_access_token
     */
    public function isLiked($uid, $ownerId, $itemId, $vkType, $ownerType, $access_token)
    {

        if ($ownerType == 1) $ownerId = abs($ownerId);
        else if ($ownerType == 2) $ownerId = '-' . abs($ownerId); //для групп нужно указать с минусом!

        $data = [
            'user_id' => $uid,
            'type' => $vkType,
            'owner_id' => $ownerId,
            'item_id' => $itemId,
            'access_token' => $access_token,
        ];

        return $this->api('likes.isLiked', $data);

    }


}
