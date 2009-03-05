<?php
/**
 * Class representing a reporting template
 * 
 * @author: Michael Kyndt
 */
 
class ReportingTemplate{
 	const PROPERTY_ID = 'id';
 	const PROPERTY_NAME = 'name';
 	const PROPERTY_APPLICATION = 'application';
 	const PROPERTY_CLASSNAME = 'class';
 	const PROPERTY_PLATFORM = 'platform';
 	private $properties, $reporting_blocks;
 	
 	public function ReportingTemplate($properties = array())
 	{
 		$this->properties = $properties;	
 		$reporting_block = array();
 	}

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_property_names()
	{
		return array (
			self :: PROPERTY_ID,
			self :: PROPERTY_NAME,
			self :: PROPERTY_APPLICATION,
			self :: PROPERTY_CLASSNAME,
			self :: PROPERTY_PLATFORM
		);
	}

	/**
	 * Sets a default property by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_property($name, $value)
	{
		$this->properties[$name] = $value;
	}

	/**
	 * Sets the default properties of this class
	 */
	function set_properties($properties)
	{
		$this->properties = $properties;
	}
	
	 /**
	 * Gets a default property by name.
	 * @param string $name The name of the property.
	 */
	function get_property($name)
	{
		return $this->properties[$name];
	}
	
	/**
	 * Gets the default properties
	 * @return array An associative array containing the properties.
	 */
	function get_properties()
	{
		return $this->properties;
	}
	
	function retrieve_reporting_blocks()
	{
		//
	}
	
	function isPlatformTemplate()
	{
		return $this->get_platform == '1';
	}
	
	function create()
	{
		$repdmg = ReportingDataManager :: get_instance();
		$this->set_id($repdmg->get_next_id('reporting_template'));
		return $repdmg->create_reporting_template($this);
	}
	
	function to_html()
	{
		
	}
 	
 	/**
 	 * Getters and setters
 	 */
 	public function get_reporting_blocks()
 	{
 		return $this->reporting_blocks;
 	}
 	
 	public function add_reporting_block(&$reporting_block)
 	{
 		array_push($this->reporting_blocks,$reporting_block);
 	}
 	
 	public function get_id()
 	{
 		return $this->get_property(self :: PROPERTY_ID);
 	}
 	
	public function set_id($id)
	{
		$this->set_property(self :: PROPERTY_ID, $id);
	}
 	
 	public function get_name(){
 		return $this->get_property(self :: PROPERTY_NAME);
 	}
 	public function set_name($value){
 		$this->set_property(self :: PROPERTY_NAME,$value);
 	}
 	
 	public function get_application(){
 		return $this->get_property(self :: PROPERTY_APPLICATION);
 	}
 	
 	public function set_application($value){
 		$this->set_property(self :: PROPERTY_APPLICATION,$value);
 	}
 	
 	public function get_classname()
 	{
 		return $this->get_property(self :: PROPERTY_CLASSNAME);
 	}
 	
 	public function set_classname($value)
 	{
 		$this->set_property(self :: PROPERTY_CLASSNAME,$value);
 	}
 	
 	public function get_platform()
 	{
 		return $this->get_property(self :: PROPERTY_PLATFORM);
 	}
 	
 	public function set_platform($value)
 	{
 		$this->set_property(self :: PROPERTY_PLATFORM,$value);
 	}
 }//class ReportingTemplate
?>
