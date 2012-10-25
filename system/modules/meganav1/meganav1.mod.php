<?php

namespace Module;

class Meganav1 extends Module {

    private static $pages = array();
    private static $menu = array();

    public static function __registar_callback() {
        \CMS::callstack_add('parse', DEFAULT_CALLBACK_OUTPUT - 1);
    }

    public static function parse() {
        if (\Html::find('{meganav1}')) {
            self::load_pages();
            $check = self::build_array();
            if ($check) {
                \Html::set('{meganav1}', self::build_html());
            }
        }
    }

    public static function load_pages() {
        $sql = 'SELECT * FROM `pages` WHERE `status` = \'active\' AND `published` = \'yes\' ORDER BY `weight` ASC';
        $list = \DB::q($sql);
        foreach ($list as $item) {
            self::$pages[] = $item;
        }
    }

    public static function build_array() {
        if (count(self::$pages) < 1) {
            return false;
        }

        /**
         * Fine Root Items 
         */
        foreach (self::$pages as $page) {
            if ($page['parent'] == '0') {
                self::$menu[] = array($page['pid'], $page['menu_title'], $page['url'], self::get_children($page['pid']));
            }
        }

        return true;
    }

    public static function build_html() {
        \Html::$_css[] = '<link href="' . PATH_BASE . 'system/modules/meganav1/assets/meganav1.css" type="text/css" rel="stylesheet">';
        $html = array();
        $html[] = '<div id="meganav1">';
        $html[] = '<ul>';
        foreach (self::$menu as $main) {
            $html[] = '<li><a href="{root_doc}' . $main[2] . '">' . $main[1] . '</a>' . self::build_list($main[3], true) . '</li>';
        }
        $html[] = '</ul>';
        $html[] = '</div>';
        return implode(PHP_EOL, $html);
    }

    public static function build_list($item, $divsub = false) {
        $found = false;
        $html = array();

        if ($divsub) {
            $html[] = '<div>';
        }

        $html[] = '<ul>';
        foreach ($item as $main) {
            $found = true;
            $html[] = '<li><a href="{root_doc}' . $main[2] . '">' . $main[1] . '</a>' . self::build_list($main[3]) . '</li>';
        }
        $html[] = '</ul>';

        if ($divsub) {
            $html[] = '</div>';
        }

        if ($found) {
            return implode(PHP_EOL, $html);
        } else {
            return false;
        }
    }

    public static function get_children($pid) {
        $items = array();
        foreach (self::$pages as $page) {
            if ($page['parent'] == $pid) {
                $items[] = array($page['pid'], $page['menu_title'], $page['url'], self::get_children($page['pid']));
            }
        }
        return $items;
    }

}
