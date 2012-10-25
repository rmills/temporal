<?php

/**
 * @author Ryan Mills <ryan@ryanmills.net> (Primary)
 * 
 * This is a baseclass that all pages should extend
 */

namespace Module;

class Module {

    /**
     * Registar a callback with the core
     */
    public static function __registar_callback() {
        
    }

    public static function active() {
        
    }

    /**
     * Load block for module
     * @param string $filename
     * @return mixed html or false if not found 
     */
    protected static function block($filename) {
        $trace = debug_backtrace();
        $file = PATH_MODULE_ROOT . strtolower($trace[1]['class']) . '/blocks/' . $filename;
        if (is_file($file)) {
            return file_get_contents($file);
        } else {
            $file = PATH_MODULE_ROOT_ADDON . strtolower($trace[1]['class']) . '/blocks/' . $filename;
            if (is_file($file)) {
                return file_get_contents($file);
            } else {
                \CMS::log('Page', 'Missing block: ' . $file, 2);
                return false;
            }
        }
    }

}