<?php

namespace Module;

class Js_jgrowl extends Module {

    public static function __registar_callback() {
        \CMS::callstack_add('parse', DEFAULT_CALLBACK_CREATE);
    }

    public static function add_assets() {
        \Html::set('{footer}', '<link href="' . PATH_BASE . 'system/modules/js_jgrowl/assets/jquery.jgrowl.css" type="text/css" rel="stylesheet">');
        \Html::set('{footer}', '<script type="text/javascript" src="' . PATH_BASE . 'system/modules/js_jgrowl/assets/jquery.jgrowl.js"></script>');
    }

}