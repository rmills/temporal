<?php

if(!defined('PROFILE_DISPLAY_MODE')){
    define('PROFILE_DISPLAY_MODE', 'embed');
}

include 'userprofile.class.php';

class Profile extends Page{
    static public $_subpage = false;
    
    public static function active(){
        if(!\CMS::$_user->_logged_in){
            \CMS::redirect('login');
        }
        \CMS::callstack_add('setup', DEFAULT_CALLBACK_SETUP);
        \CMS::callstack_add('parse', DEFAULT_CALLBACK_PARSE);
    }
    
    
    public static function setup(){
        \CMS::$_page_type = 'profile';
        \CMS::$_content_type = 'html';
        \Html::load();
    }
    
    
    public static function parse(){
        if(!self::$_subpage){
            self::build_profile();
        }
    }
    
    
    public static function build_profile(){
        if(PROFILE_DISPLAY_MODE == 'embed'){
            \Html::set( '{content}', self::block('layout_embed.html') );
        }elseif(PROFILE_DISPLAY_MODE == 'standalone'){
            \Html::set( '{content}', self::block('layout_standalone.html') );
        }
    }
}