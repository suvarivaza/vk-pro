<?php

namespace Service\Grabber;

/**
 * Class Controller_State_Admin
 *
 * @package Service\Faq
 */
abstract class Controller_State_Client extends \System\Service_Controller_State
{
    /** @var Model_Posts_Post */
    protected $_post = null;

    /** @var Model_Grabbers_Grabber */
    protected $_grabberGroup = null;

    /** @var \Service\Grabber\Model_Groups_Group */
    protected $_group = null;

    /** @var \Service\Grabber\Model_Grabbers_Grabber */
    protected $_grabber = null;

    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setLocation('/users/login');
        }

        if ($this->_application->User->login == '') {
            return $this->_response->setLocation('/users/register');
        }

        $this->_application->page = 'grabber';
        $this->_application->Title->addScript('/js/grabber.js?' .  filemtime(VAR_PATH . 'js/grabber.js'));

        $this->_application->Title->add('link', [
            'rel' => 'icon',
            'href' => '/img/icons/32/icon-grabber.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->add('link', [
            'rel' => 'shortcut icon',
            'href' => '/img/icons/32/icon-grabber.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->Title = 'Граббер';

        return null;
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'upload':
                return $this->_upload();
            case 'delete':
                return $this->_delete();
            case 'video_search':
                return $this->_video_search();
            case 'load_attachments':
                return $this->_load_attachments();
        }

        return $this->_response->setStatus(\System\HttpResponse::S4_BAD_REQUEST);
    }

    private function _upload()
    {
        $uuid = $this->_request->post['uuid']->string();

        $dir = [
            'big' => IMAGES_PATH . 'temp/big/' . $uuid,
            'small' => IMAGES_PATH . 'temp/small/' . $uuid,
        ];

        if (!is_dir($dir['big'])) {
            mkdir($dir['big'], 0777, true);
        }

        if (!is_dir($dir['small'])) {
            mkdir($dir['small'], 0777, true);
        }

        if (!is_file($_FILES['file']['tmp_name'])) {
            return $this->_response->setJson(['error' => 'FUCK!!!']);
        }

        $info = @getimagesize($_FILES['file']['tmp_name']);

        if ($info === false) {
            $err = error_get_last();

            return $this->_response->setJson(['error' => $err['message']]);
        }

        if (($res = \Lib_Images::PrepareResize($_FILES['file']['tmp_name'], 0, 50, 50))) {
            \Lib_Images::Resize($_FILES['file']['tmp_name'], $dir['small'] . '/' . $_FILES['file']['name'], $res);
        }

        if (($res = \Lib_Images::PrepareResize($_FILES['file']['tmp_name'], 0, 800, 800))) {
            \Lib_Images::Resize($_FILES['file']['tmp_name'], $dir['big'] . '/' . $_FILES['file']['name'], $res);
        }

        $result = [
            'success' => true,
            'jsonrpc' => '2.0',
            'key' => \Lib_Uuid::getNext(),
            'name' => $_FILES['file']['name'],
            'url' => '/img/temp/big/' . $uuid . '/' . $_FILES['file']['name'],
            'url_preview' => '/img/temp/small/' . $uuid . '/' . $_FILES['file']['name'],
        ];

        return $this->_response->setJson($result);
    }

    private function _delete()
    {
        $postId = $this->_request->post['postId']->int(0);
        $uuid = $this->_request->post['uuid']->string();
        $name = $this->_request->post['key']->string();

        if ($postId > 0) {
            $this->_post = $this->factoryGrabber->posts->getById($postId, true);

            if ($this->_post === null || $this->_post->userId != $this->_application->UserID) {
                return $this->_response->setJson(['success' => false, 'errorText' => 'Пост не найден']);
            }
        }
        $dirs = [
            IMAGES_PATH . 'temp/big/' . $uuid . '/',
            IMAGES_PATH . 'temp/small/' . $uuid . '/',
            IMAGES_PATH . 'news/big/',
            IMAGES_PATH . 'news/small/',
        ];

        foreach ($dirs as $dir) {
            if (is_file($dir . $name)) {
                @unlink($dir . $name);
            }
        }

        if ($this->_post->postId) {
            $this->_post->makeShadow();
            $attachments = $this->_post->getAttachments();

            foreach ($attachments as $id => $attachment) {
                if ($attachment['type'] == 'video' && $attachment['video']['id'] == $name) {
                    unset($attachments[$id]);

                    continue;
                }

                if ($attachment['type'] == 'doc' && $attachment['doc']['id'] == $name) {
                    unset($attachments[$id]);

                    continue;
                }

                if ($attachment['type'] != 'photo') {
                    continue;
                }

                if ($attachment['name'] == $name) {
                    @unlink(IMAGES_PATH . '/posting/small/' . $attachment['small']['path']);
                    @unlink(IMAGES_PATH . '/posting/big/' . $attachment['big']['path']);
                    unset($attachments[$id]);

                    continue;
                }

                if ($attachment['id'] == $name) {
                    @unlink(IMAGES_PATH . '/posting/small/' . $attachment['small']['path']);
                    @unlink(IMAGES_PATH . '/posting/big/' . $attachment['big']['path']);
                    unset($attachments[$id]);

                    continue;
                }

                if ($attachment['photo']['id'] == $name) {
                    unset($attachments[$id]);

                    continue;
                }
            }
            $this->_post->setAttachments($attachments);
            $this->factoryGrabber->posts->save($this->_post);
        }

        return $this->_response->setJson(['success' => true]);
    }

    private function _video_search()
    {
        $query = $this->_request->post['query']->string();

        if (!$query) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Укажите текст для поиска']);
        }

        $response = $this->VK->api('video.search', [
            'q' => $query,
            'sort' => 0,
            'extended' => 1,
            'access_token' => $this->_application->User->access_token,
        ]);

        if (isset($response['count']) && $response['count'] == 0) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => 'По запросу видео не найдено. Попробуйте изменить строку поиска',
            ]);
        }

        if (isset($response['count']) && $response['count'] > 0) {
            $vars = $response;

            return $this->_response->setJson(['success' => true, 'html' => \STPL::Fetch('client/video', $vars)]);
        }

        return $this->_response->setJson(['success' => false, 'errorText' => print_r($response, true)]);
    }

    private function _load_attachments()
    {
        $uuid = $this->_request->post['uuid']->string();
        $postId = $this->_request->post['postId']->int(0);

        if ($postId > 0) {
            $this->_post = $this->factoryGrabber->posts->getById($postId);

            if ($this->_post === null || $this->_post->userId != $this->_application->UserID) {
                return $this->_response->setJson(['success' => false, 'errorText' => 'Пост не найден']);
            }
        }

        if ($this->_post === null) {
            return $this->_response->setJson(['success' => true, 'html' => '']);
        }

        $attachments = $this->_post->getAttachments();
        $videos = $this->_request->post['videoId']->asArray([]);

        $dir = [
            'big' => IMAGES_PATH . 'temp/big/' . $uuid,
            'small' => IMAGES_PATH . 'temp/small/' . $uuid,
        ];

        if (is_dir($dir['big'])) {
            $dirs = new \DirectoryIterator($dir['big']);

            foreach ($dirs as $file) {
                if (!is_file($dir['small'] . '/' . $file)) {
                    continue;
                }

                $attachments[] = [
                    'type' => 'photo',
                    'id' => $file,
                    'small' => [
                        'url' => '/img/temp/small/' . $uuid . '/' . $file,
                    ],
                    'big' => [
                        'url' => '/img/temp/big/' . $uuid . '/' . $file,
                    ],
                ];
            }
        }
        $vlist = [];

        foreach ($attachments as $attachment) {
            if ($attachment['type'] == 'video') {
                $vlist[] = $attachment;
            }
        }

        foreach ($videos as $video) {
            $vlist[] = [
                'id' => $video['id'],
                'img' => $video['photo_320'],
                'title' => $video['title'],
                'player' => $video['player'],
            ];
        }
        $vars = [
            'uuid' => $uuid,
            'attachments' => $attachments,
            'videos' => $vlist,
        ];

        return $this->_response->setJson(['success' => true, 'html' => \STPL::Fetch('client/attachments', $vars)]);
    }

    protected function _edit()
    {
        $this->_grabberGroup->source = $this->_request->post['source']->string();
        $this->_grabberGroup->interval = $this->_request->post['days']->int(0) * 24 + $this->_request->post['hour']->int(0);
        $this->_grabberGroup->linkDelete = $this->_request->post['linkDelete']->bool(false);
        $this->_grabberGroup->hashtags = $this->_request->post['hashtags']->string('', \System\HttpRequest::OUT_HTML);

        $path = '';
        $data = parse_url($this->_grabberGroup->source);

        if (isset($data['query'])) {
            parse_str($data['query'], $query);

            if (isset($query['z'])) {
                list($path) = explode('/', $query['z']);
            } elseif (isset($query['w'])) {
                $path = $query['w'];
            }
        }

        if (!$path) {
            $path = trim($data['path'], '/');
        }

        if (!$path) {
            $this->_errors[] = 'Не удалось определить страницу';

            return null;
        }

        if (preg_match('@^(public|club)(\d+)$@', $path, $matches)) {
            $path = $matches[2];
        }


        $result = $this->VK->getGroup(urlencode($path), $this->check_access_token);

        if (!isset($result['response'][0]['id'])) {
            $this->_errors[] = 'Не правильный УРЛ страницы';

            return null;
        } else {
            $this->_grabberGroup->ownerId = strval($result['response'][0]['gid']);
            $this->_grabberGroup->itemId = '';
            $this->_grabberGroup->title = $result['response'][0]['name'];

            if (isset($result['response'][0]['photo_medium']) && $result['response'][0]['photo_medium'] != '') {
                $photo = [
                    'small' => $this->savePhoto($result['response'][0]['photo']),
                    'big' => $this->savePhoto($result['response'][0]['photo_medium']),
                ];
                $this->_grabberGroup->setPhoto($photo);
            }
        }

        $this->factoryGrabber->groups->save($this->_grabberGroup);

        return $this->_response->setLocation('/grabber/' . $this->_group->groupId);
    }

    protected function savePhoto($url)
    {
        $arr = explode('.', $url);
        $ext = array_pop($arr);

        $data = file_get_contents($url);
        $image = new \Imagick();
        $image->readImageBlob($data);

        $path = 'grabber/' . rand(10, 99) . '/' . rand(10, 99) . '/' . rand(10, 99) . '/';
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

        return $photo;
    }

    protected function uploadPhotos()
    {
        $uuid = $this->_request->post['uuid']->string();

        $dir = [
            'big' => IMAGES_PATH . 'temp/big/' . $uuid,
            'small' => IMAGES_PATH . 'temp/small/' . $uuid,
        ];

        $photos = [];

        if (is_dir($dir['big'])) {
            $dirs = new \DirectoryIterator($dir['big']);

            foreach ($dirs as $file) {
                if (!is_file($dir['small'] . '/' . $file)) {
                    continue;
                }

                $path = rand(10, 99) . '/' . rand(10, 99) . '/' . rand(10, 99);

                if (!is_dir(IMAGES_PATH . 'posting/big/' . $path)) {
                    mkdir(IMAGES_PATH . 'posting/big/' . $path, 0777, true);
                }

                if (!is_dir(IMAGES_PATH . 'posting/small/' . $path)) {
                    mkdir(IMAGES_PATH . 'posting/small/' . $path, 0777, true);
                }

                $photo = [
                    'type' => 'photo',
                    'file' => strval($file),
                    'name' => strval($file),
                    'small' => [
                        'path' => $path . '/' . $file,
                        'url' => '/img/posting/small/' . $path . '/' . $file,
                    ],
                    'big' => [
                        'path' => $path . '/' . $file,
                        'url' => '/img/posting/big/' . $path . '/' . $file,
                    ],
                ];

                rename($dir['big'] . '/' . $file, IMAGES_PATH . 'posting/big/' . $photo['big']['path']);
                rename($dir['small'] . '/' . $file, IMAGES_PATH . 'posting/small/' . $photo['small']['path']);

                $photos[] = $photo;
            }

            foreach ($dir as $path) {
                rmdir($path);
            }
        }

        return $photos;
    }
}
