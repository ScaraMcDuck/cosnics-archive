<?php
/*
 * Class representing a reporting block
 */
 
 class ReportingBlock{
 	protected $name='Default block', $application,$application_url, $function, $displaymode,$data;
 	
 	public function ReportingBlock($name,$application,$application_url,$function,$displaymode){
 		$this->name = $name;
 		$this->application = $application;
 		$this->application_url = $application_url;
 		$this->function = $function;
 		$this->displaymode = $displaymode;
 	}//Reporting_Block
 	
 	public function retrieve_data()
 	{
 		require_once($this->get_applicationUrl());
 		$this->data = call_user_func($this->get_application().'::'.$this->get_function());
 	}
 	
 	public function get_data()
 	{
 		return $this->data;
 	}
 	
 	public function get_name(){
 		return $this->name;
 	}
 	public function set_name($var){
 		$this->name = $var;
 	}
 	
 	public function get_application(){
 		return $this->application;
 	}
 	
 	public function set_application($var){
 		$this->application = $var;
 	}
 	
 	public function get_function(){
 		return $this->function;
 	}
 	
 	public function set_function($var){
 		$this->function = $var;
 	}
 	
 	public function get_displaymode(){
 		return $this->displaymode;
 	}
 	
 	public function set_displaymode($var){
 		$this->displaymode = $var;
 	}
 	
 	public function get_applicationUrl(){
 		return $this->application_url;
 	}
 	
 	public function set_applicationUrl($var){
 		$this->application_url = $var;
 	}
 }//class Reporting_Block
?>
