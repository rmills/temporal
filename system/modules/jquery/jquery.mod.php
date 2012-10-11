<?php

class Jquery extends Module{
	
	public static function __registar_callback(){
		CMS::callstack_add('add_links', DEFAULT_CALLBACK_CREATE);
    }
	
	public static function add_links(){
		Html::set('{scripts}', '<script type="text/javascript" src="{root_doc}system/inc/js/jquery.js"></script>');
                Html::set('{scripts}', '<script src="http://code.jquery.com/ui/1.9.0/jquery-ui.js"></script>');
		Html::set('{css}', '<link rel="stylesheet" href="{root_doc}system/inc/css/js-theme/jquery-ui-1.8.17.custom.css" type="text/css" media="screen, projection">');
	}
}
