<?php
/**
 * @package application.lib.weblcms.course
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../weblcmsdatamanager.class.php';

/**
 *	This class represents a course user relation in the weblcms.
 *
 *	course user relations have a number of default properties:
 *	- course code: the code of the course;
 *	- user_id: the user's id;
 *	- status: the subscription status (teacher or student);
 *	- role: the user's role;
 *	- group_id: the group id;
 *  - tutor_id: the id of the tutor;
 *	- sort: the sort order;
 *	- category: the category in which the user has placed the course;
 *
 * To access the values of the properties, this class and its subclasses
 * should provide accessor methods. The names of the properties should be
 * defined as class constants, for standardization purposes. It is recommended
 * that the names of these constants start with the string "PROPERTY_".
 *
 */

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

	/**
	 * Creates a new course user relation object.
	 * @param int $id The numeric ID of the course user relation object. May be omitted
	 *                if creating a new object.
	 * @param array $defaultProperties The default properties of the course user relation
	 *                object. Associative array.
	 */
    function CourseUserRelation($course = null, $user = null, $defaultProperties = array ())
    {
    	$this->course = $course;
    	$this->user = $user;
		$this->defaultProperties = $defaultProperties;
    }

    /**
	 * Gets a default property of this course user relation object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	/**
	 * Gets the default properties of this course user relation object.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	/**
	 * Sets a default property of this course user relation object by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	/**
	 * Get the default properties of all course user relations.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_COURSE, self :: PROPERTY_USER, self :: PROPERTY_STATUS, self :: PROPERTY_ROLE, self :: PROPERTY_GROUP, self :: PROPERTY_TUTOR, self :: PROPERTY_SORT, self :: PROPERTY_CATEGORY	);
	}

	/**
	 * Returns the course of this course user relation object
	 * @return int
	 */
    function get_course()
    {
    	return $this->course;
    }

	/**
	 * Sets the course of this course user relation object
	 * @param int $course
	 */
    function set_course($course)
	{
		$this->course = $course;
	}

	/**
	 * Returns the user of this course user relation object
	 * @return int
	 */
    function get_user()
    {
    	return $this->user;
    }

	/**
	 * Sets the user of this course user relation object
	 * @param int $user
	 */
	function set_user($user)
	{
		$this->user = $user;
	}

	/**
	 * Gets the user
	 * @return User
	 * @todo The functions get_user and set_user should work with a User object
	 * and not with the user id's!
	 */
	function get_user_object()
	{
		$udm = UsersDatamanager::get_instance();
		return $udm->retrieve_user($this->user);
	}

	/**
	 * Returns the status of this course user relation object
	 * @return int
	 */
    function get_status()
    {
    	return $this->get_default_property(self :: PROPERTY_STATUS);
    }

	/**
	 * Sets the status of this course user relation object
	 * @param int $status
	 */
	function set_status($status)
	{
		$this->set_default_property(self :: PROPERTY_STATUS, $status);
	}

	/**
	 * Returns the group of this course user relation object
	 * @return int
	 */
    function get_group()
    {
    	return $this->get_default_property(self :: PROPERTY_GROUP);
    }

	/**
	 * Sets the group of this course user relation object
	 * @param int $group
	 */
	function set_group($group)
	{
		$this->set_default_property(self :: PROPERTY_GROUP, $group);
	}

	/**
	 * Returns the role of this course user relation object
	 * @return int
	 */
    function get_role()
    {
    	return $this->get_default_property(self :: PROPERTY_ROLE);
    }

	/**
	 * Sets the role of this course user relation object
	 * @param int $role
	 */
	function set_role($role)
	{
		$this->set_default_property(self :: PROPERTY_ROLE, $role);
	}

	/**
	 * Returns the tutor of this course user relation object
	 * @return int
	 */
    function get_tutor()
    {
    	return $this->get_default_property(self :: PROPERTY_TUTOR);
    }

	/**
	 * Sets the tutor of this course user relation object
	 * @param int $tutor
	 */
	function set_tutor($tutor)
	{
		$this->set_default_property(self :: PROPERTY_TUTOR, $tutor);
	}

	/**
	 * Returns the sort of this course user relation object
	 * @return int
	 */
    function get_sort()
    {
    	return $this->get_default_property(self :: PROPERTY_SORT);
    }

	/**
	 * Sets the sort of this course user relation object
	 * @param int $sort
	 */
	function set_sort($sort)
	{
		$this->set_default_property(self :: PROPERTY_SORT, $sort);
	}

	/**
	 * Returns the category of this course user relation object
	 * @return int
	 */
    function get_category()
    {
    	return $this->get_default_property(self :: PROPERTY_CATEGORY);
    }

	/**
	 * Sets the category of this course user relation object
	 * @param int $category
	 */
	function set_category($category)
	{
		$this->set_default_property(self :: PROPERTY_CATEGORY, $category);
	}

	/**
	 * Updates the course user relation object in persistent storage
	 * @return boolean
	 */
	function update()
	{
		$wdm = WeblcmsDataManager :: get_instance();
		$success = $wdm->update_course_user_relation($this);
		if (!$success)
		{
			return false;
		}

		return true;
	}

	/**
	 * Creates the course user relation object in persistent storage
	 * @return boolean
	 */
	function create()
	{
		$wdm = WeblcmsDataManager :: get_instance();
		$success = $wdm->create_course_user_relation($this);
		if (!$success)
		{
			return false;
		}

		return true;
	}

	/**
	 * Deletes the course user relation object from persistent storage
	 * @return boolean
	 */
	function delete()
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