<?php

class UserAnswer 
{
	const PROPERTY_ID = 'id';
	const PROPERTY_USER_QUESTION_ID = 'user_question_id';
	const PROPERTY_ANSWER_ID = 'answer_id';
	const PROPERTY_EXTRA = 'extra';
	const PROPERTY_SCORE = 'score';
	
	const TABLE_NAME = 'user_answer';
	
	private $default_properties;
	
	function UserAnswer($id = null, $default_properties = array())
	{
		$this->set_id($id);
		$this->default_properties = $default_properties;
	}
	
	function get_default_property_names()
	{
		return array(
		self :: PROPERTY_ID,
		self :: PROPERTY_USER_QUESTION_ID,
		self :: PROPERTY_ANSWER_ID,
		self :: PROPERTY_EXTRA,
		self :: PROPERTY_SCORE
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
	
	function get_user_question_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_QUESTION_ID);
	}
	
	function get_answer_id()
	{
		return $this->get_default_property(self :: PROPERTY_ANSWER_ID);
	}
	
	function get_extra()
	{
		return $this->get_default_property(self :: PROPERTY_EXTRA);
	}
	
	function get_score()
	{
		return $this->get_default_property(self :: PROPERTY_SCORE);
	}
	
	function set_user_question_id($value)
	{
		$this->set_default_property(self :: PROPERTY_USER_QUESTION_ID, $value);
	}
	
	function set_answer_id($value)
	{
		$this->set_default_property(self :: PROPERTY_ANSWER_ID, $value);
	}
	
	function set_extra($value)
	{
		$this->set_default_property(self :: PROPERTY_EXTRA, $value);
	}
	
	function set_score($value)
	{
		$this->set_default_property(self :: PROPERTY_SCORE, $value);
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
	
	static function get_table_name()
	{
		return self :: TABLE_NAME;
	}
}
?>