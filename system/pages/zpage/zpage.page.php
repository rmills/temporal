<?php

namespace Page;

class Zpage extends Page {

    public static $_isrestricted = true;
    static public $_data;
    static public $_status = 500;
    static public $_pid = 0;
    static public $_zonestack = array(
        'z1', 'z2', 'z3', 'z4', 'z5', 'z6', 'z7', 'z8', 'z9', 'z10', 'z11',
        'z12', 'z13', 'z14', 'z15', 'z16', 'z17', 'z18', 'z19', 'z20'
    );

    public static function __registar_callback() {

        if (\CMS::allowed()) {
            \CMS::callstack_add('check_url', 10);
            if (\CMS::$_vars[0] == 'update_zone' && isset($_POST['zone_data'])) {
                \CMS::$_content_type = 'json';
                \CMS::$_page_type = 'zpage';
                \CMS::callstack_add('update', DEFAULT_CALLBACK_PARSE);
            }
        }
    }

    public static function update() {
        self::update_zone(\CMS::$_vars[1], \CMS::$_vars[2], $_POST['zone_data']);
    }

    public static function check_url() {

        $found = false;
        if (\CMS::$_vars[0] == '' && DEFAULT_PAGE_GUEST == 'Zpage') {
            $found = true;
        } elseif (\CMS::$_vars[0] == 'page') {
            $found = true;
        } elseif (\CMS::$_vars[0] != '') {
            self::$_pid = self::fetch_by_url(\CMS::$_vars[0]);
            if (is_numeric(self::$_pid)) {
                if (self::$_pid != 0) {
                    $found = true;
                }
            }
        }

        if ($found) {
            \CMS::$_page_type = 'zpage';
            \CMS::$_content_type = 'html';
            \CMS::callstack_add('create', DEFAULT_CALLBACK_CREATE);
            \CMS::callstack_add('parse', DEFAULT_CALLBACK_PARSE);
        }
        \CMS::callstack_add('output', DEFAULT_CALLBACK_OUTPUT);
    }

    public static function create() {
        if (!self::$_pid) {
            if (\CMS::$_vars[0] == '') {
                self::$_pid = DEFAULT_ZPAGE;
                self::$_data = self::fetch_by_id(self::$_pid);
                self::$_status = 200;
            } elseif (\CMS::$_vars[0] == 'page') {
                $check = self::fetch_by_id(\CMS::$_vars[1]);
                if (is_array($check)) {
                    self::$_pid = \CMS::$_vars[1];
                    self::$_data = $check;
                    self::$_status = 200;
                } else {
                    self::$_status = 404;
                }
            }
        } else {
            self::$_data = self::fetch_by_id(self::$_pid);
            self::$_status = 200;
        }
        \Page\Admin::add_quick_link('<li><a href="{root_doc}admin/page/edit/' . self::$_pid . '">Edit Page</a></li>');
        \CMS::log('Zpage', 'loading page "' . self::$_data['pid'] . '"');
        self::mount_zones();
    }

    public static function parse() {
        \Html::load(self::$_data['template']);
        if (is_array(self::$_data)) {
            foreach (self::$_data as $key => $value) {
                \Html::set('{' . $key . '}', $value);
            }
        }
    }

    public static function output() {
        if (!\Html::$_hascontent && \CMS::$_content_type == 'html' && $_page_type == 'zpage') {
            self::display_404();
        }
    }

    private static function fetch_by_url($url) {
        $url = strtolower(trim($url));
        $sql = 'SELECT pid FROM `pages` WHERE `url` = \'' . \DB::clean($url) . '\' AND `status` = \'active\' LIMIT 1;';
        $response = \DB::q($sql);
        if (is_array($response)) {
            foreach ($response as $item) {
                return $item['pid'];
            }
        }
    }

    public static function fetch_by_id($pid, $all = false) {
        if (!is_numeric($pid)) {
            return false;
        }
        $pid = trim($pid);
        if ($all) {
            $sql = 'SELECT * FROM `pages` WHERE `pid` = \'' . \DB::clean($pid) . '\' LIMIT 1;';
        } else {
            $sql = 'SELECT * FROM `pages` WHERE `pid` = \'' . \DB::clean($pid) . '\' AND `status` = \'active\' LIMIT 1;';
        }
        $response = \DB::q($sql);
        if (is_array($response)) {
            foreach ($response as $item) {
                return $item;
            }
        }
    }

    private static function mount_zones() {
        foreach (self::$_zonestack as $v) {
            if (is_numeric(self::$_data[$v])) {
                self::$_data[$v] = self::fetch_zone(self::$_data[$v]);
            }
        }
    }

    private static function fetch_zone($zid) {
        if (!is_numeric($zid)) {
            return false;
        }
        $zid = trim($zid);
        $sql = 'SELECT z_data FROM `zones` WHERE `zid` = \'' . \DB::clean($zid) . '\' LIMIT 1;';
        $response = \DB::q($sql);
        if (is_array($response)) {
            foreach ($response as $item) {
                return urldecode($item['z_data']);
            }
        }
    }

    private static function display_404() {
        \Html::load('404.html');
    }

    private static function update_zone($zid, $pid, $zdata) {
        $zdate = date("m-d-Y");
        $zdata = urlencode(trim($zdata));
        $sql = '
            INSERT INTO zones (
                `z_data`,
                `z_creation`
            ) VALUES (
                \'' . \DB::clean($zdata) . '\',
                \'' . $zdate . '\'
            )';
        \DB::q($sql);



        $sql = '
            UPDATE `pages` SET 
                    `' . \DB::clean($zid) . '` = \'' . \DB::clean(\DB::$_lastid) . '\'
            WHERE 
                    `pid` = \'' . \DB::clean($pid) . '\' 
            LIMIT 1
	';
        \DB::q($sql);
        \Json::$_body .= json_encode(array('status' => 'ok'));
    }

}

