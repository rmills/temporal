<?php

class Highslide_js extends Module{
    public static function __registar_callback(){
		CMS::callstack_add('add_assets', DEFAULT_CALLBACK_CREATE);
    }
    
    public static function add_assets(){
		Html::$_css[] = '<link href="'.PATH_BASE.'system/modules/highslide_js/assets/highslide.css" type="text/css" rel="stylesheet">';
		Html::$_scripts[] = '<script type="text/javascript" src="'.PATH_BASE.'system/modules/highslide_js/assets/highslide.min.js"></script>';
		Html::$_scripts[] = '<script type="text/javascript">hs.graphicsDir = "'.PATH_BASE.'system/modules/highslide_js/assets/graphics/";</script>';
    }
}