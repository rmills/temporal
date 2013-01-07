<?php
namespace Module;
class Parser{
    
    public static $_isrestricted = false;


    public static function __registar_callback() {
        \CMS::callstack_add('reg', DEFAULT_CALLBACK_CREATE);
    }
    
    public static function reg(){
        \Parser::register(__CLASS__);
    }
    
    public static function test1($obj){
        return print_r($obj, TRUE);
    }
}