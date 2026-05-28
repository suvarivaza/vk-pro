<?php

namespace Service\Pages;

class Controller_Admin_Default extends Controller_Admin
{
    /**
     * @return mixed
     */
    public function actionGet()
    {
        return $this->_response->setLocation('/admin/pages/list/1');
    }

    /**
     * @return \System\HttpResponse|null
     */
    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'upload':
                return $this->_upload();
            case 'delete':
                return $this->_delete();
        }

        return $this->_response->setStatus(\System\HttpResponse::S4_METHOD_NOT_ALLOWED);
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

        if (($res = \Lib_Images::PrepareResize($_FILES['file']['tmp_name'], 0, 200, 130))) {
            \Lib_Images::Resize($_FILES['file']['tmp_name'], $dir['small'] . '/' . $_FILES['file']['name'], $res);
        }

        if (($res = \Lib_Images::PrepareResize($_FILES['file']['tmp_name'], 0, 600, 600))) {
            \Lib_Images::Resize($_FILES['file']['tmp_name'], $dir['big'] . '/' . $_FILES['file']['name'], $res);
        }

        $result = [
            'success' => true,
            'jsonrpc' => '2.0',
            'key' => \Lib_Uuid::getNext(),
            'name' => $_FILES['file']['name'],
            'url' => '/images/temp/big/' . $uuid . '/' . $_FILES['file']['name'],
            'url_preview' => '/images/temp/small/' . $uuid . '/' . $_FILES['file']['name'],
        ];

        return $this->_response->setJson($result);
    }

    private function _delete()
    {
        $uuid = $this->_request->post['uuid']->string();
        $name = $this->_request->post['key']->string();

        $dirs = [
            IMAGES_PATH . 'temp/big/' . $uuid . '/',
            IMAGES_PATH . 'temp/small/' . $uuid . '/',
            IMAGES_PATH . 'articles/big/',
            IMAGES_PATH . 'articles/small/',
        ];

        foreach ($dirs as $dir) {
            if (is_file($dir . $name)) {
                @unlink($dir . $name);
            }
        }

        $photos = $this->_item->getPhotos();

        foreach ($photos as $id => $photo) {
            if ($name == $photo['path']) {
                unset($photos[$id]);
            }
        }
        $this->_item->setPhotos($photos);
        $this->factory->items->save($this->_item);

        return $this->_response->setJson(['success' => true]);
    }

    protected function uploadPhotos($photos)
    {
        $uuid = $this->_request->post['uuid']->string();

        $dir = [
            'big' => IMAGES_PATH . 'temp/big/' . $uuid,
            'small' => IMAGES_PATH . 'temp/small/' . $uuid,
        ];

        if (!is_array($photos)) {
            $photos = [];
        }

        if (is_dir($dir['big'])) {
            $dirs = new \DirectoryIterator($dir['big']);

            foreach ($dirs as $file) {
                if (!is_file($dir['small'] . '/' . $file)) {
                    continue;
                }

                $photo = [];

                $path = rand(10, 99) . '/' . rand(10, 99) . '/' . rand(10, 99);
                $photo['path'] = $path . '/' . $file;

                if (!is_dir(IMAGES_PATH . 'articles/big/' . $path)) {
                    mkdir(IMAGES_PATH . 'articles/big/' . $path, 0777, true);
                }

                if (!is_dir(IMAGES_PATH . 'articles/small/' . $path)) {
                    mkdir(IMAGES_PATH . 'articles/small/' . $path, 0777, true);
                }

                rename($dir['big'] . '/' . $file, IMAGES_PATH . 'articles/big/' . $photo['path']);
                rename($dir['small'] . '/' . $file, IMAGES_PATH . 'articles/small/' . $photo['path']);

                $photos[] = $photo;
            }

            foreach ($dir as $path) {
                rmdir($path);
            }
        }

        return $photos;
    }
}
