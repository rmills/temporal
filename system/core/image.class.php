<?php

if(!defined('IMAGE_CACHE_PATH')){
    define('IMAGE_CACHE_PATH', PATH_MEDIA.'/cache/image/');
}

if(!defined('IMAGE_ORGINAL_PATH')){
    define('IMAGE_ORGINAL_PATH', PATH_MEDIA.'image/');
}

if(!defined('IMAGE_DEFAULT_THUMBNAIL_SIZE')){
    define('IMAGE_DEFAULT_THUMBNAIL_SIZE', 100);
}

if(!defined('IMAGE_DEFAULT_SQUARE')){
    define('IMAGE_DEFAULT_SQUARE', true);
}

class Image{
    
    private $_file = false;
    private $_name = false;
    private $_data = array();
    private $_id = 0;
    
    public $_error = false;
    
    private function __construct($id = false) {
        $this->check_folders();
        if(is_numeric($id)){
            $this->load($id);
        }
    }
    
    public function create($file, $name=false){
        $orginal = IMAGE_ORGINAL_PATH.$file;
        $ext = pathinfo($orginal);
        $sql = '
            INSERT INTO images (
                `file`,
                `orginal`,
                `name`,
            ) VALUES (
                \'' . \DB::clean($this->random_filename($ext['extension'])) . '\',
                \'' . \DB::clean($file) . '\',
                \'' . \DB::clean($name) . '\'
            )';
        \DB::q($sql);
    }
    
    public function load($image_id_or_file){
        if(is_numeric($image_id_or_file)){
            $sql = 'SELECT * FROM `images` WHERE `id` = \'' . \DB::clean($image_id_or_file) . '\' LIMIT 1';
        }else{
            $sql = 'SELECT * FROM `images` WHERE `file` = \'' . \DB::clean($image_id_or_file) . '\' LIMIT 1';
        }
        $list = \DB::q($sql);
        if (is_array($list)) {
            foreach ($list as $item) {
                foreach ($item as $k=>$v){
                   $this->_data[$k] = $v; 
                }
                $this->_name = $item['name'];
                $this->_file = $item['file'];
                return true;
            }
        }
        $this->_error = 'Image not found';
        return false;
    }
    
    
    private function check_folders(){
        if(!is_folder(IMAGE_CACHE_PATH)){
            mkdir(IMAGE_CACHE_PATH);
        }
        
        if(!is_folder(IMAGE_ORGINAL_PATH)){
            mkdir(IMAGE_ORGINAL_PATH);
        }
    }
    
    public function name($name=false){
        return $this->_name;
    }
    
    
    public function setname(){
        
    }
    
    public function delete(){
        
    }
    
    public function thumbnail($size=IMAGE_DEFAULT_THUMBNAIL_SIZE, $square=IMAGE_DEFAULT_SQUARE){
        $this->check_cache($this->_file, $size, $square);
        $path = IMAGE_CACHE_PATH.$square.'/'.$size.'/';
        if(!is_file($path)){
            $this->create_cache_file($size, $square, $path, $this->_file);
        }
        return '<img src="'.$path.$file.'" alt="'.$this->_name.'" alt="'.$this->_title.'">';
    }
    
    public function thumbnail_link($href, $size=IMAGE_DEFAULT_THUMBNAIL_SIZE, $square=IMAGE_DEFAULT_SQUARE){
        $this->check_cache($this->_file, $size, $square);
        $path = IMAGE_CACHE_PATH.$square.'/'.$size.'/';
        return '<a href="'.$href.'">'.$this->thumbnail($size, $square).'</a>';
    }
    
    private function check_cache($file, $size, $square){
        $path = IMAGE_CACHE_PATH.$square.'/'.$size.'/';
        if(!is_folder($path)){
            mkdir($path);
        }
        
        if(!is_file($path.$file)){
            $this->create_cache_file($size, $square, $path, $file);
        }
    }
    
    private function create_cache_file($size, $square, $path, $file){
        $path = IMAGE_CACHE_PATH.$square.'/'.$size.'/';
        copy(IMAGE_ORGINAL_PATH.$file, $path.$file);
        $image = new Imagick($path.$file);
        
        list($w,$h) = getimagesize($path.$file);
        //copy(IMAGE_ORGINAL_PATH.$file, $path.$file);
        if(!$square){
            $image->thumbnailImage($size, $size);
        }else{
            if($w > $h){
                $image->thumbnailImage($size, 0, false);
            }else{
                $image->thumbnailImage(0, $size, false);
            }
        }
    }
    

    public static function random_filename($ext, $len = 10) {
        $result = "";
        $charPool = '0123456789abcdefghijklmnopqrstuvwxyz';
        for ($p = 0; $p < $len; $p++) {
            $result .= $charPool[mt_rand(0, strlen($charPool) - 1)];
        }
        $sql = 'SELECT * FROM `images` WHERE `file` = \'' . \DB::clean($result.'.'.$ext) . '\' LIMIT 1';
        $list = \DB::q($sql);
        if (is_array($list)) {
            foreach ($list as $item) {
                return $this->random_filename($ext);
            }
        }
        return $result;
        
    }
}