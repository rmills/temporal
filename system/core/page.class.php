<?php
/**
 * @author Ryan Mills <ryan@ryanmills.net> (Primary)
 * 
 * This is a baseclass that all pages should extend
 */
class Page{
    /**
     * Load block for page
     * @param string $filename
     * @return mixed html or false if not found 
     */
    protected static function block($filename){
		$trace = debug_backtrace();
		$file = PATH_PAGE_ROOT.strtolower($trace[1]['class']).'/blocks/'.$filename;
		if(is_file($file)){
			return file_get_contents($file);
		}else{
            $file = PATH_PAGE_ROOT_ADDON.strtolower($trace[1]['class']).'/blocks/'.$filename;
            if(is_file($file)){
                return file_get_contents($file);
            }else{
                CMS::log('Page', 'Missing block: '.$file, 2);
                return false;
            }
		}
    }
    
    
    /**
     * Registar a callback with the core
     */
    public static function __registar_callback(){}
    
    
    /**
     * Registar a callback with the core
     */
    public static function active(){}
}