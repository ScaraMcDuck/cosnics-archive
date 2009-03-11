<?php
/**
 * Class representing a reporting template
 * 
 * @author: Michael Kyndt
 */
 
class ReportingTemplateRegistration{
    const CLASS_NAME = __CLASS__;
    
 	const PROPERTY_ID = 'id';
 	const PROPERTY_NAME = 'name';
 	const PROPERTY_APPLICATION = 'application';
 	const PROPERTY_CLASSNAME = 'class';
 	const PROPERTY_PLATFORM = 'platform';
    const PROPERTY_DESCRIPTION = 'description';
 	private $properties;

    /**
     *
     * @param array $properties
     */
 	public function ReportingTemplateRegistration($properties = array())
 	{
 		$this->properties = $properties;	
 	}

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (
			self :: PROPERTY_ID,
			self :: PROPERTY_NAME,
			self :: PROPERTY_APPLICATION,
			self :: PROPERTY_CLASSNAME,
			self :: PROPERTY_PLATFORM,
            self :: PROPERTY_DESCRIPTION
		);
	}

	/**
	 * Sets a default property by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->properties[$name] = $value;
	}

	/**
	 * Sets the default properties of this class
	 */
	function set_default_properties($properties)
	{
		$this->properties = $properties;
	}
	
	 /**
	 * Gets a default property by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->properties[$name];
	}
	
	/**
	 * Gets the default properties
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->properties;
	}

    /**
     * creates a reporting template registration in the database
     * @return ReportingTemplateRegistration
     */
	function create()
	{
		$repdmg = ReportingDataManager :: get_instance();
		$this->set_id($repdmg->get_next_reporting_template_registration_id());
		return $repdmg->create_reporting_template_registration($this);
	}

    /**
     * Checks if the reporting template registration is aplatform template.
     * @return int
     */
    function isPlatformTemplate()
    {
        return $this->get_default_property(self :: PROPERTY_PLATFORM) == '1';
    }
 	
 	/*
 	 * Getters and setters
 	 */
 	
 	public function get_id()
 	{
 		return $this->get_default_property(self :: PROPERTY_ID);
 	}
 	
	public function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
 	
 	public function get_name(){
 		return $this->get_default_property(self :: PROPERTY_NAME);
 	}
 	public function set_name($value){
 		$this->set_default_property(self :: PROPERTY_NAME,$value);
 	}
 	
 	public function get_application(){
 		return $this->get_default_property(self :: PROPERTY_APPLICATION);
 	}
 	
 	public function set_application($value){
 		$this->set_default_property(self :: PROPERTY_APPLICATION,$value);
 	}
 	
 	public function get_classname()
 	{
 		return $this->get_default_property(self :: PROPERTY_CLASSNAME);
 	}
 	
 	public function set_classname($value)
 	{
 		$this->set_default_property(self :: PROPERTY_CLASSNAME,$value);
 	}
 	
 	public function get_platform()
 	{
 		return $this->get_default_property(self :: PROPERTY_PLATFORM);
 	}
 	
 	public function set_platform($value)
 	{
 		$this->set_default_property(self :: PROPERTY_PLATFORM,$value);
 	}

    public function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    public function set_description($value)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $value);
    }

    static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
 }//class ReportingTemplateRegistration
?>
