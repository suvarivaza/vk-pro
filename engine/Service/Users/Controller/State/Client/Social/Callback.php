<?php

namespace Service\Users;

class Controller_State_Client_Social_Callback extends Controller_State_Client
{
    public function actionGet()
    {
        // Вернет код. АПИ - https://vk.com/dev/authcode_flow_user
        //Пример запроса: https://oauth.vk.com/authorize?client_id=7909307&redirect_uri=http://vk-pro.top/users/social/callback&scope=offline,video,photos,wall,groups,stats&response_type=code&state=/&v=5.64
        $code = $this->_request->get['code']->string('');

        if (!$code) {
            return $this->_response->setLocation('/');
        }

        $response = file_get_contents('https://oauth.vk.com/access_token?client_id=' . VK_ID . '&client_secret=' . $this->_application->settings['secret'] . '&redirect_uri=' . urlencode(VK_REDIRECT_URL) . '&code=' . $code);
        $json = json_decode($response, true);

        if (!isset($json['access_token']) || !isset($json['user_id'])) {
            return $this->_response->setLocation('/');
        }

        $vkUserId = $json['user_id'];
        $accessToken = $json['access_token'];
        $expiresIn = $json['expires_in'] ?? 0;

        // Проверяем, существует ли пользователь
        $user = $this->factoryUsers->users->getByUid($vkUserId, true);

        if ($user === null) {
            // Создаем нового пользователя
            $this->VK->init($this->factoryUsers->users->getById(random_int(1, 3)));

            $fields = 'relation,verified,sex,bdate,city,country,domain,contacts,site,education,status,last_seen,followers_count,occupation,nickname,relatives,personal,connections,exports,wall_comments,activities,about,quotes,timezone,screen_name,maiden_name,career,military,blacklisted,photo_200';
            $vkUserData = $this->VK->getUser($vkUserId, $this->check_access_token, $fields);

            if (!$vkUserData || !isset($vkUserData[0])) {
                return $this->_response->setLocation('/');
            }

            $data = $vkUserData[0];

            $vkDateCreate = null;
            $response = @file_get_contents('http://vk.com/foaf.php?id=' . $vkUserId);
            if ($response && preg_match('@<ya:created dc:date="(.*)"/>@', $response, $matches)) {
                $vkDateCreate = strtotime($matches[1]);
            }

            $posts = $this->VK->getPosts($vkUserId, 1, $this->check_access_token);
            $avatarCount = $posts['count'] ?? 0;

            $pagesCount = $this->VK->getSubscriptions($vkUserId, $this->check_access_token);
            $friends = $this->VK->getFriends($vkUserId, $this->check_access_token);

            $user = $this->factoryUsers->users->getNewUser();
            $user->access_token = $accessToken;
            $user->access_token_expire = $expiresIn > 0 ? time() + $expiresIn : null;
            $user->referrerUrl = \Lib_Uuid::getNext();

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

            $user->uid = intval($vkUserId);
            $user->identity = 'https://vk.com/id' . $vkUserId;
            $user->network = 'vkontakte';
            $user->login = '';
            $user->password = \Lib_Uuid::getNext();
            $user->ceed = \Lib_Uuid::getNext();
            $user->token = md5($user->login . $user->password . $user->ceed);
            $user->userType = 0;
            $user->lastName = $data['last_name'] ?? '';
            $user->firstName = $data['first_name'] ?? '';
            $user->name = trim($user->lastName . ' ' . $user->firstName);
            $user->secondName = '';
            $user->email = $json['email'] ?? '';
            $user->phone = '';
            $user->icq = '';
            $user->skype = '';
            $user->dateCreate = time();
            $user->dateUpdate = time();
            $user->confirmed = 1;
            $user->restore = '';

            if (isset($data['bdate'])) {
                $bdateParts = explode('.', $data['bdate']);
                if (count($bdateParts) == 3) {
                    $user->year = intval($bdateParts[2]);
                    $user->age = date('Y') - $user->year;
                } else {
                    $user->year = '';
                    $user->age = 0;
                }
            } else {
                $user->year = '';
                $user->age = 0;
            }

            $user->sex = intval($data['sex'] ?? 0);
            $user->vkDateCreate = $vkDateCreate;

            if ($vkDateCreate) {
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
            } else {
                $user->pageAge = 0;
            }

            $user->cityId = intval($data['city']['id'] ?? 0);
            $user->city = strval($data['city']['title'] ?? '');
            $user->countryId = intval($data['country']['id'] ?? 0);
            $user->country = strval($data['country']['title'] ?? '');
            $user->relation = intval($data['relation'] ?? 0);

            $followersCount = ($friends['count'] ?? 0) + ($data['followers_count'] ?? 0);
            $user->followersCount = $followersCount;
            $user->avatarCount = intval($avatarCount);
            $user->partCount = 0;
            $user->pagesCount = intval($pagesCount['groups']['count'] ?? 0);
            $user->frequency = 0;

            $lastDate = null;
            if (isset($posts['items'])) {
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

            if (isset($data['personal']) && is_array($data['personal']) && count($data['personal'])) {
                ++$user->partCount;
            }
            if (isset($data['military']) && is_array($data['military']) && count($data['military'])) {
                ++$user->partCount;
            }
            if (isset($data['career']) && is_array($data['career']) && count($data['career'])) {
                ++$user->partCount;
            }
            if (isset($data['relatives']) && is_array($data['relatives']) && count($data['relatives'])) {
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

            $photos = [];
            if (isset($data['photo_200'])) {
                $photoUrl = $data['photo_200'];
                $arr = explode('.', $photoUrl);
                $ext = array_pop($arr);

                $photoData = @file_get_contents($photoUrl);
                if ($photoData) {
                    try {
                        $image = new \Imagick();
                        $image->readImageBlob($photoData);

                        $path = 'users/' . rand(10, 99) . '/' . rand(10, 99) . '/' . rand(10, 99) . '/';
                        $file = mb_substr(md5(\Lib_Uuid::getNext()), 0, 11) . '.' . $ext;

                        if (!is_dir(IMAGES_PATH . $path)) {
                            @mkdir(IMAGES_PATH . $path, 0777, true);
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
                        $photos['big'] = $photo;
                    } catch (\Exception $e) {
                        // Игнорируем ошибки обработки изображения
                    }
                }
            }

            $user->setPhotos($photos);
            $user->visible = true;

            if ($this->factoryUsers->users->save($user)) {
                $mConfig = \Service\Messages\Model_Config::GetConfig();

                $message = $this->factoryMessages->users->getNew();
                $message->userId = $user->userId;
                $message->isDone = false;
                $message->type = \Service\Messages\Model_Config::TYPE_SYSTEM;
                $message->text = $mConfig['vkpro']['types']['register']['text'];
                $message->icon = 'vkpro';
                $this->factoryMessages->users->save($message);

                $this->_application->Log->Log(\Service\Logs\Model_Config::USER_CREATE, $user->userId, $user->userId,
                    ['login' => $user->login, 'email' => $user->email]);
                $this->_application->setUserToCookie($user);
            }
        } else {
            // Обновляем токен существующего пользователя
            if (!$user->access_token) {
                $user->access_token = $accessToken;
            }

            if ($expiresIn > 0) {
                $user->access_token_expire = time() + $expiresIn;
            } else {
                $user->access_token_expire = null;
            }

            if (!$user->referrerUrl) {
                $user->referrerUrl = \Lib_Uuid::getNext();
            }

            $this->factoryUsers->users->save($user);
            $this->_application->Log->Log(\Service\Logs\Model_Config::USER_LOGIN, $user->userId, $user->userId,
                ['login' => $user->login, 'email' => $user->email]);
            $this->_application->setUserToCookie($user);
        }

        if ($this->_request->get['state']->string('') == 'close') {
            return $this->_response->setBody('<script>window.close();</script>');
        }

        if ($this->_request->get['state']->string('') != '') {
            return $this->_response->setLocation($this->_request->get['state']->string());
        }

        return $this->_response->setLocation('/tasks/all');
    }

    public function actionPost()
    {
        return null;
    }
}
