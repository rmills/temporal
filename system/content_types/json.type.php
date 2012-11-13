<?php
/**
 * @author Ryan Mills <ryan@ryanmills.net> (Primary)
 */
class Json{
    public static $_hascontent = false;
    
    public static function __registar_callback(){
        CMS::callstack_add('parse', DEFAULT_CALLBACK_PARSE+1);
        CMS::callstack_add('output', DEFAULT_CALLBACK_OUTPUT);
    }
    
    public static $_body;
    

    public static function parse(){

    }
    
    public static function output(){
        if(CMS::$_content_type == 'json'){
            header('Pragma: no-cache');
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Content-Disposition: inline; filename="files.json"');
            header('X-Content-Type-Options: nosniff');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
            header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');
            
            echo self::$_body;
        }
    }
}
