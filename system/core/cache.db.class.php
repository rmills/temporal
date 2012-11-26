<?php

class  CacheDB{
    public $_id;
    public $_path;
    public $_data;
    public $_expires;
    public $_block = false;
    public $_status = false;
    public $_loaded = false;
    
    function __construct($id = false, $block = false) {
        if(is_numeric($id)){
            $try = $this->load($id, $block);
            return $try;
        }elseif($id){
            $try = $this->loadpath($id, $block);
            return $try;
        }
    }
    
    public function loadpath($path, $block){
        $sql = 'SELECT * FROM `cache` WHERE `path` = \''.DB::clean($path).'\' AND `block` = \''.$block.'\' LIMIT 1';
        $q = \DB::q($sql);
        foreach($q as $row){
            $this->_id = $row['id'];
            $this->_path = $row['path'];
            $this->_data = self::decode($row['data']);
            $this->_expires = $row['expires'];
            $this->_block = $row['block'];
            if(time() < $this->_expires){
                $this->_status = true;
            }
            $this->_loaded = true;
        }
    }
    
    public function load($id){
        $sql = 'SELECT * FROM `cache` WHERE `id` = \''.DB::clean($id).'\' AND `block` = \''.$block.'\' LIMIT 1';
        $q = \DB::q($sql);
        foreach($q as $row){
            $this->_id = $row['id'];
            $this->_path = $row['path'];
            $this->_data = $row['data'];
            $this->_expires = $row['expires'];
            $this->_block = $row['block'];
            if(time() < $this->_expires){
                $this->_status = true;
            }
            $this->_loaded = true;
        }
    }
    
    public function data(){
        return $this->_data;
    }
    
    public function block(){
        return $this->_block;
    }
    
    
    /** Static Methods **/
    
    public static function encode($str){
        return base64_encode($str);
    }
    
    public static function decode($str){
        return base64_decode($str);
    }
    
    public static function flush(){
        if(\CMS::$_user->_super_user){
            $sql = 'TRUNCATE TABLE `cache`';
            \DB::q($sql);
        }
    }
    
    public static function setcache($path, $data, $block, $expires = CACHE_TIME){
        self::clearpath($path, $block);
        $time = time();
        $expires = strtotime($expires, $time);
        $sql = '
            INSERT INTO cache (
                `path`,
                `data`,
                `block`,
                `expires`
            ) VALUES (
                \'' . \DB::clean($path) . '\',
                \'' . \DB::clean(self::encode($data)) . '\',
                \'' . \DB::clean($block) . '\',
                \'' . \DB::clean($expires) . '\'
            )';
        \DB::q($sql);
    }
    
    public static function clearpath($path, $block){
        $sql = 'DELETE FROM `cache` WHERE `path` = \'' . \DB::clean($path) . '\' AND `block` = \''.$block.'\'';
        \DB::q($sql);
    }
}