<?php

namespace Module;

class Js_jgrowl extends Module {

    public static function __registar_callback() {
        \CMS::callstack_add('add_assets', DEFAULT_CALLBACK_CREATE);
    }

    public static function add_assets() {
        \Html::$_css[] = '<link href="' . PATH_BASE . 'system/modules/js_jgrowl/assets/jquery.jgrowl.css" type="text/css" rel="stylesheet">';
        \Html::$_scripts[] = '<script type="text/javascript" src="' . PATH_BASE . 'system/modules/js_jgrowl/assets/jquery.jgrowl.js"></script>';
    }

}