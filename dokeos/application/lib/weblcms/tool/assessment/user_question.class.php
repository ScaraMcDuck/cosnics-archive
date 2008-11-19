<?php

class UserQuestion 
{
	const PROPERTY_ID = 'id';
	const PROPERTY_USER_ASSESSMENT_ID = 'user_assessment_id';
	const PROPERTY_QUESTION_ID = 'question_id';
	const PROPERTY_WEIGHT = 'weight';
	const PROPERTY_FEEDBACK = 'feedback';
	
	const TABLE_NAME = 'user_question';
	
	private $default_properties;
	
	function UserQuestion($id = null, $default_properties = array())
	{
		$this->set_id($id);
		$this->default_properties = $default_properties;
	}
	
	function get_default_property_names()
	{
		return array(
		self :: PROPERTY_ID,
		self :: PROPERTY_USER_ASSESSMENT_ID,
		self :: PROPERTY_QUESTION_ID,
		self :: PROPERTY_WEIGHT,
		self :: PROPERTY_FEEDBACK
		);
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
	
	function set_default_properties($properties)
	{
		$this->default_properties = $properties;
	}
	
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	
	function set_id($value)
	{
		$this->set_default_property(self :: PROPERTY_ID, $value);
	}
	
	function get_user_test_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ASSESSMENT_ID);
	}
	
	function get_question_id()
	{
		return $this->get_default_property(self :: PROPERTY_QUESTION_ID);
	}
	
	function get_weight()
	{
		return $this->get_default_property(self :: PROPERTY_WEIGHT);
	}
	
	function get_feedback()
	{
		return $this->get_default_property(self :: PROPERTY_FEEDBACK);
	}
	
	function set_user_test_id($value)
	{
		$this->set_default_property(self :: PROPERTY_USER_ASSESSMENT_ID, $value);
	}
	
	function set_question_id($value)
	{
		$this->set_default_property(self :: PROPERTY_QUESTION_ID, $value);
	}
	
	function set_weight($value)
	{
		$this->set_default_property(self :: PROPERTY_WEIGHT, $value);
	}
	
	function set_feedback($value)
	{
		$this->set_default_property(self :: PROPERTY_FEEDBACK, $value);
	}
	
	function create() 
	{
		$dm = WeblcmsDataManager :: get_instance();
		return $dm->create_user_question($this);
	}
	
	function delete()
	{
		$dm = WeblcmsDataManager :: get_instance();
		return $dm->delete_user_question($this);
	}
	
	function update()
	{
		$dm = WeblcmsDataManager :: get_instance();
		$success = $dm->update_user_question($this);
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