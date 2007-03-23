<?php

class CourseCategory {

	const PROPERTY_ID = 'id';
	const PROPERTY_NAME = 'name';
	const PROPERTY_CODE = 'code';
	const PROPERTY_PARENT = 'parent_id';
	const PROPERTY_TREE_POS = 'tree_pos';
	const PROPERTY_CHILDREN_COUNT = 'children_count';
	const PROPERTY_AUTH_COURSE_CHILD = 'auth_course_child';
	const PROPERTY_AUTH_CAT_CHILD = 'auth_cat_child';
	
	
	private $id;
	private $defaultProperties;

    function CourseCategory($id = null, $defaultProperties = array ())
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
	 * Get the default properties of all course categories.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_CODE, self :: PROPERTY_PARENT, self :: PROPERTY_TREE_POS, self :: PROPERTY_CHILDREN_COUNT, self :: PROPERTY_AUTH_COURSE_CHILD, self :: PROPERTY_AUTH_CAT_CHILD);
	}
    
    function get_id()
    {
    	return $this->id;
    }
    
    function set_id($id)
	{
		$this->id = $id;
	}
	
    function get_name()
    {
    	return $this->get_default_property(self :: PROPERTY_NAME);
    }
	
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}
	
    function get_code()
    {
    	return $this->get_default_property(self :: PROPERTY_CODE);
    }
	
	function set_code($code)
	{
		$this->set_default_property(self :: PROPERTY_CODE, $code);
	}
	
    function get_parent()
    {
    	return $this->get_default_property(self :: PROPERTY_PARENT);
    }
	
	function set_parent($parent)
	{
		$this->set_default_property(self :: PROPERTY_PARENT, $parent);
	}
	
    function get_tree_pos()
    {
    	return $this->get_default_property(self :: PROPERTY_TREE_POS);
    }
	
	function set_tree_pos($tree_pos)
	{
		$this->set_default_property(self :: PROPERTY_TREE_POS, $tree_pos);
	}
	
    function get_children_count()
    {
    	return $this->get_default_property(self :: PROPERTY_CHILDREN_COUNT);
    }
	
	function set_children_count($children_count)
	{
		$this->set_default_property(self :: PROPERTY_CHILDREN_COUNT, $children_count);
	}
	
    function get_auth_course_child()
    {
    	return $this->get_default_property(self :: PROPERTY_AUTH_COURSE_CHILD);
    }
	
	function set_auth_course_child($auth_course_child)
	{
		$this->set_default_property(self :: PROPERTY_AUTH_COURSE_CHILD, $auth_course_child);
	}
	
    function get_auth_cat_child()
    {
    	return $this->get_default_property(self :: PROPERTY_AUTH_CAT_CHILD);
    }
	
	function set_auth_cat_child($auth_cat_child)
	{
		$this->set_default_property(self :: PROPERTY_AUTH_CAT_CHILD, $auth_cat_child);
	}
}
?>