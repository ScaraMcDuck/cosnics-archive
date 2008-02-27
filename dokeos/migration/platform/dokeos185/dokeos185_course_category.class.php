<?php

/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/import_course.class.php';

/**
 * This class represents an old Dokeos 1.8.5 course_category
 *
 * @author David Van Wayenbergh
 */

class Dokeos185_course_category extends Import{
	
	/**
	 * course category properties
	 */
	const PROPERTY_CATEGORY_ID = 'id';
	const PROPERTY_CATEGORY_NAME = 'name';
	const PROPERTY_CATEGORY_CODE = 'code';
	const PROPERTY_CATEGORY_PARENT_ID = 'parent_id';
	const PROPERTY_CATEGORY_TREE_POS = 'tree_pos';
	const PROPERTY_CATEGORY_CHILDREN_COUNT = 'children_cont';
	const PROPERTY_CATEGORY_AUTH_COURSE_CHILD = 'auth_course_child';
	const PROPERTY_CATEGORY_AUTH_CAT_CHILD = 'auth_cat_child';

    /**
	 * Alfanumeric identifier of the course object.
	 */
	private $code;
	
	/**
	 * Default properties of the course_category object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new course object.
	 * @param array $defaultProperties The default properties of the user
	 *                                 object. Associative array.
	 */
	function Course_Category($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this course object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this course.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all courses.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_USER_ID, self :: PROPERTY_LASTNAME, self :: PROPERTY_FIRSTNAME, self :: PROPERTY_USERNAME, self :: PROPERTY_PASSWORD, self :: PROPERTY_AUTH_SOURCE, self :: PROPERTY_EMAIL, self :: PROPERTY_STATUS, self :: PROPERTY_PLATFORMADMIN, self :: PROPERTY_PHONE, self :: PROPERTY_OFFICIAL_CODE, self ::PROPERTY_PICTURE_URI, self :: PROPERTY_CREATOR_ID, self :: PROPERTY_LANGUAGE, self :: PROPERTY_DISK_QUOTA, self :: PROPERTY_DATABASE_QUOTA, self :: PROPERTY_VERSION_QUOTA);
	}
	
	/**
	 * Sets a default property of this course by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default course
	 * property.
	 * @param string $name The identifier.
	 * @return boolean True if the identifier is a property name, false
	 *                 otherwise.
	 */
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}
	
	/**
	 * CATEGORY GETTERS AND SETTERS
	 */
	 
	 /**
	 * Returns the ID of this category.
	 * @return int The ID.
	 */
	function get_id()
	{
		return $this->id;
	}
	
	/**
	 * Returns the name of this category.
	 * @return String The name.
	 */
	function get_name()
	{
		return $this->name;
	}
	
	/**
	 * Returns the code of this category.
	 * @return String The code.
	 */
	function get_code()
	{
		return $this->code;
	}
	
	/**
	 * Returns the parent_id of this category.
	 * @return String The parent_id.
	 */
	function get_parent_id()
	{
		return $this->parent_id;
	}
	
	/**
	 * Returns the tree_pos of this category.
	 * @return int The tree_pos.
	 */
	function tree_pos()
	{
		return $this->tree_pos;
	}
	
	/**
	 * Returns the children_count of this category.
	 * @return int The children_count.
	 */
	function get_children_count()
	{
		return $this->children_count;
	}
	
	/**
	 * Returns the auth_course_child of this category.
	 * @return Boolean The auth_course_child.
	 */
	function get_auth_course_child()
	{
		return $this->auth_course_child;
	}
	
	/**
	 * Returns the auth_cat_child of this category.
	 * @return Boolean The auth_cat_child.
	 */
	function get_auth_cat_child()
	{
		return $this->auth_cat_child;
	}
	
	/**
	 * Sets the id of this category.
	 * @param int $id The id.
	 */
	function set_id($id)
	{
		$this->id = $id;
	}
	
	/**
	 * Sets the name of this category.
	 * @param String $name The name.
	 */
	function set_name($name)
	{
		$this->name = $name;
	}
	
	/**
	 * Sets the code of this category.
	 * @param String $code The code.
	 */
	function set_code($code)
	{
		$this->code = $code;
	}
	
	/**
	 * Sets the parent_id of this category.
	 * @param String $parent_id The parent_id.
	 */
	function set_parent_id($parent_id)
	{
		$this->parent_id = $parent_id;
	}
	
	/**
	 * Sets the tree_pos of this category.
	 * @param int $tree_pos The tree_pos.
	 */
	function set_tree_pos($tree_pos)
	{
		$this->tree_pos = $tree_pos;
	}
	
	/**
	 * Sets the children_count of this category.
	 * @param int $children_count The children_count.
	 */
	function set_children_count($children_count)
	{
		$this->children_count = $children_count;
	}
	
	/**
	 * Sets the auth_course_child of this category.
	 * @param Boolean $auth_course_child The auth_course_child.
	 */
	function set_auth_course_child($auth_course_child)
	{
		$this->auth_course_child = $auth_course_child;
	}
	
	/**
	 * Sets the auth_cat_child of this category.
	 * @param Boolean $auth_cat_child The auth_cat_child.
	 */
	function set_auth_cat_child($auth_cat_child)
	{
		$this->auth_cat_child = $auth_cat_child;
	}
}
?>