<?php

class CourseUserRelation {

	const PROPERTY_COURSE = 'course_code';
	const PROPERTY_USER = 'user_id';
	const PROPERTY_STATUS = 'status';
	const PROPERTY_ROLE = 'role';
	const PROPERTY_GROUP = 'group_id';
	const PROPERTY_TUTOR = 'tutor_id';
	const PROPERTY_SORT = 'sort';
	const PROPERTY_CATEGORY = 'user_course_cat';
	
	private $course;
	private $user;
	private $defaultProperties;

    function CourseUserRelation($course = null, $user = null, $defaultProperties = array ())
    {
    	$this->course = $course;
    	$this->user = $user;
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
		return array (self :: PROPERTY_COURSE, self :: PROPERTY_USER, self :: PROPERTY_STATUS, self :: PROPERTY_ROLE, self :: PROPERTY_GROUP, self :: PROPERTY_TUTOR, self :: PROPERTY_SORT, self :: PROPERTY_CATEGORY	);
	}
    
    function get_course()
    {
    	return $this->course;
    }
    
    function set_course($course)
	{
		$this->course = $course;
	}
	
    function get_user()
    {
    	return $this->user;
    }
	
	function set_user($user)
	{
		$this->user = $user;
	}
	
    function get_status()
    {
    	return $this->get_default_property(self :: PROPERTY_STATUS);
    }
	
	function set_status($status)
	{
		$this->set_default_property(self :: PROPERTY_STATUS, $status);
	}
	
    function get_group()
    {
    	return $this->get_default_property(self :: PROPERTY_GROUP);
    }
	
	function set_group($group)
	{
		$this->set_default_property(self :: PROPERTY_GROUP, $group);
	}
	
    function get_role()
    {
    	return $this->get_default_property(self :: PROPERTY_ROLE);
    }
	
	function set_role($role)
	{
		$this->set_default_property(self :: PROPERTY_ROLE, $role);
	}
	
    function get_tutor()
    {
    	return $this->get_default_property(self :: PROPERTY_TUTOR);
    }
	
	function set_tutor($tutor)
	{
		$this->set_default_property(self :: PROPERTY_TUTOR, $tutor);
	}
	
    function get_sort()
    {
    	return $this->get_default_property(self :: PROPERTY_SORT);
    }
	
	function set_sort($sort)
	{
		$this->set_default_property(self :: PROPERTY_SORT, $sort);
	}
	
    function get_category()
    {
    	return $this->get_default_property(self :: PROPERTY_CATEGORY);
    }
	
	function set_category($category)
	{
		$this->set_default_property(self :: PROPERTY_CATEGORY, $category);
	}
	
	function update($this)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		$success = $wdm->update_course_user_relation($this);
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