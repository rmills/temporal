<?php
/**
 * @author Ryan Mills <ryan@ryanmills.net> (Primary)
 * 
 * Login page
 */
class Login extends Page {
	
    private static $_fail_strings = array(
        'Fail! Are you sure you<br />belong here?',
        'Nope, thats not vaild!',
        'Oh noes, you failed!',
        'Got fail? Yes you do.',
        'Well thats not right...'
    );
    
    public static function active(){
        if(CMS::allowed()){
            CMS::callstack_add('setup', DEFAULT_CALLBACK_SETUP);
            CMS::callstack_add('parse', DEFAULT_CALLBACK_PARSE);
        }
    }
    
    public static function setup(){
        CMS::$_page_type = 'login';
        CMS::$_content_type = 'html';
        if(USER_TYPE == 'admin'){
            Html::template( self::block('admin.html') );
        }else{
            Html::load();
            Html::set('{content}', self::block('community.html') );
        }
        Html::set('{login_error}');
    }
    
    public static function parse(){
        if(isset($_POST['do_login'])){
            if(!CMS::$_user->_logged_in){
                    if(CMS::$_user->_error){
                        Html::set('{login_error}', '<p class="alert alert-error">'.CMS::$_user->_error.'</p>');
                    }else{
                        Html::set('{login_error}', '<p class="alert alert-error">'.self::$_fail_strings[rand(0, count(self::$_fail_strings)-1)].'</p>');
                    }
            }else{
                header('Location: '.DEFAULT_PROTOCOL.DOMAIN);
                die();
            }
        }
    }
}
