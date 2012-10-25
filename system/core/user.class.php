<?php

class User {
    public $_uid = 0;
    public $_data = array();
    public $_logged_in = false;
    public $_internal = false;
    public $_error = false;
    public $_super_user = false;
    public $_permissions = array();
    public $_modules = array();

    public function __construct($id){
        $sql = 'SELECT * FROM `users` WHERE `uid` = '.DB::clean($id).' LIMIT 1';
        $response = DB::q($sql);
        if(count($response)){
                $this->build($response[0]);
        }else{
            $this->_error = true;
        }
        if($this->_internal){
            CMS::log('User', 'User Loaded:'.$id);
            CMS::log('User', '<pre>User:'.print_r($_SESSION['user'], true).'</pre>');
        }
    }

    public function build($array){
        
        $this->_data = array();
        $this->_uid = $array['uid'];
        if($array['uid'] != DEFAULT_USER){
            $this->_logged_in = true;
        }
        
        foreach($array as $key=>$value){
            $this->_data[$key] = $value;
        }

        $this->_super_user = $this->_data['super_user'];

        if($this->_data['super_user'] != 'yes'){
            $_SESSION['user_allow_ext'] = 'no';
            $permission_groups = explode(',',$this->_data['groups']); 
            foreach($permission_groups as $group){
                $sql = 'SELECT modules FROM `groups` WHERE `id` = "'.DB::clean($group).'" LIMIT 1';
                $response = DB::q($sql);
                if(count($response)){
                    $this->_permissions = array_merge($this->_permissions, explode(',',$response[0]['modules']) );
                }
            }
            $this->_permissions = array_unique($this->_permissions);
        }else{
            $_SESSION['user_allow_ext'] = 'ok';
        }
        $this->init_usermod();
    }
    
    public function init_usermod(){
        foreach(CMS::$_usermod as $classname){
            $name = '\UserMod\\'.$classname;
            if (class_exists($name)) {
                $this->_modules[] = new $name($this->_uid);
            }
        }
    }

    public function destroy(){
        if($this->_internal){
            $_SESSION['user'] = 0;
        }
        $this->_data = array();
    } 


    /**
     * Check if a module is allowed
     * @param string $key module/page name
     * @return bool 
     */
    public function allowed( $key=false ){
        if(!$key){
            $trace = debug_backtrace();
            $key = $trace[2]['class'];
        }

        if($this->_super_user == 'yes'){
            return true;
        }
        
        foreach($this->_permissions as $v){
            //echo trim(strtolower($v)).':'.trim(strtolower($key)).'<br>';
            if(trim(strtolower($v)) == trim(strtolower($key))){
                return true;
            }
        }
        return false;
    } 
}