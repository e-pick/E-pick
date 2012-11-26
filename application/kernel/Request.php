<?php
class Request { 

	protected $arrays;
	protected $controller = null;
	protected $action = null;
	
	function __construct($request){
		$this->arrays = $request;	
		if(!empty($this->arrays['controller']))
			$this->controller = $this->arrays['controller'];
		if(!empty($this->arrays['action']))
			$this->action = $this->arrays['action'];
	}
	
	
	function getArray(){
		return $this->arrays;
	}
	
	function getVar($key){
		return (isset($this->arrays[$key]) ) ? $this->arrays[$key] : null;
	}
	
	
	function addVar($key, $value){
		$this->arrays[$key] = $value;
	}
	
	function getController(){
		return $this->controller;
	}
	
	function getAction(){
		return $this->action;
	}


}

?>