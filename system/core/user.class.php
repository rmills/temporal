<?php
class User {
	public $_data = array();
    public $_logged_in = false;
    public $_internal = false;
    public $_error = false;
    public $_super_user = false;
    public $_permissions = array();
	
	public function init($id = false, $internal = true){
		
        if($internal){
            $this->_internal = true;
            $this->check_logout();
            $this->check_login();

            if(!isset($_SESSION['user'])){
                $this->load(DEFAULT_USER);
            }else{
                if(is_numeric($_SESSION['user']) && $_SESSION['user'] > 0){
                    $this->load($_SESSION['user']);
                }else{
                    $this->load(DEFAULT_USER);
                }
            }
        }else{
            $this->load($id);
            $this->_internal = false;
        }
	}
	
	public function load($id){
        if($this->_internal){
            $_SESSION['user'] = $id;
        }
		$sql = 'SELECT * FROM `users` WHERE `uid` = '.DB::clean($id).' LIMIT 1';
		$response = DB::q($sql);
		if(count($response)){
			$this->build($response[0]);
		}
        if($this->_internal){
            CMS::log('User', 'User Loaded:'.$id);
            CMS::log('User', '<pre>User:'.print_r($_SESSION['user'], true).'</pre>');
        }
	}
	
	public function build($array){
		$this->_data = array();
        if($array['uid'] != DEFAULT_USER){
            $this->_logged_in = true;
        }
		foreach($array as $key=>$value){
			$this->_data[$key] = $value;
		}
        
        $this->_super_user = $this->_data['super_user'];
        
        if($this->_data['super_user'] != 'yes'){
            $_SESSION['user_allow_ext'] = 'no';
            $permission_groups = explode(',',$this->_data['permissions']); 
            foreach($permission_groups as $group){
                $sql = 'SELECT modules FROM `permissions` WHERE `id` = "'.DB::clean($group).'" LIMIT 1';
                $response = DB::q($sql);
                if(count($response)){
                    $this->_permissions = array_merge($this->_permissions, explode(',',$response[0]['modules']) );
                }
            }
            $this->_permissions = array_unique($this->_permissions);
        }else{
            $_SESSION['user_allow_ext'] = 'ok';
        }
	}
	
	public function destroy(){
        if($this->_internal){
            $_SESSION['user'] = 0;
        }
        $this->_data = array();
	}
	
	public function check_logout(){
		if(isset($_GET['logout']) || CMS::$_vars[0] == 'logout' && $this->_internal == 'normal'){
			$this->destroy();
            CMS::redirect('home');
		}
	}
	
	public function check_login(){
		if( isset($_POST['do_login']) ){
			if( isset( $_POST['email'] ) && isset( $_POST['pass'] ) ){
				$this->auth($_POST['email'], $_POST['pass']);
			}
		}
	}
	
	public function auth($user, $pass){
		$hashed = md5(PASS_SALT.$pass.PASS_SALT);
		$email = strtolower(stripslashes(trim($user)));
		$email = substr($email, 0, 60);
		$sql = 'SELECT uid,pass,status FROM `users` WHERE `email` = "'.DB::clean($user).'" LIMIT 1';
		$response = DB::q($sql);
		if(count($response)){
			if($hashed === $response[0]['pass']){
                if($response[0]['status'] == 'active'){
                    $this->load($response[0]['uid']);
                }else{
                    $this->_error = 'User account has been suspended';
                }
			}else{
				CMS::log('User', 'password does not match email');
			}
		}else{
			CMS::log('User', 'email address not found');
		}
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
			if(trim(strtolower($v)) == trim(strtolower($key))){
				return true;
			}
		}
		return false;
	} 
}