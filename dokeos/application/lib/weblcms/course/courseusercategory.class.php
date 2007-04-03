<?php

class CourseUserCategory {

	const PROPERTY_ID = 'id';
	const PROPERTY_USER = 'user_id';
	const PROPERTY_SORT = 'sort';
	const PROPERTY_TITLE = 'title';
	
	private $id;
	private $defaultProperties;

    function CourseUserCategory($id = null, $defaultProperties = array ())
    {
    	$this->id = $id;
		$this->defaultProperties = $defaultProperties;
    }
    
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Get the default properties of all user course categories.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_USER, self :: PROPERTY_SORT, self :: PROPERTY_TITLE);
	}
    
    function get_id()
    {
    	return $this->id;
    }
    
    function set_id($id)
	{
		$this->id = $id;
	}
	
    function get_user()
    {
    	return $this->get_default_property(self :: PROPERTY_USER);
    }
	
	function set_user($user)
	{
		$this->set_default_property(self :: PROPERTY_USER, $user);
	}
	
    function get_sort()
    {
    	return $this->get_default_property(self :: PROPERTY_SORT);
    }
	
	function set_sort($sort)
	{
		$this->set_default_property(self :: PROPERTY_SORT, $sort);
	}
	
    function get_title()
    {
    	return $this->get_default_property(self :: PROPERTY_TITLE);
    }
	
	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
	}
	
	function update($this)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		$success = $wdm->update_course_user_category($this);
		if (!$success)
		{
			return false;
		}

		return true;
	}
	
	function create($this)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		$success = $wdm->create_course_user_category($this);
		if (!$success)
		{
			return false;
		}

		return true;
	}
	
	function delete($this)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		$success = $wdm->delete_course_user_category($this);
		if (!$success)
		{
			return false;
		}

		return true;
	}
}
?>