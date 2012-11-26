<?php

class DB {

    private static $__connection = false;
    private static $__querys = array();
    public static $_lasterror = false;
    public static $_lastid = 0;

    public static function init() {
        $DB = false;
        self::$__connection = new mysqli(
                        DATABASE_HOST,
                        DATABASE_USER,
                        DATABASE_PASSWORD,
                        DATABASE_TABLE
        );

        if (mysqli_connect_errno()) {
            printf("DB Connect failed: %s\n", mysqli_connect_error());
            exit();
        }
    }

    public static function q($sql) {
        /** store query for debug * */
        self::$__querys[] = $sql;

        /** run * */
        $data = array();
        $free_result = false;
        $result = mysqli_query(self::$__connection, $sql);
        if (gettype($result) != 'boolean') {
            $free_result = true;
            while ($r = $result->fetch_assoc()) {
                $data[] = $r;
            }
        } else {
            if ($result) {
                $data = $result;
            } else {
                $error = mysqli_error(self::$__connection);
                if ($error) {
                    CMS::log('DB', 'SQL ERROR (next two lines)', 2);
                    CMS::log('DB', $sql, 2);
                    CMS::log('DB', $error, 2);
                }
                $data = false;
            }
        }

        self::$_lastid = mysqli_insert_id(self::$__connection);
        self::$_lasterror = mysqli_error(self::$__connection);
        if ($free_result) {
            mysqli_free_result($result);
        }
        return $data;
    }

    public static function clean($input) {
        return mysqli_real_escape_string(self::$__connection, $input);
    }

}