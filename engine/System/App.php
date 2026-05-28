<?php

namespace System;

use Lib_VK;

class App
{
    /** @var int */
    public $Terminal;

    /** @var \Lib_Html_Titles */
    public $Title;

    /** @var int|null */
    public $UserID;

    /** @var \Service\Users\Model_Users_User */
    public $User;

    public $searchValue = '';

    public $menu = [];

    public $theme = 'blank';

    public $admin = false;

    public $page = '';
    public $userPage = '';

    public $settings = [];
    public $notifications = [];

    public $Log = null;

    public $messages = [];

    private $tokens = ['token', 'token2', 'token3', 'token4', 'token5'];

    public $check_access_token = null;

    public function __construct()
    {
        $factory = new \Service\Logs\Model_Factory();
        $this->Log = $factory->getLogs();

        $this->Title = new \Lib_Html_Titles();

        $this->menu = \Service\System\Model_Config::$menu;
        $this->settings = $this->getSettings();

        //устанавливаем check_access_token здесь - после установки settings!
        $this->check_access_token = $this->getRandomCheckToken();

        $this->getUserFromCookie();
        $this->notifications = $this->getNotifications();
    }

    private function getNotifications()
    {
        $notifications = [];

        if ($this->UserID > 0) {
            $factory = new \Service\Users\Model_Factory();
            $query = $factory->notifications->query();
            $query->filter->fieldValue('userId', '=', $this->UserID);
            $query->filter->fieldValue('status', '=', 0);
            $it = $query->iterator();
            /** @var \Service\Users\Model_Notifications_Notification $notification */
            foreach ($it as $notification) {
                $notifications[$notification->service][] = $notification;
            }
        }

        return $notifications;
    }

    public function UserIsRegister()
    {
        if ($this->UserID == 0) {
            return false;
        }

        if (!$this->User->login) {
            return false;
        }

        return true;
    }

    public function UserIsAuth()
    {
        if ($this->UserID == 0) {
            return false;
        }

        return true;
    }

    public function getSettings()
    {
        $factory = new \Service\System\Model_Factory();
        $it = $factory->settings->getAll();
        $list = [];

        foreach ($it as $setting) {
            $list[$setting->name] = $setting->value;
        }

        return $list;
    }

    /*
     * Проверка качества аккаунта ВК
     */
    public function updateUserFromVK()
    {
        $user = $this->User;

        Lib_VK::init($user);
        $VK = new Lib_VK();

        $fields = 'photo_id, sex, bdate, city, country, photo_50, education, universities, schools, status, last_seen, followers_count, occupation, nickname, relatives, relation, personal, connections, exports, activities, interests, music, movies, tv, books, games, about, quotes, timezone, screen_name, maiden_name, crop_photo, friend_status, career, military, blacklisted,photo_big,crop_photo';
        $result = $VK->getUser($user->uid, $this->settings['service'], $fields);

        $data = $result[0];

        //получаем фото пользователя
        $result = $VK->getAllPhotosUser($user->uid, $this->check_access_token);

        $avatarCount = $result['count'];

        //получаем подписки пользователя
        $pagesCount = $VK->getSubscriptions($user->uid, $this->settings['service']);


        //получаем друзей пользователя
        $friends = $VK->getFriends($user->uid, $this->check_access_token);

        $friendsCount = intval($friends['count']);

        //получаем посты пользователя
        $posts = $VK->getPosts($user->uid, 1, $this->check_access_token);


        $lastDate = null;
        foreach ($posts['items'] as $post) {
            if ($lastDate === null) {
                $user->frequency = 1;
                $lastDate = $post['date'];
            } else {
                if ($post['date'] > $lastDate - 86400) {
                    ++$user->frequency;
                }
            }
        }

        if (isset($data['city'])) {
            $user->cityId = intval($data['city']['id']);
            $user->city = strval($data['city']['title']);
        }

        if (isset($data['country'])) {
            $user->countryId = intval($data['country']['id']);
            $user->country = strval($data['country']['title']);
        }

        $user->relation = isset($data['relation']) ? intval($data['relation']) : 0;
        $user->followersCount = $friendsCount + (isset($data['followers_count']) ? intval($data['followers_count']) : 0);
        $user->avatarCount = intval($avatarCount);
        $user->partCount = 0;
        $user->pagesCount = intval($pagesCount['groups']['count']);
        $user->frequency = 0;

        if (is_array($data['personal']) && count($data['personal'])) {
            ++$user->partCount;
        }

        if (isset($data['interests']) && strlen($data['interests']) > 3) {
            ++$user->partCount;
        }

        if (isset($data['music']) && strlen($data['music']) > 3) {
            ++$user->partCount;
        }

        if (isset($data['movies']) && strlen($data['movies']) > 3) {
            ++$user->partCount;
        }

        if (isset($data['tv']) && strlen($data['tv']) > 3) {
            ++$user->partCount;
        }

        if (isset($data['books']) && strlen($data['books']) > 3) {
            ++$user->partCount;
        }

        if (isset($data['games']) && strlen($data['games']) > 3) {
            ++$user->partCount;
        }

        if (is_array($data['military']) && count($data['military'])) {
            ++$user->partCount;
        }

        if (is_array($data['career']) && count($data['career'])) {
            ++$user->partCount;
        }

        if (is_array($data['relatives']) && count($data['relatives'])) {
            ++$user->partCount;
        }

        if (isset($data['activities']) && strlen($data['activities']) > 5) {
            ++$user->partCount;
        }

        if (isset($data['about']) && strlen($data['about']) > 5) {
            ++$user->partCount;
        }

        if (isset($data['university']) && $data['university'] > 0) {
            ++$user->partCount;
        }

        if ($avatarCount > 0) {
            ++$user->partCount;
        }

        if ($friendsCount > 0) {
            ++$user->partCount;
        }

        if ($pagesCount > 0) {
            ++$user->partCount;
        }

        $photos = [];

        if (isset($data['photo_50'])) {
            $temp = explode('?', $data['photo_50']);
            $arr = explode('.', $temp[0]);
            $ext = array_pop($arr);

            $content = file_get_contents($data['photo_50']);

            $image = new \Imagick();
            $image->readImageBlob($content);

            $path = 'users/' . rand(10, 99) . '/' . rand(10, 99) . '/' . rand(10, 99) . '/';
            $file = mb_substr(md5(\Lib_Uuid::getNext()), 0, 11) . '.' . $ext;

            if (!is_dir($path)) {
                mkdir(IMAGES_PATH . $path, 0777, true);
            }

            $image->writeImage(IMAGES_PATH . $path . $file);
            $photo = [
                'path' => $path,
                'file' => $file,
                'url' => '/img/' . $path . $file,
                'w' => $image->getImageWidth(),
                'h' => $image->getImageHeight(),
            ];
            $photos['small'] = $photo;
        }

        if (isset($data['photo_big'])) {
            $temp = explode('?', $data['photo_big']);
            $arr = explode('.', $temp[0]);
            $ext = array_pop($arr);

            $content = file_get_contents($data['photo_big']);
            $image = new \Imagick();
            $image->readImageBlob($content);

            $path = 'users/' . rand(10, 99) . '/' . rand(10, 99) . '/' . rand(10, 99) . '/';
            $file = mb_substr(md5(\Lib_Uuid::getNext()), 0, 11) . '.' . $ext;

            if (!is_dir(IMAGES_PATH . $path)) {
                mkdir(IMAGES_PATH . $path, 0777, true);
            }

            $image->writeImage(IMAGES_PATH . $path . $file);
            $photoBig = [
                'path' => $path,
                'file' => $file,
                'url' => '/img/' . $path . $file,
                'w' => $image->getImageWidth(),
                'h' => $image->getImageHeight(),
            ];
            $photos['big'] = $photoBig;
        }

        $user->setPhotos($photos);
        $user->lastCheck = time();
        $factoryUsers = new \Service\Users\Model_Factory();

        $user->bad = 0;

        //нет аватарки
        if (strpos($data['photo_big'], 'camera') !== false) {
            $user->bad |= \Service\Users\Model_Config::BAD_AVATAR;
        }

        //если меньше 5 постов
        if ($posts['count'] < 5) {
            $user->bad |= \Service\Users\Model_Config::BAD_POSTS;
        }

        if ($user->avatarCount < 3) {
            $user->bad |= \Service\Users\Model_Config::BAD_AVATAR_COUNT;
        }

        //меньше 3 друзей
        if ($user->followersCount < 3) {
            $user->bad |= \Service\Users\Model_Config::BAD_FOLLOWERS;
        }

        //если пользователь забанен в ВК баним у нас и списываем карму
        if (isset($data['deactivated']) && $data['deactivated'] == 'banned') {
            if (!$user->ban) {
                $user->ban = true;
                $user->banDate = time();
                $karmaObj = $factoryUsers->users->karma->getNew();
                $karmaObj->userId = $user->userId;
                $karmaObj->karma = -500.00;
                $karmaObj->karmaFrom = $user->karma;
                $user->karma = -500.00;
                $karmaObj->karmaTo = $user->karma;
                $karmaObj->dateCreate = time();
                $karmaObj->comment = 'Списывание кармы за бан в ВК';
                $factoryUsers->users->karma->save($karmaObj);

                $factoryMessages = new \Service\Messages\Model_Factory();
                $message = $factoryMessages->users->getNew();
                $message->userId = $user->userId;
                $message->isDone = false;
                $message->type = \Service\Messages\Model_Config::TYPE_SYSTEM;
                $text = 'Вам был начислен штраф за бан страницы в ВК';
                $message->text = $text;
                $message->icon = 'vkpro';
                $factoryMessages->users->save($message);
            }
        } else {
            $user->ban = false;
            $user->banDate = null;
        }
        $factoryUsers->users->save($user);
    }

    public function getUserFromCookie()
    {
        $request = new \System\HttpRequest('', $_GET, $_POST, $_COOKIE, $_SERVER);
        $factory = new \Service\Users\Model_Factory();

        if ($request->cookie['auth']->string('')) {
            $this->User = $factory->users->getByToken($request->cookie['auth']->string());

            if ($this->User !== null) {
                $this->User->makeShadow();
                $login = strtotime('midnight', $this->User->lastLogin);
                $now = strtotime('midnight');

                if ($now > $login) {

                    $mConfig = \Service\Messages\Model_Config::GetConfig();
                    $factoryMessages = new \Service\Messages\Model_Factory();
                    $message = $factoryMessages->users->getNew();
                    $message->userId = $this->User->userId;
                    $message->isDone = false;
                    $message->type = \Service\Messages\Model_Config::TYPE_SYSTEM;
                    $text = $mConfig['vkpro']['types']['day_first']['text'];
                    $text = str_replace('%name%', $this->User->firstName, $text);
                    $message->text = $text;
                    $message->icon = 'vkpro';
                    $factoryMessages->users->save($message);

                    if (!$this->User->lastLogin) {
                        $this->updateUserFromVK();
                    }

                    $key = date('Y-m-d');
                    $userOnline = $factory->online->getByKey($key, true);

                    if (!$userOnline) {
                        $userOnline = $factory->online->getNew();
                        $userOnline->date = $key;
                        $userOnline->count = 0;
                    }
                    $userOnline->count++;
                    $factory->online->save($userOnline);
                }

                $this->User->lastLogin = time();

                try {
                    $factory->users->save($this->User);
                } catch (\Exception $e) {
                }
                $this->UserID = $this->User->userId;
                $this->getMessages();
                $this->setUserToCookie($this->User);
            }
        }
    }

    public function setUserToCookie(\Service\Users\Model_Users_User $user)
    {
        setcookie('auth', $user->token, time() + (10 * 60 * 60 * 24), '/');
    }

    public function delUserFromCookie()
    {
        setcookie('auth', '', time() - 3600, '/');
    }

    public function getMessages()
    {
    }

    /*
    * Получает один из наших токенов для проверки
    */
    public function getRandomCheckToken()
    {
        $tokenNumber = rand(0, count($this->tokens) - 1);
        $tokenName = $this->tokens[$tokenNumber];
        return $this->settings[$tokenName];
    }
}
