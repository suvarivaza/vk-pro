<?php

namespace Service\Api;

use Imagick;
use Lib_Uuid;

use Service\Logs\Model_Config;
use System\Service_Controller_State;

class Controller_State_Client_Login extends Service_Controller_State
{
    public function actionGet()
    {
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            default:
                return $this->_login();
        }
    }

    private function _login()
    {
        $access_token = $this->_request->post['access_token']->string();
        $uid = $this->_request->post['uid']->string('');

        if (!$uid) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => 'Необходим ИД пользователя ВКонтакте',
            ]);
        }

        if (!$access_token) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Необходим токен VK']);
        }

        $response = $this->VK->getUser($uid, $access_token);

        if (!isset($response['id'])) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Ошибка доступа']);
        }

        if ($response['id'] != $uid) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Ошибка пользователя']);
        }

        $user = $this->factoryUsers->users->getByUid($uid);

        if ($user !== null) {
            $vars = [
                'success' => true,
                'token' => $user->token,
            ];

            return $this->_response->setJson($vars);
        }


        $fields = 'relation,verified,sex,bdate,city,country,domain,contacts,site,education,status,last_seen,followers_count,occupation,nickname,relatives,personal,connections,exports,wall_comments,activities,about,quotes,timezone,screen_name,maiden_name,career,military,blacklisted';
        $result = $this->VK->getUser($uid, $this->check_access_token, $fields);
        $json = $result;
        $data = $result;

        $vkDateCreate = null;
        $response = file_get_contents('http://vk.com/foaf.php?id=' . $json['uid']);

        if (preg_match('@<ya:created dc:date="(.*)"/>@', $response, $matches)) {
            $vkDateCreate = strtotime($matches[1]);
        }

        $result = file_get_contents('https://api.vk.com/method/photos.get?owner_id=' . $json['uid'] . '&album_id=profile&v=5.63&access_token=' . urlencode($this->_application->settings['token']));
        $avatarCount = json_decode($result, true);
        $avatarCount = $avatarCount['response']['count'];

        $pagesCount = $this->VK->getSubscriptions($json['uid'], $this->check_access_token);

        $posts = $this->VK->getPosts($json['uid'], 1, $this->check_access_token);

        $user = $this->factoryUsers->users->getNewUser();
        $user->access_token = '';
        $user->referrerUrl = Lib_Uuid::getNext();

        if ($this->_request->cookie['referrerUrl']->string('')) {
            $referrer = $this->factoryUsers->users->getByReferrerUrl($this->_request->cookie['referrerUrl']->string());

            if ($referrer !== null) {
                $user->parentId = $referrer->userId;

                if ($referrer->parentId > 0) {
                    $user->pParentId = $referrer->parentId;
                }

                if ($referrer->pParentId > 0) {
                    $user->ppParentId = $referrer->pParentId;
                }
            }
        }
        $user->uid = intval($json['uid']);
        $user->identity = $json['identity'];
        $user->network = $json['network'];
        $user->login = '';
        $user->password = Lib_Uuid::getNext();
        $user->ceed = Lib_Uuid::getNext();
        $user->token = md5($user->login . $user->password . $user->ceed);
        $user->userType = 0;
        $user->lastName = $json['last_name'];
        $user->firstName = $json['first_name'];
        $user->name = $user->lastName . ' ' . $user->firstName;
        $user->secondName = '';
        $user->email = $json['email'];
        $user->phone = isset($json['phone']) && $json['phone'] ? $json['phone'] : '';
        $user->icq = '';
        $user->skype = '';
        $user->dateCreate = time();
        $user->dateUpdate = time();
        $user->confirmed = $json['verified_email'] ? 1 : 0;
        $user->restore = '';
        $user->year = isset($json['bdate']) ? date('Y', strtotime($json['bdate'])) : '';
        $user->age = 0;

        if ($user->year) {
            $user->age = date('Y') - $user->year;
        }

        $user->sex = intval($json['sex']);
        $user->vkDateCreate = $vkDateCreate;

        if ($user->vkDateCreate > strtotime('-3 MONTH')) {
            $user->pageAge = 0;
        } elseif ($user->vkDateCreate > strtotime('-6 MONTH')) {
            $user->pageAge = 1;
        } elseif ($user->vkDateCreate > strtotime('-1 YEAR')) {
            $user->pageAge = 2;
        } elseif ($user->vkDateCreate > strtotime('-2 YEAR')) {
            $user->pageAge = 3;
        } elseif ($user->vkDateCreate > strtotime('-3 YEAR')) {
            $user->pageAge = 4;
        } else {
            $user->pageAge = 5;
        }

        $user->cityId = 0;
        $user->city = '';
        $user->countryId = 0;
        $user->country = '';

        if (isset($data[0]['city'])) {
            $user->cityId = intval($data[0]['city']['id']);
            $user->city = strval($data[0]['city']['title']);
        }

        if (isset($data[0]['country'])) {
            $user->countryId = intval($data[0]['country']['id']);
            $user->country = strval($data[0]['country']['title']);
        }

        $user->relation = isset($data[0]['relation']) ? intval($data['response'][0]['relation']) : 0;
        $user->followersCount = isset($data[0]['followers_count']) ? intval($data[0]['followers_count']) : 0;
        $user->avatarCount = intval($avatarCount);
        $user->partCount = 0;
        $user->pagesCount = intval($pagesCount['response']['groups']['count']);
        $user->frequency = 0;
        $user->access_token = '';

        $lastDate = null;
        if(!empty($posts['items']) and is_array($posts['items'])){
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
        }


        if (is_array($data[0]['personal']) && count($data[0]['personal'])) {
            ++$user->partCount;
        }

        if (is_array($data[0]['military']) && count($data[0]['military'])) {
            ++$user->partCount;
        }

        if (is_array($data[0]['career']) && count($data[0]['career'])) {
            ++$user->partCount;
        }

        if (is_array($data[0]['relatives']) && count($data[0]['relatives'])) {
            ++$user->partCount;
        }

        if (isset($data[0]['activities']) && strlen($data[0]['activities']) > 5) {
            ++$user->partCount;
        }

        if (isset($data[0]['about']) && strlen($data[0]['about']) > 5) {
            ++$user->partCount;
        }

        if (isset($data[0]['university']) && $data[0]['university'] > 0) {
            ++$user->partCount;
        }

        $photos = [];

        if (isset($json['photo'])) {
            $arr = explode('.', $json['photo']);
            $ext = array_pop($arr);

            $data = file_get_contents($json['photo']);
            $image = new Imagick();
            $image->readImageBlob($data);

            $path = 'users/' . rand(10, 99) . '/' . rand(10, 99) . '/' . rand(10, 99) . '/';
            $file = mb_substr(md5(Lib_Uuid::getNext()), 0, 11) . '.' . $ext;

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

        if (isset($json['photo_big'])) {
            $arr = explode('.', $json['photo_big']);
            $ext = array_pop($arr);

            $data = file_get_contents($json['photo_big']);
            $image = new Imagick();
            $image->readImageBlob($data);

            $path = 'users/' . rand(10, 99) . '/' . rand(10, 99) . '/' . rand(10, 99) . '/';
            $file = mb_substr(md5(Lib_Uuid::getNext()), 0, 11) . '.' . $ext;

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
        $user->visible = true;
        $user->country = $json['country'] ?? '';
        $user->city = $json['city'] ?? '';

        if ($this->factoryUsers->users->save($user)) {
            $mConfig = \Service\Messages\Model_Config::GetConfig();

            $message = $this->factoryMessages->users->getNew();
            $message->userId = $user->userId;
            $message->isDone = false;
            $message->type = \Service\Messages\Model_Config::TYPE_SYSTEM;
            $message->text = $mConfig['vkpro']['types']['register']['text'];
            $message->icon = 'vkpro';
            $this->factoryMessages->users->save($message);

            $this->_application->Log->Log(Model_Config::USER_CREATE, $user->userId, $user->userId,
                ['login' => $user->login, 'email' => $user->email]);

            return $this->_response->setJson(['success' => true, 'token' => $user->token]);
        }

        return $this->_response->setJson(['success' => false, 'errorText' => 'Не удалось добавить пользователя']);
    }
}
