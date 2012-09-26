<?php
/**
 * This is the base configuration. You should not make changes in here
 * All changes should be added to the site/config.inc.php
 * 
 */

if(!defined('ENABLE_DEBUG')){
    define('ENABLE_DEBUG', false);
}

if(!defined('ENABLE_DEBUG_FORCED_OUTPUT')){
    define('ENABLE_DEBUG_FORCED_OUTPUT', false);
}

if(!defined('ENABLE_DEBUG_TRACE')){
    define('ENABLE_DEBUG_TRACE', false);
}

if(!defined('SITE_NAME')){
    define('SITE_NAME', 'Temporal'); // Name of your site
}

if(!defined('BRANDING_FOLDER')){
    define('BRANDING_FOLDER', 'default'); //Branding package
}

if(!defined('PATH_LAYOUT_ROOT')){
    /*
    * Setting this will disable the default layout, if this folder is not found
    * the system will enable the default template.
    */
    define('PATH_LAYOUT_ROOT', 'site/layout/test/');
}

if(!defined('DATABASE_HOST')){
    define('DATABASE_HOST', 'localhost');
}

if(!defined('DATABASE_USER')){
    define('DATABASE_USER', 'root');
}

if(!defined('DATABASE_PASSWORD')){
    define('DATABASE_PASSWORD', '');
}

if(!defined('DATABASE_TABLE')){
    define('DATABASE_TABLE', 'temporal_v7');
}

if(!defined('DOMAIN')){
    define('DOMAIN', '127.0.0.1');
}

if(!defined('PASS_SALT')){
    define('PASS_SALT', 'guejd;ag873hasf72yuwea_sd38yfaskdfh8skf2');
}

if(!defined('DEFAULT_USER')){
    #Default user loaded, i.e. guest
    define('DEFAULT_USER', 1);
}

if(!defined('PATH_BASE')){
    #Path to layout folder
    define('PATH_BASE', '/');
}

if(!defined('PATH_LAYOUT_ROOT_DEFAULT')){
    #Path to layout folder
    define('PATH_LAYOUT_ROOT_DEFAULT', 'system/layout/default/');
}

if(!defined('PATH_LAYOUT_ROOT_ADDON')){
    #Path to layout addon folder
    define('PATH_LAYOUT_ROOT_ADDON', 'site/layout/');
}

if(!defined('LAYOUT_DEFAULT')){
    #Default html, includes all wrapping elements
    define('LAYOUT_DEFAULT', 'default.html');
}

if(!defined('PATH_MODULE_ROOT')){
    #Path to module folder
    define('PATH_MODULE_ROOT', 'system/modules/');
}

if(!defined('PATH_MODULE_ROOT_ADDON')){
    #Path to module folder addon
    define('PATH_MODULE_ROOT_ADDON', 'site/modules/');
}

if(!defined('PATH_PAGE_ROOT')){
    define('PATH_PAGE_ROOT', 'system/pages/');
}

if(!defined('PATH_PAGE_ROOT_ADDON')){
    define('PATH_PAGE_ROOT_ADDON', 'site/pages/');
}

if(!defined('PATH_CONTENT_TYPES_ROOT')){
    define('PATH_CONTENT_TYPES_ROOT', 'system/content_types/');
}

if(!defined('PATH_CONTENT_TYPES_ROOT_ADDON')){
    define('PATH_CONTENT_TYPES_ROOT_ADDON', 'site/content_types/');
}

if(!defined('PATH_MEDIA_LOCAL_ROOT')){
    define('PATH_MEDIA_LOCAL_ROOT', 'D:/!!git/temporal/system/upload/');
}

if(!defined('PATH_MEDIA_REL_ROOT')){
    define('PATH_MEDIA_REL_ROOT', '/system/upload/');
}

if(!defined('DEFAULT_PAGE_AUTH')){
    define('DEFAULT_PAGE_AUTH', 'Zpage');
}

if(!defined('DEFAULT_PAGE_GUEST')){
    define('DEFAULT_PAGE_GUEST', 'Zpage');
}

if(!defined('DEFAULT_ZPAGE')){
    define('DEFAULT_ZPAGE', 1);
}

if(!defined('MAX_ZONES')){
    define('MAX_ZONES', 20);
}

if(!defined('DEFAULT_PROTOCOL')){
    define('DEFAULT_PROTOCOL', 'http://');
}

if(!defined('DEFAULT_CALLBACK_SETUP')){
    define('DEFAULT_CALLBACK_SETUP', 10);
}

if(!defined('DEFAULT_CALLBACK_CREATE')){
    define('DEFAULT_CALLBACK_CREATE', 20);
}

if(!defined('DEFAULT_CALLBACK_PARSE')){
    define('DEFAULT_CALLBACK_PARSE', 30);
}

if(!defined('DEFAULT_CALLBACK_OUTPUT')){
    define('DEFAULT_CALLBACK_OUTPUT', 50);
}



$config = array();
$config['vars'] = array
(
    'uvar1',
    'uvar2',
    'uvar3',
    'uvar4',
    'uvar5',
    'uvar6',
    'uvar7'
);
if( is_dir(PATH_LAYOUT_ROOT) ){
    $config['path_layout'] = PATH_LAYOUT_ROOT;
    $config['path_layout_default'] = PATH_LAYOUT_ROOT.LAYOUT_DEFAULT;
}else{
    $config['path_layout'] = PATH_LAYOUT_ROOT_DEFAULT;
    $config['path_layout_default'] = PATH_LAYOUT_ROOT_DEFAULT.LAYOUT_DEFAULT; 
}

/*
 
IGNORE, just to refrence from old version
 

if($_SERVER['HTTP_HOST'] == '127.0.0.1'){
	define('ENVIRONMENT', 'local'); 
    define('DOMAIN', '127.0.0.1');
}else{
	define('ENVIRONMENT', 'live');
    define('DOMAIN', 'DOMAIN_NOT SET');
}


$config = array();

$config['domain'] = DOMAIN;
$config['root'] = $_SERVER['DOCUMENT_ROOT'].'/';


$config['vars'] = array
(
    'uvar1',
    'uvar2',
    'uvar3',
    'uvar4',
    'uvar5',
    'uvar6',
    'uvar7'
);




$config['media'] = array();
$config['media']['image'] = array(
  'jpg'  
);
$config['media']['video'] = array(
  'mp4',
  'mov'
);
$config['media']['document'] = array(
  'pdf',
  'txt',
  'rtf',
  'docx',
  'doc'
);

  
 */