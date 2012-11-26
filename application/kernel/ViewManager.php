<?php			
/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
***
 * VIEWMANAGER.PHP
 * 
 * La classe ViewManager
 */
 
class ViewManager {
	
	private static $vars;
	private static $header;
	private static $smarty;
	
	 
	public static function _init($smarty){
		self::$vars = array();
		self::$smarty = $smarty;
	}
	
	public static function setHeader($header){
		self::$header = $header;
	}
	 
	public static function load($view_path){ 
		foreach(self::$vars as $key => $value){
			self::$smarty->assign($key,$value); 
		}
		 
		self::$smarty->display($view_path);	 
	}
	
	
	
	public static function setVars($vars){
		self::$vars = $vars;
	}
}
 
?>