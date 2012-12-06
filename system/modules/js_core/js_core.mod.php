<?php

namespace Module;

class Js_core extends Module {
    public static $_isrestricted = true;
    public static function __registar_callback() {
        \CMS::callstack_add('add_links', DEFAULT_CALLBACK_PARSE - 1);
        
    }

    public static function add_links() {
        \Html::set('{scripts}', '<script src="{root_doc}system/inc/js/jquery.temporal.js" type="text/javascript"></script>');
    }

}