<?php

class UserAssessment
{
	const PROPERTY_ID = 'id';
	const PROPERTY_ASSESSMENT_ID = 'assessment_id';
	const PROPERTY_DATE_TIME_TAKEN = 'date_time_taken';
	const PROPERTY_USER_ID = 'user_id';
	
	const TABLE_NAME = 'user_assessment';
	
	//private $id;
	private $default_properties;
	
	function UserAssessment($id, $default_properties)
	{
		//$this->id = $id;
		$this->set_id($id);
		$this->default_properties = $default_properties;
	}
	
	function set_default_property($name, $value)
	{
		$this->default_properties[$name] = $value;
	}
	
	function get_default_property($name) 
	{
		return $this->default_properties[$name];
	}
	
	function get_default_properties()
	{
		return $this->default_properties;
	}
	
	function set_default_properties($default_properties)
	{
		$this->default_properties = $default_properties;
	}
	
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	
	function set_id($value)
	{
		$this->set_default_property(self :: PROPERTY_ID, $value);
	}
	
	function get_assessment_id()
	{
		return $this->get_default_property(self :: PROPERTY_ASSESSMENT_ID);
	}
	
	function get_date_time_taken()
	{
		return $this->get_default_property(self :: PROPERTY_DATE_TIME_TAKEN);
	}
	
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}
	
	function set_assessment_id($value)
	{
		$this->set_default_property(self :: PROPERTY_ASSESSMENT_ID, $value);
	}
	
	function set_date_time_taken($value)
	{
		$this->set_default_property(self :: PROPERTY_DATE_TIME_TAKEN, $value);
	}
	
	function set_user_id($value)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $value);
	}
	
	function create() 
	{
		$dm = WeblcmsDataManager :: get_instance();
		return $dm->create_user_assessment($this);
	}
	
	function delete()
	{
		$dm = WeblcmsDataManager :: get_instance();
		return $dm->delete_user_assessment($this);
	}
	
	function update()
	{
		$dm = WeblcmsDataManager :: get_instance();
		$success = $dm->update_user_assessment($this);
		if ($success)
		{
			return true;
		}
		return false;
	}
	
	static function get_table_name()
	{
		return self :: TABLE_NAME;
	}
}
?>