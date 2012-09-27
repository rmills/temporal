<?php
/**
 * @author Ryan Mills <ryan@ryanmills.net> (Primary)
 * 
 */

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

if(ENABLE_MAIL){
    require_once('system/core/phpmail/class.phpmailer.php');
}


/* Tip the first domino */
CMS::init($config);
CMS::callstack_run(1); //int is starting point in stack

/* Disable debug output */
if(ENABLE_DEBUG_FORCED_OUTPUT){
    echo CMS::log_display_html();
    echo CMS::callstack_display_html();
}