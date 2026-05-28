<?php

namespace Service\Posting;

class Controller_State_Client_List extends Controller_State_Client_Default
{
    protected $_fonts = [
        'Calibri',
        'Chiller',
        'Impact',
        'Jokerman',
        'KunstlerScript',
        'Lobster',
        'ProximaNova',
        'Roboto',
        'Tahoma',
        'TimesNewRoman',
        'Verdana',
    ];
    private $_ext = [
        'image/png' => '.png',
        'image/gif' => '.gif',
        'image/jpg' => '.jpg',
        'image/jpeg' => '.jpg',
    ];
    private $_sorts = [];

    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        $this->_params['date'] = date('d.m.Y');

        $this->_group = $this->factoryPosting->groups->getById($this->_params['groupId']);

        if ($this->_group === null || $this->_group->userId != $this->_application->UserID) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }

        $del = $this->_request->get['del']->int(0);

        if ($del > 0) {
            $post = $this->factoryPosting->posts->getById($del);

            if ($post === null || $post->userId != $this->_application->UserID) {
                return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
            }

            $this->factoryPosting->posts->delete($post);

            return $this->_response->setLocation('/posting/' . $this->_group->groupId);
        }

        $this->_application->Title->addScripts(
            [
                '/js/plupload/plupload.full.min.js',
                '/js/plupload/uploader.js',
                '/js/posts/edit.min.js',
                '/js/bootstrap/bootstrap-datepicker.min.js',
                '/js/bootstrap/bootstrap-datetimepicker.min.js',
                '/js/grabber/watermark.min.js',
                '/js/bootstrap/bootstrap-colorpicker.min.js',
            ]
        );

        $this->_application->Title->addStyles([
            '/css/bootstrap/bootstrap-datepicker.min.css',
            '/css/uploader.css',
            '/css/bootstrap/bootstrap-datetimepicker.min.css',
            '/css/posting.min.css',
            '/css/bootstrap/bootstrap-colorpicker.min.css',
            '/css/fonts/stylesheet.css',
        ]);

        $this->_application->Title->addStyles(['/css/material-switch.min.css']);

        $this->_post = $this->factoryPosting->posts->getNew();
        $this->_post->datePost = time();

        $this->_application->Title->Title = 'Автопостинг - ' . $this->_group->title;

        return null;
    }

    public function actionGet()
    {
        $posts = $this->factoryPosting->posts->getByGroupIdIsPost($this->_group->groupId, false);
        $dates = [];
        $list = [];

        foreach ($posts as $post) {
            $dates[] = date('d.m.Y', $post->datePost);
            $list[] = $post;
        }

        usort($list, function ($a, $b) {
            return $a->datePost > $b->datePost;
        });

        $vars = [
            'action' => 'edit',
            'counts' => $this->factoryPosting->posts->getCounts($this->_application->UserID),
            'group' => $this->_group,
            'emoji' => Model_Config::GetEmoji(),
            'dates' => $dates,
            'list' => $list,
            'date' => $this->_params['date'],
            'post' => $this->_post,
            'uploader' => [
                'url' => $this->_request->server['REQUEST_URI']->string(),
                'uuid' => \Lib_Uuid::getNext(),
            ],
        ];

        return $this->_response->setBody(\STPL::Fetch('client/list', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'edit':
                return $this->_edit();
            case 'getGroupSettingsFrom':
                return $this->_getGroupSettingsFrom();
            case 'watermarkUpload':
                return $this->_watermarkUpload();
            case 'groupSettingsSave':
                return $this->_groupSettingsSave();
            case 'postEdit':
                return $this->_postEdit();
            case 'postDel':
                return $this->_postDel();
            case 'postPublish':
                return $this->_postPublish();
            case 'multiUploadForm':
                return $this->_multiUploadForm();
            case 'multi_upload':
                return $this->_multi_upload();
        }

        return parent::actionPost();
    }

    protected function _getGroupSettingsFrom()
    {
        if (!$this->_group->watermarkFont) {
            $this->_group->watermarkFont = 'Courier';
        }
        $sizes = [
            8,
            9,
            10,
            11,
            12,
            13,
            14,
            16,
            18,
            20,
            22,
            23,
            24,
            26,
            28,
            30,
            32,
            34,
            36,
            38,
            40,
            42,
            44,
            46,
            48,
            50,
            52,
            58,
            62,
            66,
            70,
            74,
            78,
            82,
            86,
            90,
            92,
        ];
        $vars = [
            'action' => 'groupSettingsSave',
            'group' => $this->_group,
            'fonts' => $this->_fonts,
            'sizes' => $sizes,
        ];

        return $this->_response->setJson(['success' => true, 'html' => \STPL::Fetch('client/group/settings', $vars)]);
    }

    protected function _watermarkUpload()
    {
        if (!in_array($_FILES[0]['type'], ['image/png', 'image/gif', 'image/jpg', 'image/jpeg'])) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => 'Водяной знак должен быть формата png,jpg,gif',
            ]);
        }

        $ext = $this->_ext[$_FILES[0]['type']];

        $file = \Lib_Uuid::getNext() . $ext;

        if (copy($_FILES[0]['tmp_name'], IMAGES_PATH . 'grabber/watermark/' . $file)) {
            $this->_group->makeShadow();
            $this->_group->watermark = $file;
            $this->_group->isWatermark = 1;

            if ($this->factoryPosting->groups->save($this->_group)) {
                if ($this->_group->getShadow()->isWatermark) {
                    @unlink(IMAGES_PATH . 'grabber/watermark/' . $this->_group->getShadow()->watermark);
                }
            }

            return $this->_response->setJson([
                'success' => true,
                'src' => '/img/grabber/watermark/' . $this->_group->watermark,
            ]);
        }
    }

    protected function _groupSettingsSave()
    {
        $this->_group->makeShadow();
        $this->_group->hashtags = $this->_request->post['hashtags']->string('', \System\HttpRequest::OUT_HTML_CLEAN);
        $this->_group->hashtagsPos = $this->_request->post['hashtagsPos']->int(0);
        $this->_group->timeLimit = $this->_request->post['timeLimit']->bool(false);
        $this->_group->timeHourFrom = $this->_request->post['timeHourFrom']->int(0);
        $this->_group->timeMinuteFrom = $this->_request->post['timeMinuteFrom']->int(0);
        $this->_group->timeHourTo = $this->_request->post['timeHourTo']->int(0);
        $this->_group->timeMinuteTo = $this->_request->post['timeMinuteTo']->int(0);
        $this->_group->interval = $this->_request->post['interval']->int(0);
        $this->_group->maxLength = $this->_request->post['maxLength']->int(0);
        $this->_group->photoInGroup = $this->_request->post['photoInGroup']->bool(false);
        $this->_group->adsLimit = $this->_request->post['adsLimit']->bool(false);
        $this->_group->adsInterval = $this->_request->post['adsInterval']->int(0);
        $this->_group->watermarkPos = $this->_request->post['watermarkPos']->int(0);
        $this->_group->watermarkOpacity = $this->_request->post['watermarkOpacity']->dec(0.0);
        $this->_group->watermarkMaxSize = $this->_request->post['watermarkMaxSize']->int(0);
        $isWatermark = $this->_request->post['isWatermark']->asArray([], \System\HttpRequest::INTEGER_NUM);
        $this->_group->isWatermark = array_sum($isWatermark);
        $this->_group->watermarkSize = $this->_request->post['watermarkSize']->int(8, \System\HttpRequest::INTEGER_NUM);
        $this->_group->watermarkText = $this->_request->post['watermarkText']->string('',
            \System\HttpRequest::OUT_HTML_CLEAN);
        $this->_group->watermarkTextOpacity = $this->_request->post['watermarkTextOpacity']->dec(0.0);
        $this->_group->watermarkTextPos = $this->_request->post['watermarkTextPos']->int(0,
            \System\HttpRequest::INTEGER_NUM);
        $this->_group->watermarkFont = $this->_request->post['watermarkFont']->string('',
            \System\HttpRequest::OUT_HTML_CLEAN);
        $this->_group->watermarkColor = $this->_request->post['watermarkColor']->string('',
            \System\HttpRequest::OUT_HTML_CLEAN);

        if ($this->factoryPosting->groups->save($this->_group)) {
            return $this->_response->setJson(['success' => true]);
        }
    }

    protected function _postEdit()
    {
        $postId = $this->_request->post['postId']->int(0);

        if (!$postId) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }
        $post = $this->factoryPosting->posts->getById($postId, true);

        if ($post === null || $post->userId != $this->_application->UserID) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }

        $vars = [
            'action' => 'edit',
            'group' => $this->_group,
            'emoji' => Model_Config::GetEmoji(),
            'post' => $post,
            'uploader' => [
                'url' => $this->_request->server['REQUEST_URI']->string(),
                'uuid' => \Lib_Uuid::getNext(),
            ],
        ];

        $json = [
            'html' => \STPL::Fetch('client/list/post_edit', $vars),
            'uploadForm' => \STPL::Fetch('controls/upload_dialog', $vars['uploader']),
        ];

        return $this->_response->setJson($json);
    }

    protected function _postDel()
    {
        $postId = $this->_request->post['postId']->int();

        if (!$postId) {
            return $this->_response->setStatus(\System\HttpResponse::S4_BAD_REQUEST);
        }
        $post = $this->factoryPosting->posts->getById($postId);

        if ($post === null) {
            return $this->_response->setStatus(\System\HttpResponse::S4_BAD_REQUEST);
        }

        if ($post->userId != $this->_application->UserID) {
            return $this->_response->setStatus(\System\HttpResponse::S4_FORBIDDEN);
        }

        $this->factoryPosting->posts->delete($post);

        return $this->_response->setJson(['success' => true]);
    }

    protected function _postPublish()
    {
        $curl = new \Lib_Curl();
        $postId = $this->_request->post['postId']->int();

        if (!$postId) {
            return $this->_response->setStatus(\System\HttpResponse::S4_BAD_REQUEST);
        }
        $post = $this->factoryPosting->posts->getById($postId, true);

        if ($post === null) {
            return $this->_response->setStatus(\System\HttpResponse::S4_BAD_REQUEST);
        }

        if ($post->userId != $this->_application->UserID) {
            return $this->_response->setStatus(\System\HttpResponse::S4_FORBIDDEN);
        }

        $group = $this->factoryPosting->groups->getById($post->groupId);

        if ($group === null) {
            return $this->_response->setStatus(\System\HttpResponse::S4_BAD_REQUEST);
        }

        $user = $this->_application->User;

        $string = [];
        $attachments = $post->getAttachments();

        foreach ($attachments as $attachment) {
            switch ($attachment['type']) {
                case 'video':
                    $string[] = 'video' . $attachment['owner_id'] . '_' . $attachment['id'];
                    break;
                case 'url':
                    $string[] = $attachment['url'];
                    break;
                case 'poll':

                    $response = $this->VK->pollsCreate($attachment['title'], $group->ownerId,2, json_encode($attachment['answers'], JSON_UNESCAPED_UNICODE), $user->access_token);

                    if ($response['id'] > 0) {
                        usleep(200000);
                        $string[] = 'poll' . $response['owner_id'] . '_' . $response['id'];
                    }
                    break;
                case 'photo':

                    $response = $this->VK->getWallUploadServer($group->ownerId, $user->access_token);


                    if ($response['upload_url']) {
                        $filedata = IMAGES_PATH . 'posting/big/' . $attachment['big']['path'];

                        if (function_exists('curl_file_create')) { // php 5.5+
                            $cFile = curl_file_create($filedata);
                        } else {
                            $cFile = '@' . realpath($filedata);
                        }
                        $params = [
                            'httpheader' => [
                                '"Content-Type:multipart/form-data"',
                            ],
                            'follow_redirect' => true,
                            'url' => $response['upload_url'],
                            'get_headers' => true,
                        ];
                        $params['query'] = ['photo' => $cFile];
                        $result = $curl->Query($params);
                        $json = json_decode($result->body(), true);

                        if ($json['server']) {

                            $final = $this->VK->saveWallPhoto($group->ownerId, $json['photo'], $json['server'],$json['hash'], $user->access_token);
                            $string[] = 'photo' . $final[0]['owner_id'] . '_' . $final[0]['id'];
                            usleep(200000);
                        }
                    }
            }
        }

        $params = [
            'owner_id' => '-' . $group->ownerId,
            'from_group' => 1,
            'message' => $post->text,
            'signed' => $post->signature ? 1 : 0,
            'access_token' => $user->access_token,
        ];

        if (count($string)) {
            $params['attachments'] = implode(',', $string);
        }

        if ($post->ads) {
            $params['mark_as_ads'] = 1;
        }

        $this->VK->addPost($params);

        if ($response['post_id'] > 0) {
            $post->isPost = true;
            $post->isPostDate = time();
        }

        $this->factoryPosting->posts->save($post);

        return $this->_response->setJson(['success' => true]);
    }

    protected function _multiUploadForm()
    {
        $vars = [
            'group' => $this->_group,
            'uploader' => [
                'button' => 'i_button_upload_multi',
                'container' => 'i_uploader_container_multi',
                'url' => $this->_request->server['REQUEST_URI']->string(),
                'uuid' => \Lib_Uuid::getNext(),
            ],
        ];
        $html = \STPL::Fetch('client/list/multi_upload', $vars);

        return $this->_response->setJson(['success' => true, 'title' => 'Массовая загрузка фото', 'html' => $html]);
    }

    protected function _multi_upload()
    {
        $uuid = $this->_request->post['uuid']->string();
        $count = $this->_request->post['count']->int(0);
        $frequency = $this->_request->post['frequency']->int(0);
        $from = $this->_request->post['from']->int(0);
        $to = $this->_request->post['to']->int(0);
        $sorts = $this->_request->post['sort']->asArray();

        if ($count < 1) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Укажите количество фото на пост']);
        }

        if ($to <= $from) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Укажите правильное время от и до']);
        }

        $dir = [
            'big' => IMAGES_PATH . 'temp/big/' . $uuid,
            'small' => IMAGES_PATH . 'temp/small/' . $uuid,
        ];

        if (!is_dir($dir['big'])) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Загрузите фото']);
        }

        $dirs = new \DirectoryIterator($dir['big']);
        $files = [];

        foreach ($dirs as $file) {
            if (!is_file($dir['small'] . '/' . $file)) {
                continue;
            }
            $path = strval($file);
            $arr = [
                'path' => $path,
                'sort' => 0,
            ];

            foreach ($sorts as $id => $name) {
                if ($name == $path) {
                    $arr['sort'] = $id;
                }
            }
            $files[] = $arr;
        }

        usort($files, [$this, '_sort_photos_with_sorts']);

        if (!count($files)) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Загрузите фото']);
        }

        $time = time();

        while (count($files)) {
            $time += $frequency * 60;
            $hour = date('H', $time);

            if ($hour >= $to || $hour < $from) {
                $time = mktime($from, 0, 0, date('m'), date('d', strtotime('tomorrow', $time)),
                    date('Y', strtotime('tomorrow', $time)));
            }
            $post = $this->factoryPosting->posts->getNew();
            $post->userId = $this->_application->UserID;
            $post->groupId = $this->_group->groupId;
            $post->datePost = $time;
            $post->dateCreate = time();

            $post->text = '';
            $post->signature = $this->_request->post['signature']->bool(false);
            $post->ads = false;
            $post->isPost = false;
            $post->isPostDate = null;

            $i = 0;
            $photos = [];

            foreach ($files as $id => $file) {
                $i++;
                $path = rand(10, 99) . '/' . rand(10, 99) . '/' . rand(10, 99);

                if (!is_dir(IMAGES_PATH . 'posting/big/' . $path)) {
                    mkdir(IMAGES_PATH . 'posting/big/' . $path, 0777, true);
                }

                if (!is_dir(IMAGES_PATH . 'posting/small/' . $path)) {
                    mkdir(IMAGES_PATH . 'posting/small/' . $path, 0777, true);
                }

                $photo = [
                    'type' => 'photo',
                    'file' => strval($file['path']),
                    'name' => strval($file['path']),
                    'sort' => $file['sort'],
                    'small' => [
                        'path' => $path . '/' . $file['path'],
                        'url' => '/img/posting/small/' . $path . '/' . $file['path'],
                    ],
                    'big' => [
                        'path' => $path . '/' . $file['path'],
                        'url' => '/img/posting/big/' . $path . '/' . $file['path'],
                    ],
                ];

                rename($dir['big'] . '/' . $file['path'], IMAGES_PATH . 'posting/big/' . $photo['big']['path']);
                rename($dir['small'] . '/' . $file['path'], IMAGES_PATH . 'posting/small/' . $photo['small']['path']);

                $photos[] = $photo;
                unset($files[$id]);

                if ($i == $count) {
                    break;
                }
            }

            rmdir($dir['big']);
            rmdir($dir['small']);

            $attachments = $photos;

            $post->setAttachments($attachments);
            $this->factoryPosting->posts->save($post);
        }

        return $this->_response->setJson(['success' => true]);
    }

    protected function _sort_photos_with_sorts($a, $b)
    {
        return $a['sort'] > $b['sort'];
    }
}
