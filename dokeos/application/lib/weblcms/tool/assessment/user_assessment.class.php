<?php

class UserAssessment
{
	const PROPERTY_ID = 'id';
	const PROPERTY_ASSESSMENT_ID = 'assessment_id';
	const PROPERTY_DATE_TIME_TAKEN = 'date_time_taken';
	const PROPERTY_USER_ID = 'user_id';
	
	private $id;
	private $properties;
	
	function UserAssessment($id, $properties)
	{
		$this->id = $id;
		$this->properties = $properties;
	}
	
	function set_property($name, $value)
	{
		$this->properties[$name] = $value;
	}
	
	function get_property($name) 
	{
		return $this->properties[$name];
	}
	
	function get_properties()
	{
		return $this->properties;
	}
	
	function set_properties($properties)
	{
		$this->properties = $properties;
	}
	
	function get_id()
	{
		return $this->id;
	}
	
	function set_id($value)
	{
		$this->id = $id;
	}
	
	function get_assessment_id()
	{
		return $this->get_property(self :: PROPERTY_ASSESSMENT_ID);
	}
	
	function get_date_time_taken()
	{
		return $this->get_property(self :: PROPERTY_DATE_TIME_TAKEN);
	}
	
	function get_user_id()
	{
		return $this->get_property(self :: PROPERTY_USER_ID);
	}
	
	function set_assessment_id($value)
	{
		$this->set_property(self :: PROPERTY_ASSESSMENT_ID, $value);
	}
	
	function set_date_time_taken($value)
	{
		$this->set_property(self :: PROPERTY_DATE_TIME_TAKEN, $value);
	}
	
	function set_user_id($value)
	{
		$this->set_property(self :: PROPERTY_USER_ID, $value);
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
}
?>