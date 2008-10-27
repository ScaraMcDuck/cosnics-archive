<?php

class UserAnswer 
{
	const PROPERTY_ID = 'id';
	const PROPERTY_USER_ASSESSMENT_ID = 'user_assessment_id';
	const PROPERTY_QUESTION_ID = 'question_id';
	const PROPERTY_ANSWER_ID = 'answer_id';
	const PROPERTY_EXTRA = 'extra';
	
	private $id;
	private $properties;
	
	function UserAnswer($id = null, $properties = array())
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
	
	function get_user_test_id()
	{
		return $this->get_property(self :: PROPERTY_USER_ASSESSMENT_ID);
	}
	
	function get_question_id()
	{
		return $this->get_property(self :: PROPERTY_QUESTION_ID);
	}
	
	function get_answer_id()
	{
		return $this->get_property(self :: PROPERTY_ANSWER_ID);
	}
	
	function get_extra()
	{
		return $this->get_property(self :: PROPERTY_EXTRA);
	}
	
	function set_user_test_id($value)
	{
		$this->set_property(self :: PROPERTY_USER_ASSESSMENT_ID, $value);
	}
	
	function set_question_id($value)
	{
		$this->set_property(self :: PROPERTY_QUESTION_ID, $value);
	}
	
	function set_answer_id($value)
	{
		$this->set_property(self :: PROPERTY_ANSWER_ID, $value);
	}
	
	function set_extra($value)
	{
		$this->set_property(self :: PROPERTY_EXTRA, $value);
	}
	
	function create() 
	{
		$dm = WeblcmsDataManager :: get_instance();
		return $dm->create_user_answer($this);
	}
	
	function delete()
	{
		$dm = WeblcmsDataManager :: get_instance();
		return $dm->delete_user_answer($this);
	}
	
	function update()
	{
		$dm = WeblcmsDataManager :: get_instance();
		$success = $dm->update_user_answer($this);
		if ($success)
		{
			return true;
		}
		return false;
	}
}
?>