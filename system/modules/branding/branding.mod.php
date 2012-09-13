<?php
class Branding extends Module {
    public static function __registar_callback(){
		CMS::callstack_add('insert', DEFAULT_CALLBACK_PARSE+1);
    }
    
    
    public static function insert(){
		Html::set('{branding_logo}', '<img src="/branding/'.BRANDING_FOLDER.'/images/logo.png" alt="logo" />');
        Html::set('{branding}', BRANDING_FOLDER);
	}
	
}
