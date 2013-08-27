<?php
class SiteDebug{
    public static function log($msg){
        $trace = debug_backtrace();
        
        $bug_details = array();
        $bug_details['stacktrace1'] = 'Line: '.\DB::clean($trace[0]['line']).' - '.\DB::clean($trace[0]['file']);
        $bug_details['stacktrace2'] = 'Line: '.\DB::clean($trace[1]['line']).' - '.\DB::clean($trace[1]['file']);
        $bug_details['stacktrace3'] = 'Line: '.\DB::clean($trace[2]['line']).' - '.\DB::clean($trace[2]['file']);
        $bug_details['stacktrace4'] = 'Line: '.\DB::clean($trace[3]['line']).' - '.\DB::clean($trace[3]['file']);
        $bug_details['msg'] = \DB::clean($msg);
        $bug_details['time'] = time();
        
        $bug = new AutoDB('site_debug');
        $bug->init($bug_details);
    }
}
