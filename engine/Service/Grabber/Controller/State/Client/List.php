<?php

namespace Service\Grabber;

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
    /** @var \Lib_Curl */
    private $_curl = null;
    private $_ext = [
        'image/png' => '.png',
        'image/gif' => '.gif',
        'image/jpg' => '.jpg',
        'image/jpeg' => '.jpg',
    ];

    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        $this->_group = $this->factoryGrabber->groups->getById($this->_params['groupId']);

        if ($this->_group === null || $this->_group->userId != $this->_application->UserID) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }

        $this->_application->Title->Title = 'Граббер - ' . $this->_group->title;
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

        return null;
    }

    public function actionGet()
    {
        $sources = $this->factoryGrabber->sources->getByGroupId($this->_group->groupId);

        $posts = $this->factoryGrabber->posts->getByGroupIdIsPost($this->_group->groupId, false);
        $list = [];

        foreach ($posts as $post) {
            $list[] = $post;
        }


        $vars = [
            'group' => $this->_group,
            'list' => $sources,
            'posts' => $list,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/list', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string('');

        switch ($action) {
            case 'edit':
                return $this->_postSave();
            case 'getSourceAddFrom':
                return $this->_getSourceAddFrom();
            case 'getSourceEditFrom':
                return $this->_getSourceEditFrom();
            case 'sourceEdit':
                return $this->_sourceEdit();
            case 'sourceRefresh':
                return $this->_sourceRefresh();
            case 'sourceRemove':
                return $this->_sourceRemove();
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
        }

        return parent::actionPost();
    }

    protected function _postSave()
    {
        $postId = $this->_request->post['postId']->int(0);

        if ($postId > 0) {
            $this->_post = $this->factoryGrabber->posts->getById($postId, true);

            if ($this->_post === null || $this->_post->userId != $this->_application->UserID) {
                return $this->_response->setJson(['success' => false, 'errorText' => 'Пост не найден']);
            }
        }

        $emoji = \Service\Posting\Model_Config::GetEmoji();

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
        $this->_post->ads = $this->_request->post['ads']->bool(false);

        $attachments = $this->_post->getAttachments();

        foreach ($attachments as $id => $attachment) {
            if ($attachment['type'] == 'poll' || $attachment['type'] == 'url' || $attachment['type'] == 'link') {
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
        $this->factoryGrabber->posts->save($this->_post);

        return $this->_response->setJson(['success' => true]);
    }

    protected function _getSourceAddFrom()
    {
        $source = $this->factoryGrabber->sources->getById($this->_request->post['sourceId']->int(0));

        if ($source === null || $source->userId != $this->_application->UserID) {
            $source = $this->factoryGrabber->sources->getNew();
        }

        $vars = [
            'action' => 'sourceEdit',
            'sourceId' => 0,
            'source' => $source,
        ];

        return $this->_response->setJson(['success' => true, 'html' => \STPL::Fetch('client/sources/settings', $vars)]);
    }

    protected function _getSourceEditFrom()
    {
        $source = $this->factoryGrabber->sources->getById($this->_request->post['sourceId']->int());

        if ($source === null) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Не удалось найти источник']);
        }

        if ($source->userId != $this->_application->UserID) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Не удалось найти источник']);
        }

        $vars = [
            'action' => 'sourceEdit',
            'sourceId' => $source->sourceId,
            'source' => $source,
        ];

        return $this->_response->setJson(['success' => true, 'html' => \STPL::Fetch('client/sources/settings', $vars)]);
    }

    protected function _sourceEdit()
    {
        $source = $this->factoryGrabber->sources->getNew();

        if ($this->_request->post['sourceId']->int(0) > 0) {
            $source = $this->factoryGrabber->sources->getById($this->_request->post['sourceId']->int(0), true);

            if ($source === null) {
                return $this->_response->setJson(['success' => false, 'errorText' => 'Не удалось найти источник']);
            }

            if ($source->userId != $this->_application->UserID) {
                return $this->_response->setJson(['success' => false, 'errorText' => 'Не удалось найти источник']);
            }
        }

        if (!$source->sourceId) {
            $query = $this->factoryGrabber->sources->query()->sqlCalcFoundRows(true)->limit(1);
            $query->filter->fieldValue('userId', '=', $this->_application->UserID)
                ->fieldValue('groupId', '=', $this->_group->groupId);
            $it = $query->iterator();

            if ($it->getTotal() >= 5) {
                return $this->_response->setJson([
                    'success' => false,
                    'errorText' => 'На одну группу можно добавлять не более 5-ти источников',
                ]);
            }
        }

        $source->grabberId = 0;
        $source->userId = $this->_application->UserID;
        $source->groupId = $this->_group->groupId;
        $source->url = $this->_request->post['url']->string('');
        $source->blacklist = $this->_request->post['blacklist']->string();
        $source->delText = $this->_request->post['delText']->bool(false);
        $source->delHashtags = $this->_request->post['delHashtags']->bool(false);
        $source->delLinks = $this->_request->post['delLinks']->bool(false);
        $source->delVKLinks = $this->_request->post['delVKLinks']->bool(false);
        $source->delEmoji = $this->_request->post['delEmoji']->bool(false);
        $source->delVideo = $this->_request->post['delVideo']->bool(false);
        $source->delPoll = $this->_request->post['delPoll']->bool(false);
        $source->notPhoto = $this->_request->post['notPhoto']->bool(false);
        $source->notVideo = $this->_request->post['notVideo']->bool(false);
        $source->notMusic = true;
        $source->notDoc = $this->_request->post['notDoc']->bool(false);
        $source->notGif = $this->_request->post['notGif']->bool(false);
        $source->withPhoto = $this->_request->post['withPhoto']->bool(false);
        $source->withVideo = $this->_request->post['withVideo']->bool(false);
        $source->withDoc = $this->_request->post['withDoc']->bool(false);
        $source->withText = $this->_request->post['withText']->bool(false);
        $source->withGif = $this->_request->post['withGif']->bool(false);
        $source->filter = $this->_request->post['filter']->string('');
        $source->notAdv = $this->_request->post['notAdv']->bool(false);
        $source->notFixed = $this->_request->post['notFixed']->bool(false);
        $source->notLink = $this->_request->post['notLink']->bool(false);
        $source->notVKLink = $this->_request->post['notVKLink']->bool(false);
        $source->notTextOnly = $this->_request->post['notTextOnly']->bool(false);
        $source->notPhotoOnly = $this->_request->post['notPhotoOnly']->bool(false);
        $source->notFromGroup = $this->_request->post['notFromGroup']->bool(false);
        $source->withFromGroup = $this->_request->post['withFromGroup']->bool(false);
        $source->addCopyright = $this->_request->post['addCopyright']->bool(false);
        $source->copyrightTitle = $this->_request->post['copyrightTitle']->string('');
        $source->copyrightType = $this->_request->post['copyrightType']->int(1);
        $source->copyrightPosition = $this->_request->post['copyrightPosition']->int(1);
        $source->maxLength = $this->_request->post['maxLength']->int(0);

        $shadow = $source->getShadow();

        if ($shadow === null || $source->url != $shadow->url) {
            if (!$source->url) {
                return $this->_response->setJson(['success' => false, 'errorText' => 'Укажите адрес группы']);
            }
            $data = parse_url($source->url);
            $path = trim($data['path'], '/');

            if (strpos($path, 'public') !== false) {
                if (preg_match('@public(\d+)@', $path, $matches)) {
                    $path = $matches[1];
                }
            }

            $response = $this->VK->getGroup($path, $this->_application->settings['service']);

            if ($response['error'] > 0) {
                return $this->_response->setJson([
                    'success' => false,
                    'errorText' => 'Указаный адрес группы не верный ' . $path . ' ' . print_r($response, true),
                ]);
            }

            $source->ownerId = '-' . $response[0]['id'];
            $source->title = $response[0]['name'];
            $source->photo = $response[0]['photo_100'];

            $posts = $this->VK->getPosts($response[0]['id'], 2,$this->_application->User->access_token, 1);

            if (!empty($posts['error']) ) {

                $this->_application->Log->Log(\Service\Logs\Model_Config::SOURCE_ERROR, $this->_group->groupId,
                    $this->_application->UserID, $posts);

                if ($posts['error'] == 6) {
                    return $this->_response->setJson([
                        'success' => false,
                        'errorText' => 'Слишком большая нагрузка на сервис, попробуйте позднее',
                    ]);
                }

                return $this->_response->setJson([
                    'success' => false,
                    'errorText' => 'Указанная группа закрыта от общего доступа',
                ]);
            }
        }

        $source->isActive = true;

        if (!$source->sourceId) {
            $query = $this->factoryGrabber->sources->query()->sqlCalcFoundRows(true)->limit(1);
            $query->filter->fieldValue('userId', '=', $this->_application->UserID)
                ->fieldValue('groupId', '=', $this->_group->groupId)
                ->fieldValue('ownerId', '=', $source->ownerId);
            $it = $query->iterator();

            if ($it->getTotal() > 0) {
                return $this->_response->setJson([
                    'success' => false,
                    'errorText' => 'Указаный источник уже есть в списке источников',
                ]);
            }
        }

        if ($this->factoryGrabber->sources->save($source)) {
            return $this->_response->setJson(['success' => true]);
        }

        return $this->_response->setJson(['success' => false, 'errorText' => 'Не удалось сохранить источник']);
    }

    protected function _sourceRefresh()
    {
        $source = $this->factoryGrabber->sources->getNew();
        $sourceId = $this->_request->post['sourceId']->int(0);
        $source = $this->factoryGrabber->sources->getById($sourceId, true);

        if ($source === null) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Не удалось найти источник']);
        }

        if ($source->userId != $this->_application->UserID) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Не удалось найти источник']);
        }

        $query = $this->factoryGrabber->posts->query();
        $query->filter->fieldValue('sourceId', '=', $source->sourceId);
        $it = $query->iteratorForSave();
        /** @var Model_Posts_Post $post */
        foreach ($it as $post) {
            $post->isPost = true;
            $this->factoryGrabber->posts->save($post);
        }

        $source->count = 0;
        $this->factoryGrabber->sources->save($source);

        return $this->_response->setJson(['success' => true]);
    }

    protected function _sourceRemove()
    {
        $source = $this->factoryGrabber->sources->getNew();

        if ($this->_request->post['sourceId']->int(0) > 0) {
            $source = $this->factoryGrabber->sources->getById($this->_request->post['sourceId']->int(0), true);

            if ($source === null) {
                return $this->_response->setJson(['success' => false, 'errorText' => 'Не удалось найти источник']);
            }

            if ($source->userId != $this->_application->UserID) {
                return $this->_response->setJson(['success' => false, 'errorText' => 'Не удалось найти источник']);
            }
        }

        $this->factoryGrabber->sources->delete($source);

        return $this->_response->setJson(['success' => true]);
    }

    protected function _getGroupSettingsFrom()
    {
        if (!$this->_group->watermarkFont) {
            $this->_group->watermarkFont = 'Roboto';
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

            if ($this->factoryGrabber->groups->save($this->_group)) {
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
        $this->_group->random = $this->_request->post['random']->bool(false);

        if ($this->factoryGrabber->groups->save($this->_group)) {
            return $this->_response->setJson(['success' => true]);
        }
    }

    protected function _postEdit()
    {
        $postId = $this->_request->post['postId']->int(0);

        if (!$postId) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }
        $post = $this->factoryGrabber->posts->getById($postId, true);

        if ($post === null || $post->userId != $this->_application->UserID) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }

        $vars = [
            'action' => 'edit',
            'group' => $this->_group,
            'emoji' => \Service\Posting\Model_Config::GetEmoji(),
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
        $post = $this->factoryGrabber->posts->getById($postId, true);

        if ($post === null) {
            return $this->_response->setStatus(\System\HttpResponse::S4_BAD_REQUEST);
        }

        if ($post->userId != $this->_application->UserID) {
            return $this->_response->setStatus(\System\HttpResponse::S4_FORBIDDEN);
        }

        $post->isPost = true;
        $post->isPostDate = null;

        if ($this->factoryGrabber->posts->save($post)) {
            $source = $this->factoryGrabber->sources->getById($post->sourceId, true);

            if ($source !== null) {
                $source->count--;
                $this->factoryGrabber->sources->save($source);
            }
        }

        return $this->_response->setJson(['success' => true]);
    }

    protected function _postPublish()
    {
        $this->_curl = new \Lib_Curl();

        $postId = $this->_request->post['postId']->int(0);
        $post = $this->factoryGrabber->posts->getById($postId, true);

        if ($post === null || $post->userId != $this->_application->UserID) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Пост не найден']);
        }

        if ($post->isPost) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Пост уже опубликован']);
        }

        $group = $this->factoryGrabber->groups->getById($post->groupId, true);

        if ($group === null) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Не найдена целевая группа']);
        }

        $user = $this->_application->User;
        $user->makeShadow();

        $string = [];
        $attachments = $post->getAttachments();

        foreach ($attachments as $attachment) {
            switch ($attachment['type']) {
                case 'video':
                    $string[] = 'video' . $attachment['video']['owner_id'] . '_' . $attachment['video']['id'];
                    break;
                case 'doc':
                    $string[] = 'doc' . $attachment['doc']['owner_id'] . '_' . $attachment['doc']['id'];
                    break;
                case 'link':
                    $string[] = $attachment['link']['url'];
                    break;
                case 'poll':
                    $answers = [];

                    foreach ($attachment['poll']['answers'] as $answer) {
                        $answers[] = $answer['text'];
                    }

                    $response = $this->VK->pollsCreate($attachment['poll']['question'], $group->ownerId, 2, json_encode($answers, JSON_UNESCAPED_UNICODE), $user->access_token);

                    if ($response['id'] > 0) {
                        usleep(200000);
                        $string[] = 'poll' . $response['owner_id'] . '_' . $response['id'];
                    }
                    break;
                case 'photo':
                    $photoUrl = '';

                    //старый вариант
                    if (isset($attachment['photo']['photo_2560'])) {
                        $photoUrl = $attachment['photo']['photo_2560'];
                    } elseif (isset($attachment['photo']['photo_1280'])) {
                        $photoUrl = $attachment['photo']['photo_1280'];
                    } elseif (isset($attachment['photo']['photo_807'])) {
                        $photoUrl = $attachment['photo']['photo_807'];
                    } elseif (isset($attachment['photo']['photo_604'])) {
                        $photoUrl = $attachment['photo']['photo_604'];
                    } elseif (isset($attachment['photo']['photo_130'])) {
                        $photoUrl = $attachment['photo']['photo_130'];
                    }

                    //условие для нового API
                    if (!$photoUrl) {
                        $photo = max($attachment['photo']['sizes']); //Выбираем изображение с максимальным разрешением
                        $photoUrl = $photo['url'];
                    }

                    try {
                        $data = file_get_contents($photoUrl);
                        $image = new \Imagick();
                        $image->readImageBlob($data);
                    } catch (\Exception $e) {
                        return $this->_response->setJson([
                            'success' => false,
                            'errorText' => 'Не удалось скопировать фото по ссылке',
                        ]);
                    }

                    if ($group->isWatermark & 4) {
                        $this->_getPhotoDataText($group, $image);
                    }

                    if ($group->isWatermark & 2) {
                        $this->_getPhotoDataWatermark($group, $image);
                    }
                    $image->setImageFormat('jpg');
                    $image->writeImage(IMAGES_PATH . 'posting/temp/temp' . $this->_application->UserID . '.jpg');

                    $response = $this->VK->getWallUploadServer($group->ownerId, $user->access_token);

                    if ($response['upload_url']) {
                        $filedata = IMAGES_PATH . 'posting/temp/temp' . $this->_application->UserID . '.jpg';

                        $json = $this->VK->uploadPhotoToServerVk($response['upload_url'], $filedata);

                        if ($json['server']) {

                            $final = $this->VK->saveWallPhoto($group->ownerId, $json['photo'], $json['server'],$json['hash'], $user->access_token);

                            $string[] = 'photo' . $final[0]['owner_id'] . '_' . $final[0]['id'];
                            usleep(200000);
                        }
                    }
            }
        }

        if ($group->hashtags) {
            if ($group->hashtagsPos) {
                $post->text .= "\n" . $group->hashtags;
            } else {
                $post->text = $group->hashtags . "\n" . $post->text;
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

        $response = $this->VK->addPost($params);


        if ($response['post_id'] > 0) {
            //Успешно
            $post->isPost = true;
            $post->isPostDate = time();
        } elseif ($response['error'] == 5) {
            //Авторизация пользователя не удалась.
            $this->_application->User->makeShadow();
            $this->_application->User->token_require = true;
            $this->factoryUsers->users->save($this->_application->User);
        } else if(isset($response['error'])){
            //Если ошибку - вернем ее
            return $this->_response->setJson(['success' => false, 'errorText' => $response['errorText'], 'error' => $response['error']]);
        }

        if ($this->factoryGrabber->posts->save($post)) {
            $group->datePost = time();

            if ($group->isFree && $group->isFreeCount > 0) {
                --$group->isFreeCount;
            }
            $this->factoryGrabber->groups->save($group);
            $source = $this->factoryGrabber->sources->getById($post->sourceId, true);

            if ($source !== null) {
                ++$source->countAll;
                $this->factoryGrabber->sources->save($source);
            }
        }

        return $this->_response->setJson(['success' => true, 'errorText' => 'Пост успешно опубликован']);
    }

    private function _getPhotoDataText(Model_Groups_Group $group, $image)
    {
        $drawText = new \ImagickDraw();
        $pixelText = new \ImagickPixel($group->watermarkColor);

        /* Black text */
        $drawText->setFillColor($pixelText);
        $drawText->setFillAlpha(1 - $group->watermarkTextOpacity);

        /* Font properties */
        $drawText->setFont('Bookman-DemiItalic');
        $drawText->setFontSize($group->watermarkSize);

        $drawText->setFont(ENGINE_PATH . 'fonts/imagick/' . $group->watermarkFont . '.ttf');
        $arr = $image->queryFontMetrics($drawText, $group->watermarkText);

        $x = 0;
        $y = 0;

        switch ($group->watermarkTextPos) {
            case 0:
                $x = $image->getImageWidth() / 2 - $arr['textWidth'] / 2;
                $y = $image->getImageHeight() / 2;
                break;
            case 1:
                $x = $image->getImageWidth() - $arr['textWidth'];
                $y = $image->getImageHeight();
                break;
            case 2:
                $x = 0;
                $y = $image->getImageHeight();
                break;
            case 3:
                $x = 0;
                $y = $arr['textHeight'];
                break;
            case 4:
                $x = $image->getImageWidth() - $arr['textWidth'];
                $y = $arr['textHeight'];
                break;
            case 5:
                $x = $image->getImageWidth() / 2 - $arr['textWidth'] / 2;
                $y = $arr['textHeight'];
                break;
            case 6:
                $x = 0;
                $y = $image->getImageHeight() / 2;
                break;
            case 7:
                $x = $image->getImageWidth() - $arr['textWidth'];
                $y = $image->getImageHeight() / 2;
                break;
            case 8:
                $x = $image->getImageWidth() / 2 - $arr['textWidth'] / 2;
                $y = $image->getImageHeight();
        }
        $image->annotateimage($drawText, $x, $y, 0, $group->watermarkText);

        return $image->getImageBlob();
    }

    private function _getPhotoDataWatermark(Model_Groups_Group $group, $image)
    {
        $watermark = new \Imagick();
        $watermark->setBackgroundColor(new \ImagickPixel('transparent'));
        $watermark->readImage(IMAGES_PATH . 'grabber/watermark/' . $group->watermark);
        $w = min($watermark->getImageWidth(), $image->getImageWidth() * ($group->watermarkMaxSize / 100));
        $h = min($watermark->getImageHeight(), $image->getImageHeight());

        $src_ratio = $watermark->getImageWidth() / $watermark->getImageHeight();
        $new_w = $w;
        $new_h = $h;
        $ratio = $w / $h;

        if ($ratio < $src_ratio) {
            $new_h = $new_w / $src_ratio;
        } else {
            $new_w = $new_h * $src_ratio;
        }

        $watermark->scaleImage($new_w, $new_h);
        $watermark->evaluateImage(\Imagick::EVALUATE_MULTIPLY, 1 - $group->watermarkOpacity, \Imagick::CHANNEL_ALPHA);

        $x = 0;
        $y = 0;

        switch ($group->watermarkPos) {
            case 0:
                $x = $image->getImageWidth() / 2 - $watermark->getImageWidth() / 2;
                $y = $image->getImageHeight() / 2 - $watermark->getImageHeight() / 2;
                break;
            case 1:
                $x = $image->getImageWidth() - $watermark->getImageWidth();
                $y = $image->getImageHeight() - $watermark->getImageHeight();
                break;
            case 2:
                $x = 0;
                $y = $image->getImageHeight() - $watermark->getImageHeight();
                break;
            case 3:
                $x = 0;
                $y = 0;
                break;
            case 4:
                $x = $image->getImageWidth() - $watermark->getImageWidth();
                $y = 0;
                break;
            case 5:
                $x = $image->getImageWidth() / 2 - $watermark->getImageWidth() / 2;
                $y = 0;
                break;
            case 6:
                $x = 0;
                $y = $image->getImageHeight() / 2 - $watermark->getImageHeight() / 2;
                break;
            case 7:
                $x = $image->getImageWidth() - $watermark->getImageWidth() / 2;
                $y = $image->getImageHeight() / 2 - $watermark->getImageHeight() / 2;
                break;
            case 8:
                $x = $image->getImageWidth() / 2 - $watermark->getImageWidth() / 2;
                $y = $image->getImageHeight();
        }

        $image->compositeImage($watermark, \Imagick::COMPOSITE_DEFAULT, $x, $y);
    }
}
