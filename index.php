<?php
/**
 * @author Ryan Mills <ryan@ryanmills.net> (Primary)
 * 
 */
define('BUILD_TIME', microtime());
session_start();

/* Local Config */
date_default_timezone_set('America/Los_Angeles');

/* Primary Includes */

if(is_file('site/config.inc.php')){
    include 'site/config.inc.php';
}

include 'system/system.config.php';


/*
 * Set Debug Options
 */
if(ENABLE_DEBUG){
    error_reporting(-1);
}else{
    error_reporting(0);
}


/* Secondary Includes */
require 'system/core/crypto.class.php';
require 'system/core/db.class.php';
require 'system/core/cms.class.php';
require 'system/core/module.class.php';
require 'system/core/page.class.php';
require 'system/core/user.class.php';
require 'system/core/usermod.class.php';
require 'system/core/image.class.php';
require 'system/core/cache.db.class.php';

if(ENABLE_MAIL){
    require_once('system/core/phpmail/class.phpmailer.php');
}

\DB::init();

if(ENABLE_CACHE){
    $cache = new CacheDB($_SERVER['REQUEST_URI'], false);
    if($cache->_status){
        $body = $cache->data();
        if(isset($_SESSION['super_user'])){
            if($_SESSION['super_user'] == 'ok'){
                $body = str_replace('{buildtime}', "Build Time: ".\CMS::get_build_time(), $body);
            }else{
                $body = str_replace('{buildtime}', '', $body);
            }
        }else{
            $body = str_replace('{buildtime}', '', $body);
        }
        echo $body;
    }else{
        /* Tip the first domino */
        CMS::init($config);
        CMS::callstack_run(1); //int is starting point in stack
        if(ENABLE_DEBUG_FORCED_OUTPUT){
            echo CMS::log_display_html();
            echo CMS::callstack_display_html();
        }
    }
}else{
    /* Tip the first domino */
    CMS::init($config);
    CMS::callstack_run(1); //int is starting point in stack
    if(ENABLE_DEBUG_FORCED_OUTPUT){
        echo CMS::log_display_html();
        echo CMS::callstack_display_html();
    }
}


