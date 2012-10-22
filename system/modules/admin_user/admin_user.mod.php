<?php

class Admin_user extends Module {

    private static $_pagemode = false;
    private static $_status = false;
    private static $_action_complete = false;

    public static function __registar_callback() {
        if (CMS::allowed()) {
            if (CMS::$_vars[0] == 'admin' && CMS::$_vars[1] == 'user') {
                CMS::callstack_add('create', DEFAULT_CALLBACK_CREATE);
                CMS::callstack_add('parse', DEFAULT_CALLBACK_PARSE);
                CMS::callstack_add('set_nav', DEFAULT_CALLBACK_CREATE);
            } else {
                CMS::callstack_add('set_nav', DEFAULT_CALLBACK_CREATE);
            }
        }
    }

    public static function set_nav() {
        Admin::add_link('
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Users<b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="{root_doc}admin/user/add">Add User</a></li>
                    <li><a href="{root_doc}admin/user/editlist">Manage Users</a></li>
                </ul>
            </li>
        ');
    }

    public static function create() {
        Admin::$_subpage = true;
    }

    public static function parse() {
        /* Pre Action Handlers */
        if (CMS::$_vars[3] == 'submit') {
            switch (CMS::$_vars[2]) {
                case 'edit':
                    self::edit_user_submit();
                    break;

                case 'add':
                    self::add_user_submit();
                    break;
            }
        }


        /* Page Content Handlers */
        switch (CMS::$_vars[2]) {
            case 'add':
                self::$_pagemode = 'add';
                self::add_user();
                HTML::set('{userstitle}', 'Add User');
                break;

            case 'edit':
                self::$_pagemode = 'edit';
                self::edit_user();
                HTML::set('{userstitle}', 'Update User');
                break;

            case 'editlist':
                self::$_pagemode = 'editlist';
                self::edit_list();
                HTML::set('{userstitle}', 'Manage Users');
                break;

            case 'confirmdelete':
                self::$_pagemode = 'confirmdelete';
                self::confirmdelete_user();
                HTML::set('{userstitle}', 'Delete User');
                break;

            case 'delete':
                self::$_pagemode = 'delete';
                self::delete_user();
                HTML::set('{userstitle}', 'Delete User');
                break;

            case 'suspend':
                self::$_pagemode = 'suspend';
                self::suspend_user();
                HTML::set('{userstitle}', 'Suspend User');
                break;

            case 'restore':
                self::$_pagemode = 'restore';
                self::restore_user();
                HTML::set('{userstitle}', 'Restore User');
                break;
        }

        if (self::$_status) {
            HTML::set('{status}', self::$_status);
        }
    }

    public static function add_user() {
        if (!self::$_action_complete) {
            $html = self::block('adduser.html');
            HTML::set('{admin_content}', $html);

            if (isset($_POST['email'])) {
                HTML::set('{setemail}', $_POST['email']);
            } else {
                HTML::set('{setemail}');
            }

            if (isset($_POST['name'])) {
                HTML::set('{setname}', $_POST['name']);
            } else {
                HTML::set('{setname}');
            }

            if (isset($_POST['password'])) {
                HTML::set('{setpassword}', $_POST['password']);
            } else {
                HTML::set('{setpassword}');
            }

            if (isset($_POST['super_user'])) {
                HTML::set('{setsuperuser}', 'checked="checked"');
            } else {
                HTML::set('{setsuperuser}');
            }
            HTML::set('{status}');
        }
    }

    public static function add_user_submit() {
        $email = trim(strtolower($_POST['email']));
        $name = trim($_POST['name']);
        $salt = Crypto::random_key();
        $password = md5($salt . trim($_POST['password']) . $salt);

        $check = self::dup_email_check($email);
        if ($check) {
            self::$_action_complete = false;
            self::$_status = '<div class="error">Email already in use</div>';
            return false;
        }

        if (isset($_POST['super_user'])) {
            $super_user = 'yes';
        } else {
            $super_user = 'no';
        }

        $sql = '
            INSERT INTO users (
                `email`,
                `name`,
                `password`,
                `salt`,
                `super_user`,
                `groups`
            ) VALUES (
                \'' . DB::clean($email) . '\',
                \'' . DB::clean($name) . '\',
                \'' . DB::clean($password) . '\',
                \'' . DB::clean($salt) . '\',
                \'' . $super_user . '\',
                \'1\'
		)';
        DB::q($sql);
        echo DB::$_lasterror;
        $html = '<h4>Success</h4><hr /><p>User "' . $email . '" created.</p><div class="form-actions"><a class="btn btn-info" href="{root_doc}admin/user/add/">Add User</a></div>';
        HTML::set('{admin_content}', $html);
        self::$_action_complete = true;
    }

    public static function edit_list() {
        $html = self::block('editlist.html');
        $users = array();
        $sql = 'SELECT * FROM `users`';
        $list = DB::q($sql);
        foreach ($list as $v) {
            if ($v['status'] != 'active') {
                $status = '<a class="btn btn-danger disabled">' . $v['status'] . '</a>';
            } else {
                $status = '<a class="btn btn-info disabled">' . $v['status'] . '</a>';
            }

            if ($v['super_user'] == 'yes') {
                $super = '<a class="btn btn-warning disabled">Enabled</a>';
            } else {
                $super = '<a class="btn btn-info disabled">Disabled</a>';
            }
            $users[] = '
                <tr>
                    <td>' . $v['email'] . '</td>
                    <td>' . $v['name'] . '</td>
                    <td>' . $super . '</td>
                    <td>' . $status . '</td>
                    <td>';
            $users[] = '<a class="btn btn-success" href="{root_doc}admin/user/edit/' . $v['uid'] . '"><i class="icon-edit icon-white"></i> edit</a> ';

            if ($v['status'] == 'active') {
                $users[] = '<a class="btn btn-warning" href="{root_doc}admin/user/suspend/' . $v['uid'] . '"><i class="icon-lock icon-white"></i> suspend</a> ';
            } else {
                $users[] = '<a class="btn btn-info" href="{root_doc}admin/user/restore/' . $v['uid'] . '"><i class="icon-white icon-share-alt"></i> restore</a> ';
            }

            $users[] = '<a class="btn btn-danger" href="{root_doc}admin/user/confirmdelete/' . $v['uid'] . '"><i class="icon-white icon-remove"></i> delete</a> 
                    </td>
                </tr>
            ';
        }
        $html = str_replace('{users}', implode(PHP_EOL, $users), $html);
        HTML::set('{admin_content}', $html);

        HTML::set('{status}');
    }

    public static function edit_user() {
        if (!self::$_action_complete) {
            $html = self::block('edituser.html');
            if (is_numeric(CMS::$_vars[3])) {
                $uid = CMS::$_vars[3];
            } else {
                $uid = $_POST['uid'];
            }

            $user = new User();
            $user->init($uid, false);

            HTML::set('{admin_content}', $html);

            HTML::set('{status}');
            HTML::set('{setemail}', $user->_data['email']);
            $name = str_replace('"', '&quot;', $user->_data['name']);
            HTML::set('{setname}', $name);
            HTML::set('{uid}', $uid);
            if ($user->_data['super_user'] == 'yes') {
                HTML::set('{setsuperuser}', 'checked="checked"');
            } else {
                HTML::set('{setsuperuser}');
            }
        }
    }

    public static function edit_user_submit() {
        $email = trim(strtolower($_POST['email']));
        $name = trim($_POST['name']);
        
        $salt = Crypto::random_key();
        
        $password = md5($salt . trim($_POST['password']) . $salt);
        $uid = $_POST['uid'];

        $check = self::dup_email_check($email, $uid);
        if ($check) {
            self::$_action_complete = false;
            self::$_status = '<div class="error">Email already in use</div>';
            return false;
        }

        if (isset($_POST['super_user'])) {
            $super_user = 'yes';
        } else {
            $super_user = 'no';
        }

        if ($_POST['password'] != '') {
            echo 1;
            $sql = 'UPDATE `users` SET 
            `email` = \'' . DB::clean($email) . '\',
            `name` = \'' . DB::clean($name) . '\',
            `password` = \'' . DB::clean($password) . '\',
             `salt` = \'' . DB::clean($salt) . '\',
            `super_user` = \'' . DB::clean($super_user) . '\'
            WHERE `uid` = \'' . DB::clean($uid) . '\' LIMIT 1';
        } else {
            $sql = 'UPDATE `users` SET 
            `email` = \'' . DB::clean($email) . '\',
            `name` = \'' . DB::clean($name) . '\',
            `super_user` = \'' . DB::clean($super_user) . '\'
            WHERE `uid` = \'' . DB::clean($uid) . '\' LIMIT 1';
        }

        DB::q($sql);
        $html = '<h4>Success</h4><hr /><p>User "' . $email . '" updated.</p><div class="form-actions"><a class="btn btn-info" href="{root_doc}admin/user/editlist/">Return</a></div>';
        HTML::set('{admin_content}', $html);
        self::$_action_complete = true;
    }

    public static function confirmdelete_user() {
        $uid = CMS::$_vars[3];
        $user = new User();
        $user->init($uid, false);


        $html = '<h4>Confirm Delete</h4><hr /><p>Are you sure you want to delete the user: "' . $user->_data['email'] . '"?</p><div class="form-actions"><a class="btn btn-warning" href="{root_doc}admin/user/delete/' . $uid . '">Delete</a> <a class="btn btn-info" href="{root_doc}admin/user/editlist/">Cancel</a></div>';
        HTML::set('{admin_content}', $html);
    }

    public static function delete_user() {
        $uid = CMS::$_vars[3];
        if ($uid !== '1') {
            $sql = 'DELETE FROM `users` WHERE `uid` = \'' . DB::clean($uid) . '\' LIMIT 1';
            DB::q($sql);

            $html = '<h4>User Deleted</h4><hr /><p>User removed.</p><div class="form-actions"><a class="btn btn-info" href="{root_doc}admin/user/editlist/">Return</a></div>';
            HTML::set('{admin_content}', $html);
        } else {
            $html = '<h4>Failed</h4><hr /><p>Guest user can not be removed.</p><div class="form-actions"><a class="btn btn-info" href="{root_doc}admin/user/editlist/">Return</a></div>';
            HTML::set('{admin_content}', $html);
        }
    }

    public static function suspend_user() {
        $uid = CMS::$_vars[3];
        $user = new User();
        $user->init($uid, false);
        if ($uid !== '1') {
            if ($uid == CMS::$_user->_data['uid']) {
                $html = '<h4>Failed</h4><hr /><p>You can not suspend yourself.</p><div class="form-actions"><a class="btn btn-info" href="{root_doc}admin/user/editlist/">Return</a></div>';

                HTML::set('{admin_content}', $html);
                return false;
            } else {
                $sql = 'UPDATE `users` SET `status` = \'suspended\' WHERE `uid` = \'' . DB::clean($uid) . '\' LIMIT 1';
                DB::q($sql);
                $html = '<h4>Success</h4><hr /><p>User ' . $user->_data['email'] . ' suspended.</p><div class="form-actions"><a class="btn btn-info" href="{root_doc}admin/user/editlist/">Return</a></div>';

                HTML::set('{admin_content}', $html);
                return true;
            }
        } else {
            $html = '<h4>Failed</h4><hr /><p>Guest user can not be suspended.</p><div class="form-actions"><a class="btn btn-info" href="{root_doc}admin/user/editlist/">Return</a></div>';
            HTML::set('{admin_content}', $html);
            return false;
        }
    }

    public static function restore_user() {
        $uid = CMS::$_vars[3];
        $user = new User();
        $user->init($uid, false);
        $sql = 'UPDATE `users` SET `status` = \'active\' WHERE `uid` = \'' . DB::clean($uid) . '\' LIMIT 1';
        DB::q($sql);
        $html = '<h4>Success</h4><hr /><p>User ' . $user->_data['email'] . ' access restored.</p><div class="form-actions"><a class="btn btn-info" href="{root_doc}admin/user/editlist/">Return</a></div>';

        HTML::set('{admin_content}', $html);
        return true;
    }

    public static function dup_email_check($email, $uid = 0) {
        $sql = 'SELECT * FROM `users` WHERE `email` = \'' . $email . '\'';
        $list = DB::q($sql);
        if (is_array($list)) {
            foreach ($list as $v) {
                if ($uid != $v['uid']) {
                    return true;
                }
            }
        }
        return false;
    }

}
