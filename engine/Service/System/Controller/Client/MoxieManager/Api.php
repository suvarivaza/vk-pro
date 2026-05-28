<?php

namespace Service\System;

class Controller_Client_MoxieManager_Api extends Controller_Client
{
    public function actionPrepare()
    {
        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setStatus(\System\HttpResponse::S4_FORBIDDEN);
        }

        if (!in_array($this->_application->User->userType,
                [\Service\Users\Model_Config::TYPE_ADMIN]) && !$this->_application->User->visible) {
            return $this->_response->setStatus(\System\HttpResponse::S4_FORBIDDEN);
        }

        return parent::actionPrepare();
    }

    /**
     * Обработчик GET-запросов
     *
     * @return mixed
     */
    public function actionGet()
    {
        $action = $this->_request->get['action']->string();

        switch ($action) {
            case 'language':
                return $this->_language();
            case 'auth':
                return $this->_auth();
            case 'upload':
                return $this->_upload();
            case 'streamfile':
                return $this->_streamfile();
        }
        $csrf = $this->_request->post['csrf']->string();

        if (!$csrf || $csrf != $_SESSION['moxie_token']) {
            return $this->_response->setStatus(\System\HttpResponse::S4_FORBIDDEN);
        }

        $json = $this->_request->post['json']->string();
        $json = json_decode($json, true);

        if (!$json) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_ACCEPTABLE);
        }

        switch ($json['method']) {
            case 'listRoots':
                return $this->_listRoots($json);
            case 'listFiles':
                return $this->_listFiles($json);
            case 'FileInfo':
                return $this->_FileInfo($json);
            case 'delete':
                return $this->_delete($json);
            case 'moveTo':
                return $this->_moveTo($json);
            case 'copyTo':
                return $this->_copyTo($json);
            case 'createDirectory':
                return $this->_createDirectory($json);
        }

        return $this->_response->setStatus(\System\HttpResponse::S4_BAD_REQUEST);
    }

    private function _language()
    {
        if ($this->_request->get['tinymce']->bool()) {
            $data = file_get_contents(SCRIPTS_PATH . 'jquery/tinymce/plugins/moxiemanager/langs/ru.js');
        } else {
            $data = file_get_contents(SCRIPTS_PATH . 'jquery/tinymce/plugins/moxiemanager/langs/moxman_ru.js');
        }

        return $this->_response->setContentType('text/javascript')->setBody($data);
    }

    private function _auth()
    {
        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setStatus(\System\HttpResponse::S4_FORBIDDEN);
        }

        if (!isset($_SESSION['moxie_token'])) {
            $_SESSION['moxie_token'] = \Lib_Uuid::getNext();
        }

        $vars = [
            'token' => $_SESSION['moxie_token'],
            'installed' => true,
            'loggedin' => true,
            'loginurl' => '/users/login',
            'standalone' => false,
            'overwrite_action' => '',
            'dropbox.app_id' => '',
            'googledrive.client_id' => '',
        ];

        return $this->_response->setJson($vars);
    }

    private function _upload()
    {
        /*
         * {"jsonrpc":"2.0","error":{"code":100,"message":"This action is restricted in demo mode.","data":null},"id":null}
         */
        $vars = [
            'jsonrpc' => '2.0',
            'id' => $this->_request->get['id']->string(),
        ];
        $csrf = $this->_request->get['csrf']->string();

        if (!$csrf || $csrf != $_SESSION['moxie_token']) {
            return $this->_response->setStatus(\System\HttpResponse::S4_FORBIDDEN);
        }

        $path = str_replace('../', '', trim($this->_request->get['path']->string(), "\/."));
        $pathTo = IMAGES_PATH . str_replace('../', '',
                trim($this->_request->get['path']->string() . '/' . $this->_request->get['name']->string(), "\/."));
        $resolution = $this->_request->get['resolution']->string();

        if ($resolution == 'default') {
            if (copy($_FILES['file']['tmp_name'], $pathTo)) {
                $params = [
                    'jsonrpc' => '2.0',
                    'id' => $this->_request->get['id']->string(),
                    'params' => [
                        'paths' => [$path],
                    ],
                ];

                return $this->_FileInfo($params);
            }
        } elseif ($resolution == 'overwrite') {
            if (is_file($pathTo)) {
                unlink($pathTo);

                if (copy($_FILES['file']['tmp_name'], $pathTo)) {
                    $params = [
                        'jsonrpc' => '2.0',
                        'id' => $this->_request->get['id']->string(),
                        'params' => [
                            'paths' => [$path],
                        ],
                    ];

                    return $this->_FileInfo($params);
                }
            }
        }

        $vars['error'] = [
            'code' => 101,
            'message' => 'Ошибка загрузки. Попробуйте еще раз',
            'data' => null,
        ];

        return $this->_response->setJson($vars);
    }

    private function _FileInfo($json)
    {
        $result = [];

        foreach ($json['params']['paths'] as $fpath) {
            $url = '/img/' . str_replace('../', '', trim($fpath, "\/."));
            $path = IMAGES_PATH . str_replace('../', '', trim($fpath, "\/."));

            $exist = file_exists($path);
            $size = 0;
            $lastModified = 0;
            $isFile = false;
            $canRead = true;
            $canWrite = true;
            $canRename = true;
            $canView = false;
            $canPreview = false;
            $meta_url = '';

            if ($exist) {
                $size = filesize($path);
                $lastModified = filemtime($path);
                $isFile = is_file($path);
                $canRead = is_readable($path);
                $canWrite = is_writable($path);
                $canRename = is_writable($path);
                $canView = is_file($path);
                $canPreview = is_file($path);
                $meta_url = $url;
            }

            $result[] = [
                'path' => $fpath,
                'size' => $size,
                'lastModified' => $lastModified,
                'isFile' => $isFile,
                'canRead' => $canRead,
                'canWrite' => $canWrite,
                'canEdit' => $canWrite,
                'canRename' => $canRename,
                'canView' => $canView,
                'canPreview' => $canPreview,
                'exists' => $exist,
                'meta' => [
                    'url' => $meta_url,
                ],
                'info' => [],
            ];
        }

        $vars = [
            'jsonrpc' => '2.0',
            'id' => $json['id'],
            'token' => $_SESSION['moxie_token'],
            'result' => $result,
        ];

        return $this->_response->setJson($vars);
    }

    private function _streamfile()
    {
        $path = IMAGES_PATH . str_replace('../', '', trim($this->_request->get['path']->string(), "\/."));
        $info = getimagesize($path);

        switch ($info[2]) {
            case 1:
                $image = imagecreatefromgif($path);
                break;
            case 2:
                $image = imagecreatefromjpeg($path);
                break;
            case 3:
                $image = imagecreatefrompng($path);
                break;
            default:
                $response = new \System\HttpResponse();
                echo $response->setStatus(\System\HttpREsponse::S4_NOT_FOUND);
                exit;
        }
        $dx = imagesx($image);
        $dy = imagesy($image);

        if ($this->_request->get['thumb']->bool() === true) {
            $params = \Lib_Images::PrepareResize($path, 0, 100, 100);
            $imageNew = @imagecreatetruecolor($params['w2'], $params['h2']);

            imagecopyresampled($imageNew, $image, 0, 0, 0, 0, $params['w2'], $params['h2'], $dx, $dy);
            imagedestroy($image);
            $image = $imageNew;
        }
        header('Content-type: image/png');
        imagepng($image);
        imagedestroy($image);
        exit;
    }

    private function _listRoots($json)
    {
        $name = 'upload/';
        $path = '/upload';
        $dir = $this->_application->User->lastName . '_' . $this->_application->User->firstName . '_' . $this->_application->User->secondName;
        $dir = str_replace(' ', '', $dir);

        if ($this->_application->User->userType != \Service\Users\Model_Config::TYPE_ADMIN) {
            if (!is_dir(IMAGES_PATH . 'upload/' . $dir)) {
                mkdir(IMAGES_PATH . 'upload/' . $dir);
            }
            $name = $dir;
            $path = 'upload/' . $dir;
        }
        $result = [
            [
                'name' => $name,
                'path' => $path,
                'meta' => [],
                'config' => [
                    'general.hidden_tools' => '',
                    'general.disabled_tools' => '',
                    'filesystem.extensions' => 'jpg,png,gif,jpeg',
                    'filesystem.force_directory_template' => false,
                    'upload.maxsize' => '10MB',
                    'upload.chunk_size' => '1mb',
                    'upload.extensions' => 'jpg,png,gif,jpeg',
                    'createdoc.templates' => '',
                    'createdoc.fields' => '',
                    'createdir.templates' => '',
                ],
            ],
        ];
        $vars = [
            'jsonrpc' => '2.0',
            'id' => $json['id'],
            'token' => $_SESSION['moxie_token'],
            'result' => $result,
        ];

        return $this->_response->setJson($vars);
    }

    private function _listFiles($json)
    {
        $url = '/img/' . str_replace('../', '', trim($json['params']['path'], "\/."));
        $path = IMAGES_PATH . str_replace('../', '', trim($json['params']['path'], "\/."));

        $exist = file_exists($path);

        $size = 0;
        $lastModified = 0;
        $isFile = false;
        $canRead = true;
        $canWrite = true;
        $canRename = true;
        $canView = false;
        $canPreview = false;
        $meta_url = '';

        $list = [];

        if ($exist) {
            $size = filesize($path);
            $lastModified = filemtime($path);
            $isFile = is_file($path);
            $canRead = is_readable($path);
            $canWrite = is_writable($path);
            $canRename = is_writable($path);
            $canView = is_file($path);
            $canPreview = is_file($path);
            $meta_url = $url;

            if (is_dir($path)) {
                $it = new \DirectoryIterator($path);

                foreach ($it as $file) {
                    if ($file->isDot()) {
                        continue;
                    }

                    $list[] = [
                        $file->getFilename(),
                        $file->isDir() ? 0 : $file->getSize(),
                        $file->getMTime(),
                        $file->isDir() ? 'drwr----' : '-rwrevpt',
                        [],
                    ];
                }
            }
        }

        $result = [
            'columns' => ['name', 'size', 'modified', 'attrs', 'info'],
            'config' => [
                'general.hidden_tools' => '',
                'general.disabled_tools' => '',
                'filesystem.extensions' => 'jpg,png,gif,jpeg',
                'filesystem.force_directory_template' => false,
                'upload.maxsize' => '10MB',
                'upload.chunk_size' => '1mb',
                'upload.extensions' => 'jpg,png,gif,jpeg',
                'createdoc.templates' => '',
                'createdoc.fields' => '',
                'createdir.templates' => '',
            ],
            'file' => [
                'path' => $json['params']['path'],
                'size' => $size,
                'lastModified' => $lastModified,
                'isFile' => $isFile,
                'canRead' => $canRead,
                'canWrite' => $canWrite,
                'canEdit' => $canWrite,
                'canRename' => $canRename,
                'canView' => $canView,
                'canPreview' => $canPreview,
                'exists' => $exist,
                'meta' => [
                    'url' => $meta_url,
                ],
            ],
            'urlFile' => null,
            'data' => $list,
            'url' => '',
            'thumbnailFolder' => 'mcith',
            'thumbnailPrefix' => 'mcith_',
            'offset' => 0,
            'last' => true,
        ];

        $vars = [
            'jsonrpc' => '2.0',
            'id' => $json['id'],
            'token' => $_SESSION['moxie_token'],
            'result' => $result,
        ];

        return $this->_response->setJson($vars);
    }

    private function _delete($json)
    {
        $paths = [];

        foreach ($json['params']['paths'] as $path) {
            $paths[] = $path;
            $pathToDel = IMAGES_PATH . str_replace('../', '', trim($path, "\/."));
            @unlink($pathToDel);
        }

        $vars = [
            'jsonrpc' => '2.0',
            'id' => $json['id'],
            'result' => [
                'paths' => $paths,
            ],
        ];

        return $this->_response->setJson($vars);
    }

    private function _moveTo($json)
    {
        $from = IMAGES_PATH . str_replace('../', '', trim($json['params']['from'], "\/."));
        $to = IMAGES_PATH . str_replace('../', '', trim($json['params']['to'], "\/."));

        if (is_file($to)) {
            unlink($to);
        }

        if (rename($from, $to)) {
            $json['params']['path'] = $json['params']['to'];

            return $this->_listFiles($json);
        }

        return $this->_listFiles($json);
    }

    private function _copyTo($json)
    {
        $from = IMAGES_PATH . str_replace('../', '', trim($json['params']['from'], "\/."));
        $to = IMAGES_PATH . str_replace('../', '', trim($json['params']['to'], "\/."));

        if (is_file($to)) {
            unlink($to);
        }

        if (copy($from, $to)) {
            $json['params']['path'] = $json['params']['to'];

            return $this->_listFiles($json);
        }

        return $this->_listFiles($json);
    }

    private function _createDirectory($json)
    {
        $url = '/img/' . str_replace('../', '', trim($json['params']['path'], "\/."));
        $path = IMAGES_PATH . str_replace('../', '', trim($json['params']['path'], "\/."));

        $vars = [
            'jsonrpc' => '2.0',
            'id' => $json['id'],
            'token' => $_SESSION['moxie_token'],
        ];

        if (mkdir($path)) {
            $size = filesize($path);
            $lastModified = filemtime($path);
            $isFile = is_file($path);
            $canRead = is_readable($path);
            $canWrite = is_writable($path);
            $canRename = is_writable($path);
            $canView = is_file($path);
            $canPreview = is_file($path);
            $meta_url = $url;

            $result = [
                'path' => $path,
                'file' => [
                    'path' => $json['params']['path'],
                    'size' => $size,
                    'lastModified' => $lastModified,
                    'isFile' => $isFile,
                    'canRead' => $canRead,
                    'canWrite' => $canWrite,
                    'canEdit' => $canWrite,
                    'canRename' => $canRename,
                    'canView' => $canView,
                    'canPreview' => $canPreview,
                    'exists' => true,
                    'meta' => [
                        'url' => $meta_url,
                    ],
                ],
            ];
            $vars['result'] = $result;
        } else {
            $error = [
                'code' => 100,
                'message' => 'Не могу создать папку',
                'data' => null,
            ];
            $vars['error'] = $error;
        }

        return $this->_response->setJson($vars);
    }

    /**
     * Обработчик GET-запросов
     *
     * @return mixed
     */
    public function actionPost()
    {
        $action = $this->_request->get['action']->string();

        switch ($action) {
            case 'language':
                return $this->_language();
            case 'auth':
                return $this->_auth();
        }
    }
}
