<?php

class User {
    /**
     * @var int user id  
     */
    public $_uid = 0;
    
    /**
     * @var array all data from db row for user
     */
    public $_data = array();
    
    /**
     * @var bool check if user was loaded from the database 
     */
    public $_logged_in = false;
    
    /**
     * @var bool Legacy, dont use
     */
    public $_internal = false;
    
    /**
     * @var bool load errors
     */
    public $_error = false;
    
    /**
     * @var bool super user flag
     */
    public $_super_user = false;
    
    /**
     * @var bool super user flag
     */
    public $_mod_user = false;
    
    /**
     * @var array permissions stack
     */
    public $_permissions = array();
    
    /**
     * @var array stack of UserMod() loaded
     */
    public $_modules = array();
    
    /**
     * Load a user
     * @param int $id
     */
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
    
    /**
     * Inflate user based on array
     * @param array $array
     */
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
        $this->_mod_user = $this->_data['mod_user'];

        if($this->_data['super_user'] != 'yes'){
            $_SESSION['user_allow_ext'] = 'no';
            $permission_groups = explode(',',$this->_data['groups']); 
            $this->_data['groups'] = $permission_groups;
            foreach($permission_groups as $group){
                $sql = 'SELECT modules FROM `groups` WHERE `id` = "'.DB::clean($group).'" LIMIT 1';
                $response = DB::q($sql);
                if(count($response)){
                    $this->_permissions = array_merge($this->_permissions, explode(',',$response[0]['modules']) );
                }
            }
            $this->_permissions = array_unique($this->_permissions);
        }else{
            $this->_data['groups'] = array();
            $_SESSION['super_user'] = 'yes';
            $_SESSION['user_allow_ext'] = 'ok';
        }
        $this->init_usermod();
    }
    
    /**
     * Call all usermods so they can register
     */
    public function init_usermod(){
        foreach(CMS::$_usermod as $classname){
            $name = '\UserMod\\'.$classname;
            if (class_exists($name)) {
                $this->_modules[$classname] = new $name($this->_uid);
            }
        }
    }
    
    /**
     * Deflate user and clear session data
     */
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