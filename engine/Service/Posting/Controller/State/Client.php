<?php

namespace Service\Posting;

/**
 * Class Controller_State_Admin
 *
 * @package Service\Faq
 */
abstract class Controller_State_Client extends \System\Service_Controller_State
{
    /** @var Model_Posts_Post */
    protected $_post = null;

    /** @var Model_Groups_Group */
    protected $_group = null;

    /** @var Model_Postings_Posting */
    protected $_posting = null;

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

        //pd(VAR_PATH);
        $this->_application->page = 'posting';
        $this->_application->Title->addScripts([
            '/js/jquery/jquery-ui.min.js',
            '/js/posting.js?' .  filemtime(VAR_PATH . 'js/posting.js'),
        ]);

        $this->_application->Title->add('link', [
            'rel' => 'icon',
            'href' => '/img/icons/32/icon-post.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->add('link', [
            'rel' => 'shortcut icon',
            'href' => '/img/icons/32/icon-post.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->Title = 'Автопостинг';

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
            $this->_post = $this->factoryPosting->posts->getById($postId, true);

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
                }

                if ($attachment['type'] != 'photo') {
                    continue;
                }

                if ($attachment['name'] == $name) {
                    @unlink(IMAGES_PATH . '/posting/small/' . $attachment['small']['path']);
                    @unlink(IMAGES_PATH . '/posting/big/' . $attachment['big']['path']);
                    unset($attachments[$id]);
                }
            }
            $this->_post->setAttachments($attachments);
            $this->factoryPosting->posts->save($this->_post);
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
            $this->_post = $this->factoryPosting->posts->getById($postId);

            if ($this->_post === null || $this->_post->userId != $this->_application->UserID) {
                return $this->_response->setJson(['success' => false, 'errorText' => 'Пост не найден']);
            }
        }
        $attachments = $this->_post->getAttachments();
        usort($attachments, [$this, '_sort_photos']);
        $videos = $this->_request->post['videoId']->asArray([]);

        $dir = [
            'big' => IMAGES_PATH . 'temp/big/' . $uuid,
            'small' => IMAGES_PATH . 'temp/small/' . $uuid,
        ];

        if (is_dir($dir['big'])) {
            $dirs = new \DirectoryIterator($dir['big']);
            $files = [];

            foreach ($dirs as $file) {
                if (!is_file($dir['small'] . '/' . $file)) {
                    continue;
                }

                $files[] = strval($file);
            }

            sort($files);

            foreach ($files as $file) {
                $attachments[] = [
                    'type' => 'photo',
                    'id' => strval($file),
                    'name' => strval($file),
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
        $postId = $this->_request->post['postId']->int(0);

        if ($postId > 0) {
            $this->_post = $this->factoryPosting->posts->getById($postId, true);

            if ($this->_post === null || $this->_post->userId != $this->_application->UserID) {
                return $this->_response->setJson(['success' => false, 'errorText' => 'Пост не найден']);
            }
        }

        $emoji = Model_Config::GetEmoji();

        $datePost = $this->_request->post['postDate']->dateTime(time());

        $datePost += $this->_request->post['hour']->int(0) * 60 * 60;
        $datePost += $this->_request->post['minute']->int(0) * 60;
        $this->_post->datePost = $datePost;
        $this->_post->dateCreate = time();
        $text = $this->_request->post['text']->string('', null);

        $text = preg_replace('@\<img src\=\"\/img\/emoji\/(\w+|\d+)\.png\"\>@', '_$1_', $text);

        foreach ($emoji as $group) {
            $text = strtr($text, $group['replaces']);
        }

        $breaks = ['<br />' => "\r\n", '<br>' => "\r\n", '<br/>' => "\r\n"];
        $text = strtr($text, $breaks);

        $text = strip_tags($text);
        $this->_post->text = $text;
        $this->_post->signature = $this->_request->post['signature']->bool(false);

        $this->_post->userId = $this->_application->UserID;

        $this->_post->groupId = $this->_group->groupId;
        $this->_post->ads = $this->_request->post['ads']->bool(false);
        $this->_post->isPost = false;
        $this->_post->isPostDate = null;

        $attachments = $this->_post->getAttachments();

        foreach ($attachments as $id => $attachment) {
            if ($attachment['type'] == 'poll' || $attachment['type'] == 'url') {
                unset($attachments[$id]);
            }
        }

        $videos = $this->_request->post['videos']->asArray([]);
        $polls = $this->_request->post['poll']->asArray([]);

        foreach ($videos as $video) {
            $attachments[] = [
                'type' => 'video',
                'id' => $video['id'],
                'owner_id' => $video['owner_id'],
                'title' => $video['title'],
                'img' => $video['photo_320'],
                'player' => $video['player'],
            ];
        }

        $photos = $this->uploadPhotos();

        $attachments = array_merge($attachments, $photos);

        $sorts = $this->_request->post['sort']->asArray();

        foreach ($attachments as &$attachment) {
            foreach ($sorts as $id => $name) {
                if ($attachment['file'] == $name) {
                    $attachment['sort'] = $id;
                }
            }
        }

        $urls = $this->_request->post['url']->asArray();

        foreach ($urls as $url) {
            if ($url) {
                $attachments[] = [
                    'type' => 'url',
                    'url' => $url,
                ];
            }
        }

        foreach ($polls as $poll) {
            if (!$poll['title']) {
                continue;
            }

            $answers = [];

            foreach ($poll['answers'] as $answer) {
                if ($answer) {
                    $answers[] = $answer;
                }
            }

            if (!count($answers)) {
                continue;
            }
            $attachments[] = [
                'type' => 'poll',
                'title' => $poll['title'],
                'answers' => $answers,
            ];
        }

        $this->_post->setAttachments($attachments);
        $this->factoryPosting->posts->save($this->_post);

        return $this->_response->setLocation('/posting/' . $this->_post->groupId);
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
            $files = [];

            foreach ($dirs as $file) {
                if (!is_file($dir['small'] . '/' . $file)) {
                    continue;
                }
                $files[] = strval($file);
            }

            sort($files);

            foreach ($files as $file) {
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

    protected function _sort_photos($a, $b)
    {
        if ($a['type'] == 'photo' && $b['type'] != 'photo') {
            return 1;
        }

        if ($a['type'] == 'photo' && $b['type'] == 'photo') {
            return $a['sort'] > $b['sort'];
        }

        return 0;
    }
}
