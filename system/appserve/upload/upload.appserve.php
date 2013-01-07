<?php
namespace Appserve;
static $_isrestricted = true;
class Upload extends Appserve{
    public static $fail =  array('status'=>'fail', 'reason'=>'Bad/missing handler or type:');
    static $_isrestricted = true;
    public static function __registar_callback() {
        if(\CMS::allowed()){
            \CMS::callstack_add('setup', DEFAULT_CALLBACK_SETUP);
        }
    }
    
    public static function active(){
        \CMS::$_page_type = 'upload';
        \CMS::$_content_type = 'json';
        if( isset($_POST['handler']) ){
            if( array_key_exists($_POST['handler'], Appserve::$_apps) ){
                $callback = '\Appserve\\'.$_POST['handler'];
                $data = $callback::call();
                \Json::body($data);
            }else{
                \Json::body(array('status'=>'fail', 'reason'=>'bad handler "'.$_POST['handler'].'"'));
            }
        }else{
            \Json::body(array('status'=>'fail', 'reason'=>'Bad/missing handler or type'));
        }
    }
    
    public static function setup(){
        \Html::set('{footer}', '<script type="text/javascript" src="{root_doc}system/appserve/upload/assets/js/header.js"></script>');
        \Html::set('{footer}', '<script type="text/javascript" src="{root_doc}system/appserve/upload/assets/js/util.js"></script>');
        \Html::set('{footer}', '<script type="text/javascript" src="{root_doc}system/appserve/upload/assets/js/button.js"></script>');
        \Html::set('{footer}', '<script type="text/javascript" src="{root_doc}system/appserve/upload/assets/js/handler.base.js"></script>');
        \Html::set('{footer}', '<script type="text/javascript" src="{root_doc}system/appserve/upload/assets/js/handler.form.js"></script>');
        \Html::set('{footer}', '<script type="text/javascript" src="{root_doc}system/appserve/upload/assets/js/handler.xhr.js"></script>');
        \Html::set('{footer}', '<script type="text/javascript" src="{root_doc}system/appserve/upload/assets/js/uploader.basic.js"></script>');
        \Html::set('{footer}', '<script type="text/javascript" src="{root_doc}system/appserve/upload/assets/js/dnd.js"></script>');
        \Html::set('{footer}', '<script type="text/javascript" src="{root_doc}system/appserve/upload/assets/js/uploader.js"></script>');
        \Html::set('{footer}', '<script type="text/javascript" src="{root_doc}system/appserve/upload/assets/js/jquery-plugin.js"></script>');
        \Html::set('{css}', '<link rel="stylesheet" href="{root_doc}system/appserve/upload/assets/fineuploader.css" type="text/css" media="screen, projection">');
    }
}