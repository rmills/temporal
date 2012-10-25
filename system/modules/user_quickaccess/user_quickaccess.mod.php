<?php

namespace Module;

class User_quickaccess extends Module {

    public static function __registar_callback() {
        \CMS::callstack_add('parse', DEFAULT_CALLBACK_PARSE + 1);
    }

    public static function parse() {
        if (USER_TYPE == 'community') {
            if (\Html::find('{user_quickaccess}')) {
                if (isset($_SESSION['user'])) {
                    if ($_SESSION['user'] == DEFAULT_USER || $_SESSION['user'] == 0) {
                        self::build_guest();
                    } else {
                        self::build_loggedin();
                    }
                } else {
                    self::build_guest();
                }
            }
        } else {
            \Html::set('{user_quickaccess}');
        }
    }

    public static function build_guest() {
        \Html::set("{user_quickaccess}", '
            <div class="btn-group">
            <a class="btn-mini dropdown-toggle" data-toggle="dropdown" href="#">
            ' . \CMS::$_user->_data['name'] . '
            <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
            <li><a href="{root_doc}login">login</a></li>
            <li><a href="{root_doc}register">register</a></li>
            </ul>
            </div>
        ');
    }

    public static function build_loggedin() {
        \Html::set("{user_quickaccess}", '
            <div class="btn-group">
            <a class="btn-mini dropdown-toggle" data-toggle="dropdown" href="#">
            ' . \CMS::$_user->_data['name'] . '
            <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
            <li><a href="{root_doc}profile">profile</a></li>
            <li><a href="{root_doc}logout">logout</a></li>
            </ul>
            </div>
        ');
    }

}