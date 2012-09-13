<?php

class Nav extends Module{
    
    private static $stack = array();
	
    public static function __registar_callback(){
		CMS::callstack_add('parse_nav', 49);
    }
    
    /**
     * Nav Generator
     * @param string $group no spaces, lower case
     * @param string $group_name pretty name
     * @param string $name link in link
     * @param string $href href of link
     */
    public static function add($group, $group_name, $name, $href){
		self::$stack[$group]['name'] = $group_name;
		self::$stack[$group]['list'][] = array($href, $name);
    }
    
    
    /**
     * Builds the list fromt he stack
     * @return HTML formated nav list 
     */
    public static function html(){
		$html = array();
		$html[] = '<ul>';
		foreach(self::$stack as $group){
		   $html[] = '<li>'.$group['name'].'<ul>'; 
		   foreach($group['list'] as $href){
			   $html[] = '<li><a href="'.$href[0].'">'.$href[1].'</a></li>'; 
		   }
		   $html[] = '</ul></li>'; 
		}
		$html[] = '</ul>';
		return implode(PHP_EOL, $html);
    }
	
	public static function parse_nav(){
		Html::set('{nav}', self::html() );
	}
}