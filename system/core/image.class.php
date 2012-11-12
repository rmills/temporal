<?php

if (!defined('IMAGE_CACHE_PATH')) {
    define('IMAGE_CACHE_PATH', PATH_MEDIA . 'cache/image/');
}

if (!defined('IMAGE_ORGINAL_PATH')) {
    define('IMAGE_ORGINAL_PATH', PATH_MEDIA . 'image/');
}

if (!defined('IMAGE_DEFAULT_THUMBNAIL_SIZE')) {
    define('IMAGE_DEFAULT_THUMBNAIL_SIZE', 100);
}

if (!defined('IMAGE_DEFAULT_SQUARE')) {
    define('IMAGE_DEFAULT_SQUARE', 1);
}

class Image {

    private $_file = false;
    private $_name = false;
    private $_data = array();
    private $_id = 0;
    public $_error = false;

    public function __construct($id = false) {
        $this->check_folders();
        if (is_numeric($id)) {
            $this->load($id);
        }
    }

    public function create($file, $name = false) {
        $orginal = IMAGE_ORGINAL_PATH . $file;
        $ext = pathinfo($orginal);
        $sql = '
            INSERT INTO images (
                `file`,
                `orginal`,
                `name`,
            ) VALUES (
                \'' . \DB::clean($this->random_filename($ext['extension'])) . '\',
                \'' . \DB::clean($file) . '\',
                \'' . \DB::clean(urlencode($name)) . '\'
            )';
        \DB::q($sql);
    }

    public function load($image_id_or_file) {
        if (is_numeric($image_id_or_file)) {
            $sql = 'SELECT * FROM `images` WHERE `id` = \'' . \DB::clean($image_id_or_file) . '\' LIMIT 1';
        } else {
            $sql = 'SELECT * FROM `images` WHERE `file` = \'' . \DB::clean($image_id_or_file) . '\' LIMIT 1';
        }
        $list = \DB::q($sql);
        if (is_array($list)) {
            foreach ($list as $item) {
                foreach ($item as $k => $v) {
                    $this->_data[$k] = $v;
                }
                $this->_name = urldecode($item['name']);
                $this->_file = $item['file'];
                return true;
            }
        }
        $this->_error = 'Image not found';
        return false;
    }

    private function check_folders() {

        if (!is_dir(CACHE_PATH)) {
            echo CACHE_PATH;
            mkdir(CACHE_PATH);
        }

        if (!is_dir(IMAGE_CACHE_PATH)) {
            echo IMAGE_CACHE_PATH;
            mkdir(IMAGE_CACHE_PATH);
        }

        if (!is_dir(IMAGE_ORGINAL_PATH)) {
            mkdir(IMAGE_ORGINAL_PATH);
        }
    }

    public function name($name = false) {
        return $this->_name;
    }

    public function setname() {
        
    }

    public function delete() {
        
    }

    public function thumbnail($size = IMAGE_DEFAULT_THUMBNAIL_SIZE, $square = IMAGE_DEFAULT_SQUARE) {
        $this->check_cache($this->_file, $size, $square);
        $path = PATH_BASE.IMAGE_CACHE_PATH . $square . '/' . $size . '/';
        if (!is_file($path)) {
            $this->create_cache_file($size, $square, $path, $this->_file);
        }
        return '<img src="' . $path . $this->_file . '" title="' . $this->_name . '" alt="' . $this->_name . '">';
    }

    public function thumbnail_link($href, $size = IMAGE_DEFAULT_THUMBNAIL_SIZE, $square = IMAGE_DEFAULT_SQUARE, $class = false) {
        $this->check_cache($this->_file, $size, $square);
        return '<a class="'.$class.'" href="' . $href . '">' . $this->thumbnail($size, $square) . '</a>';
    }
    
    public function thumbnail_display_link($display_size, $size = IMAGE_DEFAULT_THUMBNAIL_SIZE, $square = IMAGE_DEFAULT_SQUARE, $class = false) {
        $this->check_cache($this->_file, $display_size, 0);
        $href = PATH_BASE.IMAGE_CACHE_PATH .'0'.'/' . $display_size . '/'.$this->_file;
        return '<a class="'.$class.'" href="' . $href . '">' . $this->thumbnail($size, $square) . '</a>';
    }

    private function check_cache($file, $size, $square) {
        $path1 = IMAGE_CACHE_PATH . $square . '/';
        $path2 = $path1 . $size . '/';
        if (!is_dir($path1)) {
            mkdir($path1);
        }

        if (!is_dir($path2)) {
            mkdir($path2);
        }


        if (!is_file($path2 . $file)) {
            $this->create_cache_file($size, $square, $path2, $file);
        }
    }

    private function create_cache_file($size, $square, $path, $file) {
        $path = IMAGE_CACHE_PATH . $square . '/' . $size . '/';
        copy(IMAGE_ORGINAL_PATH . $file, $path . $file);
        if ($square) {
            Image::resize_square($path . $file, $size);
        } else {
            Image::resize($path . $file, $size);
        }
    }

    public static function random_filename($ext, $len = 10) {
        $result = "";
        $charPool = '0123456789abcdefghijklmnopqrstuvwxyz';
        for ($p = 0; $p < $len; $p++) {
            $result .= $charPool[mt_rand(0, strlen($charPool) - 1)];
        }
        $sql = 'SELECT * FROM `images` WHERE `file` = \'' . \DB::clean($result . '.' . $ext) . '\' LIMIT 1';
        $list = \DB::q($sql);
        if (is_array($list)) {
            foreach ($list as $item) {
                return $this->random_filename($ext);
            }
        }
        return $result;
    }

    public static function resize($imagepath, $size, $resize_by_height = true) {

        if (!is_file($imagepath)) {
            return false;
        }

        $type = strtolower(pathinfo($imagepath, PATHINFO_EXTENSION));

        $limit_height = $size;
        $limit_width = $size;

        $q = 90;
        switch ($type) {
            case 'jpg':
                $temp_image = imagecreatefromjpeg($imagepath);
                break;

            case 'png':
                $temp_image = imagecreatefrompng($imagepath);
                break;

            case 'gif':
                $temp_image = imagecreatefromgif($imagepath);
                break;
        }


        $orginal_width = imagesx($temp_image);
        $orginal_height = imagesy($temp_image);


        if ($resize_by_height) {
            if ($orginal_height < $size) {
                return true;
            }

            $new_width = floor($orginal_width * ($limit_height / $orginal_height));
            $scale = $new_width / $orginal_width;
            $new_height = ceil($orginal_height * $scale);
        } else {
            if ($orginal_width < $size) {
                return true;
            }

            $new_height = floor($orginal_height * ($limit_width / $orginal_width));
            $scale = $new_height / $orginal_height;
            $new_width = ceil($orginal_width * $scale);
        }
        $new_image = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($new_image, $temp_image, 0, 0, 0, 0, $new_width, $new_height, $orginal_width, $orginal_height);

        switch ($type) {
            case 'jpg':
                imagejpeg($new_image, $imagepath, $q);
                break;

            case 'png':
                imagepng($new_image, $imagepath, $q);
                break;

            case 'gif':
                imagegif($new_image, $imagepath, $q);
                break;
        }

        imagedestroy($new_image);
        return true;
    }

    /**
     * Resize an image to square
     * @param <type> $imagepath path to src
     * @param <type> $size max size
     * @param <type> $resize_by_height resize by height
     * @return <bool> false if the image is not found
     */
    public static function resize_square($imagepath, $size) {
        if (!is_file($imagepath)) {
            return false;
        }

        /*
          $limit_height = $size;
          $limit_width = $size;
         */
        $q = 90;

        $type = strtolower(pathinfo($imagepath, PATHINFO_EXTENSION));
        switch ($type) {
            case 'jpg':
                $temp_image = imagecreatefromjpeg($imagepath);
                break;

            case 'bmp':
                $temp_image = imagecreatefromwbmp($imagepath);
                break;

            case 'png':
                $temp_image = imagecreatefrompng($imagepath);
                break;

            case 'gif':
                $temp_image = imagecreatefromgif($imagepath);
                break;
        }

        $image_data = getimagesize($imagepath);

        if ($image_data[0] > $image_data[1]) {
            // For landscape images
            $x_offset = ($image_data[0] - $image_data[1]) / 2;
            $y_offset = 0;
            $square_size = $image_data[0] - ($x_offset * 2);
        } else {
            // For portrait and square images
            $x_offset = 0;
            $y_offset = ($image_data[1] - $image_data[0]) / 2;
            $square_size = $image_data[1] - ($y_offset * 2);
        }

        $new_image = imagecreatetruecolor($size, $size);
        imagecopyresampled($new_image, $temp_image, 0, 0, $x_offset, $y_offset, $size, $size, $square_size, $square_size);

        switch ($type) {
            case 'jpg':
                imagejpeg($new_image, $imagepath, $q);
                break;

            case 'bmp':
                imagewbmp($new_image, $imagepath);
                break;

            case 'png':
                imagepng($new_image, $imagepath, 0);
                break;

            case 'gif':
                imagegif($new_image, $imagepath);
                break;
        }

        imagedestroy($new_image);
        return true;
    }

}