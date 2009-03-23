<?php
/**
 * @package application.lib.weblcms.course
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../weblcms_data_manager.class.php';

/**
 *	This class represents a course category in the weblcms.
 *
 *	course categories have a number of default properties:
 *	- id: the numeric course category ID;
 *	- name: the course category name;
 *	- code: the course category code;
 *	- parent: the course category parent;
 *  - tree_pos: the course category position;
 *	- children_count: the course category's number of children;
 *	- auth_course_child: the course category can contain courses ?;
 *	- auth_cat_child: the course category can contain other categories ?;
 *
 * To access the values of the properties, this class and its subclasses
 * should provide accessor methods. The names of the properties should be
 * defined as class constants, for standardization purposes. It is recommended
 * that the names of these constants start with the string "PROPERTY_".
 *
 */

class CourseCategory2 { //isn't needed anymore

	const PROPERTY_ID = 'id';
	const PROPERTY_NAME = 'name';
	const PROPERTY_CODE = 'code';
	const PROPERTY_PARENT = 'parent';
	const PROPERTY_TREE_POS = 'tree_pos';
	const PROPERTY_CHILDREN_COUNT = 'children_count';
	const PROPERTY_AUTH_COURSE_CHILD = 'auth_course_child';
	const PROPERTY_AUTH_CAT_CHILD = 'auth_cat_child';
	
	private $id;
	private $defaultProperties;

	/**
	 * Creates a new course category object.
	 * @param int $id The numeric ID of the course category object. May be omitted
	 *                if creating a new object.
	 * @param array $defaultProperties The default properties of the course category
	 *                object. Associative array.
	 */
    function CourseCategory($id = null, $defaultProperties = array ())
    {
    	$this->id = $id;
		$this->defaultProperties = $defaultProperties;
    }
    
    /**
	 * Gets a default property of this course category object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this course category object.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	/**
	 * Sets a default property of this course category object by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
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
    
	/**
	 * Returns the id of this course category object
	 * @return int
	 */ 
    function get_id()
    {
    	return $this->id;
    }
    
    /**
     * Sets the id of this course category object
     * @param int $id
     */
    function set_id($id)
	{
		$this->id = $id;
	}
	
	/**
	 * Returns the name of this course category object
	 * @return string
	 */ 
    function get_name()
    {
    	return $this->get_default_property(self :: PROPERTY_NAME);
    }
	
    /**
     * Sets the name of this course category object
     * @param string $name
     */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}
	
	/**
	 * Returns the code of this course category object
	 * @return string
	 */ 
    function get_code()
    {
    	return $this->get_default_property(self :: PROPERTY_CODE);
    }
	
    /**
     * Sets the code of this course category object
     * @param string $code
     */
	function set_code($code)
	{
		$this->set_default_property(self :: PROPERTY_CODE, $code);
	}
	
	/**
	 * Returns the parent of this course category object
	 * @return int
	 */ 
    function get_parent()
    {
    	return $this->get_default_property(self :: PROPERTY_PARENT);
    }
	
    /**
     * Sets the parent of this course category object
     * @param string $parent
     */
	function set_parent($parent)
	{
		$this->set_default_property(self :: PROPERTY_PARENT, $parent);
	}
	
	/**
	 * Returns the tree positions of this course category object
	 * @return int
	 */ 
    function get_tree_pos()
    {
    	return $this->get_default_property(self :: PROPERTY_TREE_POS);
    }
	
    /**
     * Sets the tree position of this course category object
     * @return int $tree_pos
     */
	function set_tree_pos($tree_pos)
	{
		$this->set_default_property(self :: PROPERTY_TREE_POS, $tree_pos);
	}
	
	/**
	 * Returns the amount of children of this course category object
	 * @return int
	 */ 
    function get_children_count()
    {
    	return $this->get_default_property(self :: PROPERTY_CHILDREN_COUNT);
    }
	
    /**
     * Returns the amount of children of this course category object
     * @param int $children_count
     */
	function set_children_count($children_count)
	{
		$this->set_default_property(self :: PROPERTY_CHILDREN_COUNT, $children_count);
	}
	
    /**
     * Returns whether the course category object can have courses as children
     * @return boolean
     */	
    function get_auth_course_child()
    {
    	return $this->get_default_property(self :: PROPERTY_AUTH_COURSE_CHILD);
    }
	
    /**
     * Sets whether the course category object can have courses as children
     * @param boolean $auth_course_child
     */
	function set_auth_course_child($auth_course_child)
	{
		$this->set_default_property(self :: PROPERTY_AUTH_COURSE_CHILD, $auth_course_child);
	}

    /**
     * Returns whether the course category object can have categories as children
     * @return boolean
     */	
    function get_auth_cat_child()
    {
    	return $this->get_default_property(self :: PROPERTY_AUTH_CAT_CHILD);
    }
	
    /**
     * Sets whether the course category object can have categories as children
     * @param boolean $auth_cat_child
     */
	function set_auth_cat_child($auth_cat_child)
	{
		$this->set_default_property(self :: PROPERTY_AUTH_CAT_CHILD, $auth_cat_child);
	}
	
	/**
	 * Creates the course category object in persistent storage
	 * @return boolean
	 */
	function create()
	{
		$wdm = WeblcmsDataManager :: get_instance();
		$this->set_id($wdm->get_next_course_category_id());
		return $wdm->create_course_category($this);
	}
	
	/**
	 * Updates the course category object in persistent storage
	 * @return boolean
	 */
	function update()
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->update_course_category($this);
	}
	
	/**
	 * Deletes the course category object from persistent storage
	 * @return boolean
	 */
	function delete()
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->delete_course_category($this);
	}
}
?>