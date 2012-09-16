<?php
/**
 * @author Ryan Mills <ryan@ryanmills.net> (Primary)
 * 
 * Core CMS class
 */
class CMS {
    
    static public $_config;
    static public $_vars;
    static public $_page_type = false;
    static public $_content_type = false;
    
    static private $__log = array();
    static private $__time;
    static private $__callstack = array();
	
	static private $__modules = array();
	static private $__pages = array();
    
    static public $_user = false;
    
    
//////////////////////////////////
//
//     Init Methods
//
//////////////////////////////////    
    
    public static function init( $config = array() ){
		self::$__time  = microtime();
		self::$_config = $config;

		
		
		self::init_vars();
		self::init_content_types();
		self::init_pages();
		self::init_modules();
		
		DB::init();
		self::$_user = new User();
        self::$_user->init();
		
		self::init_modules_callbacks();
		self::init_page_callbacks();
		self::init_active_page();
    }
    
    
    private static function init_vars(){
		foreach( self::$_config['vars'] as $v ){
			self::$_vars[] = false;
		}
		foreach( self::$_config['vars'] as $key=>$v ){
			if( isset($_GET[ $v ] ) ){
				self::$_vars[$key] = strtolower( $_GET[ $v ] );
			}elseif( isset($_POST[ $v ] ) ){
				self::$_vars[$key] = strtolower( $_POST[ $v ] );
			}
		}
    }


    
    /**
     * Used to load modules.
     * 
     * @param bool $autoload
     * @param array $modulestack 
     */
    public static function init_modules(){
		self::log('CMS', 'CMS::init_modules() searching: '.PATH_MODULE_ROOT);
		self::$__modules = array();

		$folders = array();
		$modules = scandir( PATH_MODULE_ROOT );
		foreach( $modules as $v ){
			if( $v != '.' && $v != '..' ){
				$folders[] = $v;
			}
		}
        if(is_dir(PATH_MODULE_ROOT_ADDON)){
            self::log('CMS', 'CMS::init_modules addons() searching: '.PATH_MODULE_ROOT_ADDON);
            $folders_addon = array();
            $modules = scandir( PATH_MODULE_ROOT_ADDON );
            foreach( $modules as $v ){
                if( $v != '.' && $v != '..' ){
                    $folders_addon[] = $v;
                }
            }
        }
        
        
        
		foreach( $folders as $v ){
			$folder = dir(PATH_MODULE_ROOT.$v);
			while (false !== ($entry = $folder->read())) {
				if( strpos($entry, '.mod.') !== false ){
					$path = PATH_MODULE_ROOT.$v.'/'.$entry;
                    if(!is_file(PATH_MODULE_ROOT.$v.'/ignore.txt')){
                        self::log('CMS', 'CMS::init_modules() found: '.$path);
                        self::$__modules[] = array($path, $v);
                    }else{
                        self::log('CMS', 'CMS::init_modules() ignoring: '.$path);
                    }
				}
			}
			$folder->close();
		}
        
        if(is_dir(PATH_MODULE_ROOT_ADDON)){
            foreach( $folders_addon as $v ){
                $folder = dir(PATH_MODULE_ROOT_ADDON.$v);
                while (false !== ($entry = $folder->read())) {
                    if( strpos($entry, '.mod.') !== false ){
                        $path = PATH_MODULE_ROOT_ADDON.$v.'/'.$entry;
                        if(!is_file(PATH_MODULE_ROOT_ADDON.$v.'/ignore.txt')){
                            self::log('CMS', 'CMS::init_modules addon() found: '.$path);
                            self::$__modules[] = array($path, $v);
                        }else{
                            self::log('CMS', 'CMS::init_modules addon() ignoring: '.$path);
                        }
                    }
                }
                $folder->close();
            }
		}

	}
	
	public static function init_modules_callbacks(){
		foreach (self::$__modules as $v){
			include($v[0]);
			$module = ucwords($v[1]);
			$try = method_exists( $module, '__registar_callback' );
			if( $try ){
				/** NOTE: format changes in PHP 5.2.3 **/
				call_user_func( array( $module, '__registar_callback'  ) );
			}
		}
	}
	
	/**
     * Used to load modules.
     * 
     * @param bool $autoload
     * @param array $modulestack 
     */
    public static function init_content_types(){
		self::log('CMS', 'CMS::init_content_types() searching: '.PATH_CONTENT_TYPES_ROOT);
		
        $folders = array();
		$files = scandir( PATH_CONTENT_TYPES_ROOT );
		foreach( $files as $entry ){
			if( strpos($entry, '.type.') !== false ){
				$path = PATH_CONTENT_TYPES_ROOT.$entry;
				self::log('CMS', 'CMS::init_content_types() found: '.$path);
				$folders[] = array($path, $entry);
			}
		}
        
        $folders_addon = array();
        if(is_dir(PATH_CONTENT_TYPES_ROOT_ADDON)){
            $files = scandir( PATH_CONTENT_TYPES_ROOT_ADDON );
            foreach( $files as $entry ){
                if( strpos($entry, '.type.') !== false ){
                    $path = PATH_CONTENT_TYPES_ROOT_ADDON.$entry;
                    self::log('CMS', 'CMS::init_content_types() found: '.$path);
                    $folders_addon[] = array($path, $entry);
                }
            }
        }
		
		foreach ($folders as $v){
			include($v[0]);
            $module = explode ('.', $v[1]);
            $module_name = ucwords($module[0]);
			$try = method_exists( $module_name, '__registar_callback' );
			if( $try ){
				/** NOTE: format changes in PHP 5.2.3 **/
				call_user_func( array( $module_name, '__registar_callback'  ) );
			}
		}
        
        if(is_dir(PATH_CONTENT_TYPES_ROOT_ADDON)){
            foreach ($folders_addon as $v){
                include($v[0]);
                $module = explode ('.', $v[1]);
                $module_name = ucwords($module[0]);
                $try = method_exists( $module_name, '__registar_callback' );
                if( $try ){
                    /** NOTE: format changes in PHP 5.2.3 **/
                    call_user_func( array( $module_name, '__registar_callback'  ) );
                }
            }
        }
	}	
		
    public static function init_active_page(){
		if(self::$_vars[0]){
		    if( class_exists(ucfirst(self::$_vars[0]) ) ){
				call_user_func(array(ucfirst(self::$_vars[0]), 'active'));
		    }else{
				call_user_func(array(ucfirst(DEFAULT_PAGE_GUEST), 'active'));
		    }
		}else{
			call_user_func(array(ucfirst(DEFAULT_PAGE_GUEST), 'active'));
		}
    }
    
    
    
    /**
     * Used to load pages
     * 
     * @param bool $autoload
     * @param array $modulestack 
     */
    public static function init_pages(){
		self::log('CMS', 'CMS::init_pages() searching: '.PATH_PAGE_ROOT);
		self::$__pages = array();

		$folders = array();
		$modules = scandir( PATH_PAGE_ROOT );
		foreach( $modules as $v ){
			if( $v != '.' && $v != '..' ){
				$folders[] = $v;
			}
		}
        
        if(is_dir(PATH_PAGE_ROOT_ADDON)){
            $folders_addon = array();
            $modules = scandir( PATH_PAGE_ROOT_ADDON );
            foreach( $modules as $v ){
                if( $v != '.' && $v != '..' ){
                    $folders_addon[] = $v;
                }
            }
        }

		foreach( $folders as $v ){
			$folder = dir(PATH_PAGE_ROOT.$v);
			while (false !== ($entry = $folder->read())) {
				if( strpos($entry, '.page.') !== false ){
					$path = PATH_PAGE_ROOT.$v.'/'.$entry;
					$__pages[] = strtolower($v);
					self::log('CMS', 'CMS::init_pages() found: '.$path);
					self::$__pages[] = array($path, $v);
				}
			}
			$folder->close();
		}
        if(is_dir(PATH_PAGE_ROOT_ADDON)){
            foreach( $folders_addon as $v ){
                $folder = dir(PATH_PAGE_ROOT_ADDON.$v);
                while (false !== ($entry = $folder->read())) {
                    if( strpos($entry, '.page.') !== false ){
                        $path = PATH_PAGE_ROOT_ADDON.$v.'/'.$entry;
                        $__pages[] = strtolower($v);
                        self::log('CMS', 'CMS::init_pages() found: '.$path);
                        self::$__pages[] = array($path, $v);
                    }
                }
                $folder->close();
            }
        }

    }
	
	public static function init_page_callbacks(){
		foreach (self::$__pages as $v){
			include($v[0]);
			$page = ucwords($v[1]);
			$try = method_exists( $page, '__registar_callback' );
			if( $try ){
				/** NOTE: format changes in PHP 5.2.3 **/
				call_user_func( array( $page, '__registar_callback'  ) );
			}
		}
	}
    
    
   
//////////////////////////////////
//
//     Callback Methods
//
//////////////////////////////////
    
    
    /**
     * Sets a callback
     * 
     * @param class $class
     * @param string $method name of method
     * @param int $loop index of the callback
     */    
    public static function callstack_add($method, $loop = 0){
		$trace = debug_backtrace();
		self::$__callstack[$loop][] = array($trace[1]['class'], $method);
    }
    
    
    /**
     * runs the callback static
     * 
     * @param class $class
     * @param string $method name of method
     * @param int $loop index of the callback
     */    
    public static function callstack_run($loop){
		ksort(self::$__callstack);
		if(isset(self::$__callstack[$loop])){
			foreach(self::$__callstack[$loop] as $call){
				$try = method_exists( $call[0], $call[1] );
				if( $try ){
					/** NOTE: format changes in PHP 5.2.3 **/
					$try = call_user_func( array( $call[0], $call[1] ) );
				}else{
					self::log('CMS', 'callback missing: '.$call[0].'::'.$call[1]);
				}
			}
		}
		
		if($loop != 90){
			self::callstack_run($loop+1);
		}
    }
    
    
    
    /**
     * Returns the callstack in a html friendly format
     * 
     * @return html 
     */
    public static function callstack_display_html(){
		$html = array();
		$html[] = '
			<div style="float: left; margin: 10px; width: 400px">
			<table style="width: 400px" id="system-callstack">
				<thead>
					<tr>
						<th>loop</th>
						<th>class</th>
						<th>method</th>
					</tr>
				</thead>
				<tbody>';
		foreach(self::$__callstack as $key=>$v){
			foreach($v as $v2){
			$html[] = '
				<tr>
					<td>'.$key.'</td>
					<td>'.$v2[0].'</td>
					<td>'.$v2[1].'</td>
				</tr>';
			}
		}
		$html[] = '</tbody></table></div>';
		$html[] = '
			<script type="text/javascript">
			  oTable = $("#system-callstack").dataTable({
					"bJQueryUI": true
				});
			</script>
		';
		return implode(PHP_EOL, $html);
    }
    
    
    
    
    

//////////////////////////////////
//
//     Log Methods
//
//////////////////////////////////
    
    
    
    /**
     * Log event
     * 
     * Level 
     *  0 = notice
     *  1 = warn
     *  2 = error
     *  3 = fatal
     * 
     * @param string $module name of the modules
     * @param string $details details of event
     * @param int $level level of event 0 to 3
     */
    public static function log($module, $details, $level = 0){
		self::$__log[] = array(
			( microtime() - self::$__time ), 
			$module, 
			$details, 
			$level
		);
    }
    
    
    
    /**
     * Returns the log in a html friendly format
     * 
     * @return html 
     */
    public static function log_display_html(){
		$html = array();
		$html[] = '
			<div style="float: left; margin: 10px; width: 900px">
			<table style="width: 900px" id="system-log">
				<thead>
					<tr>
						<th>time</th>
						<th>module</th>
						<th>details</th>
						<th>level</th>
					</tr>
				</thead>
				<tbody>';
		foreach(self::$__log as $v){
			$html[] = '
				<tr>
					<td>'.$v[0].'</td>
					<td>'.$v[1].'</td>
					<td>'.$v[2].'</td>
					<td>'.$v[3].'</td>
				</tr>';
		}
		$html[] = '</tbody></table></div>';
		$html[] = '
			<script type="text/javascript">
			  oTable = $("#system-log").dataTable({
					"bJQueryUI": true
				});
			</script>
		';
		return implode(PHP_EOL, $html);
    }
    
    
    
//////////////////////////////////
//
//     Misc Methods
//
//////////////////////////////////   
    
    
    public static function redirect($target){
        switch($target){
            case 'home':
                header("Location: ".DEFAULT_PROTOCOL.DOMAIN.PATH_BASE);
                die();
                break;
            
            case 'login':
                header("Location: ".DEFAULT_PROTOCOL.DOMAIN.PATH_BASE.'login');
                die();
                break;
            
            default;
                header("Location: ".$target);
                die();
        }
    }
    
    public static function allowed(){
        return self::$_user->allowed();
    }
}
