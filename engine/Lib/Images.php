<?php

class Lib_Images
{
    public const ERR_Images_DEFAULT_SECURITY_IMAGE_NOT_FOUND = 1;
    public const ERR_Images_DEFAULT_SECURITY_IMAGE_BIGGER_THEN_SOURCE = 2;

    private static $_security_border = [
        'x' => 10,
        'y' => 10,
    ];

    private static $work_memory_limit = '128M';

    private static $cur_memory_limit = '';

    /**
     * Установить лимит памяти
     *
     * @param string $limit лимит
     *
     * @return string old value
     */
    private static function SetMemoryLimit($limit = null)
    {
        if ($limit === null) {
            $limit = self::$work_memory_limit;
        }

        if ($limit == '') {
            return false;
        }
        self::$cur_memory_limit = ini_set('memory_limit', self::$work_memory_limit);

        return self::$cur_memory_limit;
    }

    /**
     * Вернуть лимит памяти
     *
     * @return string old value
     */
    private static function ReturnMemoryLimit()
    {
        if (self::$cur_memory_limit == '') {
            return false;
        }

        return ini_set('memory_limit', self::$cur_memory_limit);
    }

    /**
     * Вернуть дефолтный логотип
     *
     * @return string имя файла дефолтного логотипа
     */
    private static function GetDefaultSecurityImage()
    {
        $file = IMG_PATH . 'security.png';

        if (is_file($file)) {
            return $file;
        } else {
            return '';
        }
    }

    /**
     * Подготовка данных для файла
     *
     * @param string $file имя файла
     * @param string $dir путь до файла
     * @param string $url url до файла
     *
     * @throws Lib_Exception_Runtime_Backtraced
     *
     * @return array (path, url, w, h, size, mime)
     */
    public static function PrepareImage($file, $dir = '', $url = '')
    {
        $size = null;
        $hm = Lib_File_Store::GetRealPath($dir . $file);

        if (!is_file($hm)) {
            return false;
        }

        $inf = @getimagesize($hm);

        if ($inf === false) {
            return false;
        }

        $size = @filesize($hm);

        if ($size === false) {
            $err = error_get_last();

            return false;
        }

        return [
            'path' => $dir . $file,
            'url' => $url . $file,
            'w' => $inf[0],
            'h' => $inf[1],
            'size' => $size,
            'mime' => $inf['mime'],
        ];
    }

    /**
     * Подготовка данных для файла из объекта файла
     *
     * @param string $file имя файла
     * @param string $dir путь до файла
     *
     * @throws Lib_Exception_Runtime_Backtraced
     *
     * @return array - (file, w, h, size, mime)
     */
    public static function PrepareImageToObject($file, $dir = '')
    {
        $hm = Lib_File_Store::GetRealPath($dir . $file);

        if (!is_file($hm)) {
            throw new Lib_Exception_Runtime_Backtraced('File "' . $hm . '" not found when prepare images called');
        }
        $inf = @getimagesize($hm);

        if ($inf === false) {
            $err = error_get_last();
            throw new Lib_Exception_Runtime_Backtraced('Can not determine image params of file "' . $hm . '": ' . $err['message']);
        }

        $size = @filesize($hm);

        if ($size === false) {
            $err = error_get_last();
            throw new Lib_Exception_Runtime_Backtraced('Can not determine size of file "' . $hm . '": ' . $err['message']);
        }

        return [
            'file' => basename($file),
            'w' => $inf[0],
            'h' => $inf[1],
            'size' => $size,
            'mime' => $inf['mime'],
        ];
    }

    /**
     * Делает из строки параметров файла полноценный объект картинки
     *
     * @static
     *
     * @param string $fileString - строка параметров файла из \Lib_File_Store
     * @param string $path - путь к файлу
     * @param string $url - URL файла
     *
     * @return array (path, url, w, h, size, mime)
     */
    public static function PrepareImageFromFileString($fileString, $path, $url)
    {
        $fileObject = \Lib_File_Store::ObjectFromString($fileString);
        $fileObject['file'] = \Lib_File_Store::GetPath($fileObject['file']);
        $preparedImage = self::PrepareImageFromObject($fileObject, $path, $url);
        unset($fileObject);

        return $preparedImage;
    }

    /**
     * Конвертация объекта картинки в объекта для Lib_File_Store
     *
     * @param array $image - массив с информацией о файле
     *
     * @throws Lib_Exception_InvalidArgument
     *
     * @return array - (file, w, h, size, mime)
     */
    public static function ConvertImageToObject($image)
    {
        if (!is_array($image) && !isset($image['path'])) {
            throw new \Lib_Exception_InvalidArgument('image is not a Lib_Images object');
        }

        return [
            'file' => basename($image['path']),
            'w' => $image['w'],
            'h' => $image['h'],
            'size' => $image['size'],
            'mime' => $image['mime'],
        ];
    }

    /**
     * Подготовка данных для файла из объекта файла
     *
     * @param array $object объект файла (file, w, h, size, mime)
     * @param string $dir путь до файла
     * @param string $url url до файла
     *
     * @return array (path, url, w, h, size, mime)
     */
    public static function PrepareImageFromObject($object, $dir = '', $url = '')
    {
        $dir = Lib_File_Store::GetRealPath($dir);

        $url .= $object['file'];
        $file = $dir . $object['file'];

        return [
            'path' => $file,
            'url' => $url,
            'w' => $object['w'],
            'h' => $object['h'],
            'size' => $object['size'],
            'mime' => $object['mime'],
        ];
    }

    /**
     * Загрузить картинку в хранилище с созданием нескольких ее копий для различных параметров.
     * Возвращает массив объектов преобразованных в строку, готовых для сохранения в базу.
     *
     * @param string $file Имя ключа массива FILES
     * @param string $dir директория назначения
     * @param int $type Тип загружаемого добра
     * @param int $max_size Максимальный размер
     * @param array $params параметры загрузки файла
     * @param string $url адрес, с которого отдаются картинки
     * @param string $index индекс элемента в массиве FILES
     * @param int $dir_deep глубина директирий
     * @param int $dir_len  длина названия каждой директории
     *
     * @throws Lib_Exception_Runtime_Backtraced
     *
     * @return array массив объектов, преобразованных в строку
     */
    public static function MultipleUploadImages($file, $dir, $type = null, $max_size = null, $params = [], $url = '', $index = null, $dir_deep = null, $dir_len = null)
    {
        $files = Lib_File_Store::MultipleUpload($file, $dir, $type, $max_size, $params, $url, $index, $dir_deep, $dir_len);
        $objects = [];

        try {
            foreach ($files as $prefix => $l) {
                $objects[] = Lib_File_Store::ObjectToString(self::PrepareImageToObject(
                    Lib_File_Store::GetPath($l),
                    $dir . $prefix
                ));
            }
        } catch (Lib_Exception $e) {
            foreach ($files as &$l) {
                $l = $dir . $prefix . '/' . Lib_File_Store::GetPath($l);
            }

            Lib_File_Store::MultipleDelete($files);

            throw new Lib_Exception_Runtime_Backtraced('Can\'t prepare uploaded images');
        }
    }

    public static function Flip($src_file, $dest_file, $flip, $type = null)
    {
        if ($flip !== 'x' && $flip !== 'y') {
            throw new Lib_Exception_InvalidArgument('Direction is invalid.');
        }

        if (!is_file($src_file)) {
            throw new Lib_Exception_Runtime_Backtraced('File "' . $src_file . '" not found.');
        }

        $info = @getimagesize($src_file);

        if ($info === false) {
            $err = error_get_last();
            throw new Lib_Exception_Runtime_Backtraced('Can not determine image params of file "' . $src_file . '". ' . $err['message']);
        }

        self::SetMemoryLimit();
        $dest_img = @imagecreatetruecolor($info[0], $info[1]);

        if (!$dest_img) {
            $err = error_get_last();
            self::ReturnMemoryLimit();
            throw new Lib_Exception_Runtime_Backtraced('Can not create image canvas: w=' . $info[0] . ', h=' . $info[1] . '. ' . $err['message']);
        }

        $res = @imageAntiAlias($dest_img, true);

        if (!$res) {
            $err = error_get_last();
            self::ReturnMemoryLimit();
            throw new Lib_Exception_Runtime_Backtraced('imageAntiAlias error. ' . $err['message']);
        }

        switch ($info[2]) {
            case 1:
                $src_img = @imagecreatefromgif($src_file);
                break;
            case 2:
                $src_img = @imagecreatefromjpeg($src_file);
                break;
            case 3:
                $src_img = @imagecreatefrompng($src_file);
                break;
            default:
                self::ReturnMemoryLimit();
                throw new Lib_Exception_Runtime_Backtraced('Unexpected image format: "' . $src_file . '"');
        }

        if (!$src_img) {
            $err = error_get_last();
            self::ReturnMemoryLimit();
            throw new Lib_Exception_Runtime_Backtraced('Read image fail: "' . $src_file . '". ' . $err['message']);
        }

        if ($flip === 'x') {
            if (@imagecopyresampled($dest_img, $src_img, 0, 0, ($info[0] - 1), 0, $info[0], $info[1], 0 - $info[0], $info[1]) === false) {
                $err = error_get_last();
                self::ReturnMemoryLimit();
                throw new Lib_Exception_Runtime_Backtraced('Resample error: "' . $src_file . '". ' . $err['message']);
            }
        } elseif ($flip === 'y') {
            if (@imagecopyresampled($dest_img, $src_img, 0, 0, 0, ($info[1] - 1), $info[0], $info[1], $info[0], 0 - $info[1]) === false) {
                $err = error_get_last();
                self::ReturnMemoryLimit();
                throw new Lib_Exception_Runtime_Backtraced('Resample error: "' . $src_file . '". ' . $err['message']);
            }
        }

        if ($type === null) {
            $type = $info[2];
        }

        if (!(imagetypes() & $type)) {
            $type = $info[2];
        }

        $res = false;

        switch ($type) {
            case 1:
                $res = @imagegif($dest_img, $dest_file);
                break;
            case 2:
                $res = @imagejpeg($dest_img, $dest_file, 95);
                break;
            case 3:
                $res = @imagepng($dest_img, $dest_file, 2, PNG_ALL_FILTERS);
                break;
        }

        if (!$res) {
            $err = error_get_last();
            self::ReturnMemoryLimit();
            throw new Lib_Exception_Runtime_Backtraced('Image write to file error: "' . $dest_file . '". ' . $err['message']);
        }

        $res = @imagedestroy($dest_img);

        if (!$res) {
            $err = error_get_last();
            self::ReturnMemoryLimit();
            throw new Lib_Exception_Runtime_Backtraced('Error image destroy of "' . $dest_file . '". ' . $err['message']);
        }
        $res = @imagedestroy($src_img);

        if (!$res) {
            $err = error_get_last();
            self::ReturnMemoryLimit();
            throw new Lib_Exception_Runtime_Backtraced('Error image destroy of "' . $src_file . '". ' . $err['message']);
        }

        self::ReturnMemoryLimit();

        return true;
    }

    public static function ViewPortSet($src_file, $dest_file, $w, $h, $src_x, $src_y, $dst_x, $dst_y, $pw, $ph, $type = null)
    {
        if (!is_file($src_file)) {
            throw new Lib_Exception_Runtime_Backtraced('File "' . $src_file . '" not found.');
        }

        $info = @getimagesize($src_file);

        if ($info === false) {
            $err = error_get_last();
            throw new Lib_Exception_Runtime_Backtraced('Can not determine image params of file "' . $src_file . '". ' . $err['message']);
        }

        self::SetMemoryLimit();
        $dest_img = @imagecreatetruecolor($w, $h);

        if (!$dest_img) {
            $err = error_get_last();
            self::ReturnMemoryLimit();
            throw new Lib_Exception_Runtime_Backtraced('Can not create image canvas: w=' . $w . ', h=' . $h . '. ' . $err['message']);
        }

        $res = @imageAntiAlias($dest_img, true);

        if (!$res) {
            $err = error_get_last();
            self::ReturnMemoryLimit();
            throw new Lib_Exception_Runtime_Backtraced('imageAntiAlias error. ' . $err['message']);
        }

        switch ($info[2]) {
            case 1:
                $src_img = @imagecreatefromgif($src_file);
                break;
            case 2:
                $src_img = @imagecreatefromjpeg($src_file);
                break;
            case 3:
                $src_img = @imagecreatefrompng($src_file);
                break;
            default:
                self::ReturnMemoryLimit();
                throw new Lib_Exception_Runtime_Backtraced('Unexpected image format: "' . $src_file . '"');
        }

        if (!$src_img) {
            $err = error_get_last();
            self::ReturnMemoryLimit();
            throw new Lib_Exception_Runtime_Backtraced('Read image fail: "' . $src_file . '". ' . $err['message']);
        }

        if (@imagecopy($dest_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $pw, $ph) === false) {
            $err = error_get_last();
            self::ReturnMemoryLimit();
            throw new Lib_Exception_Runtime_Backtraced('Resample error: "' . $src_file . '". ' . $err['message']);
        }

        if ($type === null) {
            $type = $info[2];
        }

        if (!(imagetypes() & $type)) {
            $type = $info[2];
        }

        $res = false;

        switch ($type) {
            case 1:
                $res = @imagegif($dest_img, $dest_file);
                break;
            case 2:
                $res = @imagejpeg($dest_img, $dest_file, 95);
                break;
            case 3:
                $res = @imagepng($dest_img, $dest_file, 2, PNG_ALL_FILTERS);
                break;
        }

        if (!$res) {
            $err = error_get_last();
            self::ReturnMemoryLimit();
            throw new Lib_Exception_Runtime_Backtraced('Image write to file error: "' . $dest_file . '". ' . $err['message']);
        }

        $res = @imagedestroy($dest_img);

        if (!$res) {
            $err = error_get_last();
            self::ReturnMemoryLimit();
            throw new Lib_Exception_Runtime_Backtraced('Error image destroy of "' . $dest_file . '". ' . $err['message']);
        }
        $res = @imagedestroy($src_img);

        if (!$res) {
            $err = error_get_last();
            self::ReturnMemoryLimit();
            throw new Lib_Exception_Runtime_Backtraced('Error image destroy of "' . $src_file . '". ' . $err['message']);
        }

        self::ReturnMemoryLimit();

        return true;
    }

    /**
     * Подготовка параметров для ресайза
     *
     * @param string $src_file - путь к файлу
     * @param int $tr - способ преобразования (0 - пропорционально, 1 - по ширине, 2 - по высоте, 3 - с отрезанием части картинки (центр), 4 - с отрезанием части картинки (лево/верх), 5 - с отрезанием части картинки (право/низ), 6 - вырезание из указанного места)
     * @param int $w - необходимая ширина
     * @param int $h - необходимая высота
     * @param int $x - смещение по X
     * @param int $y - смещение по Y
     *
     * @throws Lib_Exception_Runtime_Backtraced
     * @throws Lib_Exception_Runtime_Backtraced
     *
     * @return array - (x1, y1, w1, h1, w2, h2, tr, small)
     */
    public static function PrepareResize($src_file, $tr = 0, $w = null, $h = null, $x = 0, $y = 0)
    {
        if (!is_file($src_file)) {
            throw new Lib_Exception_Runtime_Backtraced('File "' . $src_file . '" not found.');
        }
        $info = @getimagesize($src_file);

        if ($info === false) {
            $err = error_get_last();
            throw new Lib_Exception_Runtime_Backtraced('Can not determine image params of file "' . $src_file . '". ' . $err['message']);
        }

        $msize = $info[0] * $info[1] * 4;

        if ($msize > 64000000) {
            throw new Lib_Exception_Runtime_Backtraced('Too big bitmap for image: ' . $msize . ' max allowed: ' . 64000000, 1);
        }
        $list = [
            'x1' => ($tr == 6) ? $x : 0,
            'y1' => ($tr == 6) ? $y : 0,
            'w1' => $info[0],
            'h1' => $info[1],
            'w2' => null,
            'h2' => null,
            'tr' => $tr,
            'small' => false,
        ];

        $src_ratio = $info[0] / $info[1];
        $new_w = $w;
        $new_h = $h;
        $ratio = $w / $h;

        if ($tr == 0) {
            if ($ratio < $src_ratio) {
                $new_h = $new_w / $src_ratio;
            } else {
                $new_w = $new_h * $src_ratio;
            }
        } elseif ($tr == 1) {
            $new_h = $new_w / $src_ratio;
        } elseif ($tr == 2) {
            $new_w = $new_h / $src_ratio;

            if ($w > $h) {
                $t = $h;
                $h = $w;
                $w = $t;
            }
        } elseif ($tr == 3) {
            if ($ratio > $src_ratio) {
                $list['h1'] = round($info[0] / $ratio);
                $list['y1'] = round(($info[1] - $list['h1']) / 2);
            } else {
                $list['w1'] = round($info[1] * $ratio);
                $list['x1'] = round(($info[0] - $list['w1']) / 2);
            }
        } elseif ($tr == 4) {
            if ($ratio > $src_ratio) {
                $list['h1'] = round($info[0] / $ratio);
            } else {
                $list['w1'] = round($info[1] * $ratio);
            }
        } elseif ($tr == 5) {
            if ($ratio > $src_ratio) {
                $list['h1'] = round($info[0] / $ratio);
                $list['y1'] = $info[1] - $list['h1'];
            } else {
                $list['w1'] = round($info[1] * $ratio);
                $list['x1'] = $info[0] - $list['w1'];
            }
        } elseif ($tr == 6) {
            if ($info[0] < $list['x1'] + $w || $info[1] < $list['y1'] + $h) {
                throw new Lib_Exception_Runtime_Backtraced('Images crop error.');
            }

            $list['w1'] = round($new_w);
            $list['h1'] = round($new_h);
        }

        $list['w2'] = round($new_w);
        $list['h2'] = round($new_h);

        if ($info[0] <= $w && $info[1] <= $h) {
            $list['small'] = true;
        }

        return $list;
    }

    /**
     * Заливка изображения с изменением размеров
     *
     * @param string $src_file путь к файлу
     * @param string $dest_file имя файла назначения (если null - отдать в return)
     * @param array $params ассоциативный массив размеров (x1, y1, w1, h1, w2, h2)
     * @param int $type формат картинки в итоге (если null - то как у исходной)
     *
     * @return bool
     *
     * @throws Lib_Exception_Runtime_Backtraced
     * @throws Lib_Exception_Runtime_Backtraced
     */
    public static function Resize($src_file, $dest_file, $params, $type = null)
    {
        if (!is_file($src_file)) {
            throw new Lib_Exception_Runtime_Backtraced('File "' . $src_file . '" not found.');
        }
        $info = @getimagesize($src_file);

        if ($info === false) {
            $err = error_get_last();
            throw new Lib_Exception_Runtime_Backtraced('Can not determine image params of file "' . $src_file . '". ' . $err['message']);
        }

        $dest_img = @imagecreatetruecolor($params['w2'], $params['h2']);

        if (!$dest_img) {
            $err = error_get_last();
            throw new Lib_Exception_Runtime_Backtraced('Can not create image canvas: w=' . $params['w2'] . ', h=' . $params['h2'] . '. ' . $err['message']);
        }

        switch ($info[2]) {
            case 1:
                imagealphablending($dest_img, false);
                imagesavealpha($dest_img, true);
                $src_img = imagecreatefromgif($src_file);
                break;
            case 2:
                $src_img = imagecreatefromjpeg($src_file);
                break;
            case 3:
                imagealphablending($dest_img, false);
                imagesavealpha($dest_img, true);
                $src_img = imagecreatefrompng($src_file);
                break;
            default:
                throw new Lib_Exception_Runtime_Backtraced('Unexpected image format: "' . $src_file . '"');
        }

        if (!$src_img) {
            $err = error_get_last();
            throw new Lib_Exception_Runtime_Backtraced('Read image fail: "' . $src_file . '". ' . $err['message']);
        }

        if (imagecopyresampled($dest_img, $src_img, 0, 0, $params['x1'], $params['y1'], $params['w2'], $params['h2'], $params['w1'], $params['h1']) === false) {
            $err = error_get_last();
            throw new Lib_Exception_Runtime_Backtraced('Resample error: "' . $src_file . '". ' . $err['message']);
        }

        if ($type === null) {
            $type = $info[2];
        }

        if (!(imagetypes() & $type)) {
            $type = $info[2];
        }

        $res = false;

        switch ($type) {
            case 1:
                $res = imagegif($dest_img, $dest_file);
                break;
            case 2:
                $res = imagejpeg($dest_img, $dest_file, isset($params['quality_jpeg']) && (int) $params['quality_jpeg'] > 0 ? (int) $params['quality_jpeg'] : 95);
                break;
            case 3:
                $res = imagepng($dest_img, $dest_file, isset($params['quality_png']) && (int) $params['quality_png'] > 0 ? (int) $params['quality_png'] : 2, PNG_ALL_FILTERS);
                break;
        }

        if (!$res) {
            $err = error_get_last();
            throw new Lib_Exception_Runtime_Backtraced('Image write to file error: "' . $dest_file . '". ' . $err['message']);
        }

        $res = @imagedestroy($dest_img);

        if (!$res) {
            $err = error_get_last();
            throw new Lib_Exception_Runtime_Backtraced('Error image destroy of "' . $dest_file . '". ' . $err['message']);
        }
        $res = @imagedestroy($src_img);

        if (!$res) {
            $err = error_get_last();
            throw new Lib_Exception_Runtime_Backtraced('Error image destroy of "' . $src_file . '". ' . $err['message']);
        }

        return true;
    }

    /**
     * Наложение защитной картинки на изображение
     *
     * @param string $src_file - путь к файлу
     * @param string $dest_file - путь к файлу назначения
     * @param string $sec_file - имя файла, с логотипом
     * @param string $position - код места положения
     *
     * @throws Lib_Exception_Runtime_Backtraced
     * @throws Lib_Exception_Runtime
     */
    public static function PutSecurityImage($src_file, $dest_file = null, $sec_file = null, $position = null, $alpha = 50)
    {
        if ($sec_file === null) {
            $sec_file = self::GetDefaultSecurityImage();

            if ($sec_file == '') {
                throw new Lib_Exception_Runtime_Backtraced('Default security image not found', self::ERR_Images_DEFAULT_SECURITY_IMAGE_NOT_FOUND);
            }
        }

        if (!is_file($src_file)) {
            throw new Lib_Exception_Runtime_Backtraced('File "' . $src_file . '" not found');
        }

        if (!is_file($sec_file)) {
            throw new Lib_Exception_Runtime_Backtraced('File "' . $sec_file . '" not found');
        }

        if ($dest_file === null) {
            $dest_file = $src_file;
        }

        if ($position === null) {
            $position = 'br';
        }

        $info_src = @getimagesize($src_file);

        if ($info_src === false) {
            $err = error_get_last();
            throw new Lib_Exception_Runtime_Backtraced('Can not determine image params of file "' . $src_file . '". ' . $err['message']);
        }
        $info_sec = @getimagesize($sec_file);

        if ($info_sec === false) {
            $err = error_get_last();
            throw new Lib_Exception_Runtime_Backtraced('Can not determine image params of file "' . $sec_file . '". ' . $err['message']);
        }

        $src_x = $info_src[0];
        $src_y = $info_src[1];
        $sec_x = $info_sec[0];
        $sec_y = $info_sec[1];

        if ($src_x - self::$_security_border['x'] * 2 < $sec_x
            || $src_y - self::$_security_border['y'] * 2 < $sec_y
        ) {
            throw new Lib_Exception_Runtime('Security image bigger than source image. Can not put it :(', self::ERR_Images_DEFAULT_SECURITY_IMAGE_BIGGER_THEN_SOURCE);
        }

        //return true;

        // X зависит от lcr - left, center, right
        if (strpos($position, 'l') !== false) {
            $x = self::$_security_border['x'];
        } elseif (strpos($position, 'c') !== false) {
            $x = ceil($src_x / 2 - $sec_x / 2);
        } elseif (strpos($position, 'r') !== false) {
            $x = $src_x - $sec_x - self::$_security_border['x'];
        } else {
            $x = self::$_security_border['x'];
        }

        // Y зависит от tmb - top, middle, bottom
        if (strpos($position, 't') !== false) {
            $y = self::$_security_border['y'];
        } elseif (strpos($position, 'm') !== false) {
            $y = ceil($src_y / 2 - $sec_y / 2);
        } elseif (strpos($position, 'b') !== false) {
            $y = $src_y - $sec_y - self::$_security_border['y'];
        } else {
            $y = self::$_security_border['y'];
        }

        self::SetMemoryLimit();

        switch ($info_src[2]) {
            case 1:
                $src_img = @imagecreatefromgif($src_file);
                break;
            case 2:
                $src_img = @imagecreatefromjpeg($src_file);
                break;
            case 3:
                $src_img = @imagecreatefrompng($src_file);
                break;
            default:
                self::ReturnMemoryLimit();
                throw new Lib_Exception_Runtime_Backtraced('Unexpected image format: "' . $src_file . '"');
        }

        if (!$src_img) {
            $err = error_get_last();
            self::ReturnMemoryLimit();
            throw new Lib_Exception_Runtime_Backtraced('Read image fail: "' . $src_file . '". ' . $err['message']);
        }

        switch ($info_sec[2]) {
            case 1:
                $sec_img = @imagecreatefromgif($sec_file);
                break;
            case 2:
                $sec_img = @imagecreatefromjpeg($sec_file);
                break;
            case 3:
                $sec_img = @imagecreatefrompng($sec_file);
                break;
            default:
                self::ReturnMemoryLimit();
                throw new Lib_Exception_Runtime_Backtraced('Unexpected image format: "' . $sec_file . '"');
        }

        if (!$sec_img) {
            $err = error_get_last();
            self::ReturnMemoryLimit();
            throw new Lib_Exception_Runtime_Backtraced('Read image fail: "' . $sec_file . '". ' . $err['message']);
        }

        if (@imagecopymerge($src_img, $sec_img, $x, $y, 0, 0, $sec_x, $sec_y, $alpha) === false) {
            $err = error_get_last();
            self::ReturnMemoryLimit();
            throw new Lib_Exception_Runtime_Backtraced('Put watermark "' . $sec_file . '" on image "' . $src_file . '" fail. ' . $err['message']);
        }

        $res = false;

        switch ($info_src[2]) {
            case 1:
                $res = @imagegif($src_img, $dest_file);
                break;
            case 2:
                $res = @imagejpeg($src_img, $dest_file, 100);
                break;
            case 3:
                $res = @imagepng($src_img, $dest_file, 2, PNG_ALL_FILTERS);
                break;
        }

        if (!$res) {
            $err = error_get_last();
            self::ReturnMemoryLimit();
            throw new Lib_Exception_Runtime_Backtraced('Image write to file error: "' . $dest_file . '". ' . $err['message']);
        }

        $res = @imagedestroy($sec_img);

        if (!$res) {
            $err = error_get_last();
            self::ReturnMemoryLimit();
            throw new Lib_Exception_Runtime_Backtraced('Error image destroy of "' . $sec_file . '". ' . $err['message']);
        }
        $res = @imagedestroy($src_img);

        if (!$res) {
            $err = error_get_last();
            self::ReturnMemoryLimit();
            throw new Lib_Exception_Runtime_Backtraced('Error image destroy of "' . $src_file . '". ' . $err['message']);
        }

        self::ReturnMemoryLimit();
    }
}
