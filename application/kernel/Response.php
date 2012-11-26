<?php
class Response { 

	protected $array;
	protected $vars;
	protected $type;
	protected $notice;
	
	function __construct(){	
		$this->vars = array();
		$this->type = 'html';
	}
	
	function addVar($key, $value){
		$this->vars[$key] = $value;
	}
	
	function getVar($key){
		return $this->vars[$key];
	}
	
	function getVars(){
		return $this->vars;
	}
	
	function getArray(){
		return $this->array;
	}
	
	function getType(){
		return $this->type;	
	}
	
	function setType($type){
		$this->type = $type;
	}
	
	function setNotice($notice){
		$this->notice = $notice;
	}
	
	function getNotice(){
		return $this->notice;
	}
}

?>