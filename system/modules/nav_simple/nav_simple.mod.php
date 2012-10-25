<?php

namespace Module;

class Nav_simple extends Module {

    public static function __registar_callback() {
        \CMS::callstack_add('parse', DEFAULT_CALLBACK_OUTPUT - 1);
    }

    public static function parse() {
        if (\Html::find('{simple_nav}')) {
            self::build();
        }
    }

    public static function build() {
        $sql = 'SELECT title, url FROM `pages` WHERE `status` = \'active\'';
        $list = \DB::q($sql);
        $html = array();
        $html[] = '<ul>';
        foreach ($list as $item) {
            $html[] = '<li><a href="' . $item['url'] . '">' . $item['title'] . '</a></li>';
        }
        $html[] = '</ul>';
        \Html::set('{simple_nav}', implode(PHP_EOL, $html));
    }

}
