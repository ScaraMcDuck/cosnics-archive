<?php
/**
 * @package application.lib.weblcms.course
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../weblcms_data_manager.class.php';

/**
 *	This class represents a course in the weblcms.
 *
 *	courses have a number of default properties:
 *	- id: the numeric ID of the course object;
 *	- visual: the visual code of the course;
 *	- name: the name of the course object;
 *	- path: the course's path;
 *	- titular: the titular of this course object;
 *  - language: the language of the course object;
 *	- extlink url: the URL department;
 *	- extlink name: the name of the department;
 *	- category code: the category code of the object;
 *	- category name: the name of the category;
 *
 * To access the values of the properties, this class and its subclasses
 * should provide accessor methods. The names of the properties should be
 * defined as class constants, for standardization purposes. It is recommended
 * that the names of these constants start with the string "PROPERTY_".
 *
 */

class Course {

	const PROPERTY_ID = 'code';
	const PROPERTY_VISUAL = 'visual_code';
	const PROPERTY_DB = 'db_name';
	const PROPERTY_NAME = 'title';
	const PROPERTY_PATH = 'directory';
	const PROPERTY_TITULAR = 'titular';
	const PROPERTY_LANGUAGE = 'course_language';
	const PROPERTY_EXTLINK_URL = 'department_url';
	const PROPERTY_EXTLINK_NAME = 'department_name';
	const PROPERTY_CATEGORY = 'category';
	const PROPERTY_VISIBILITY = 'visibility';
	const PROPERTY_SUBSCRIBE_ALLOWED = 'subscribe';
	const PROPERTY_UNSUBSCRIBE_ALLOWED = 'unsubscribe';
	const PROPERTY_THEME = 'theme';
	const PROPERTY_LAYOUT = 'layout';

	// Remnants from the old Dokeos system
	const PROPERTY_LAST_VISIT = 'last_visit';
	const PROPERTY_LAST_EDIT = 'last_edit';
	const PROPERTY_CREATION_DATE = 'creation_date';
	const PROPERTY_EXPIRATION_DATE = 'expiration_date';


	const LAYOUT_2_COLUMNS = 1;
	const LAYOUT_3_COLUMNS = 2;
	const LAYOUT_2_COLUMNS_GROUP_INACTIVE = 3;
	const LAYOUT_3_COLUMNS_GROUP_INACTIVE = 4;

	private $id;
	private $defaultProperties;


	static function get_layouts()
	{
		return array(self :: LAYOUT_2_COLUMNS => translation :: get('TwoColumns'),
					 self :: LAYOUT_3_COLUMNS => translation :: get('ThreeColumns'),
					 self :: LAYOUT_2_COLUMNS_GROUP_INACTIVE => translation :: get('TwoColumnsGroupInactive'),
					 self :: LAYOUT_3_COLUMNS_GROUP_INACTIVE => translation :: get('ThreeColumnsGroupInactive'));
	}

	/**
	 * Creates a new course object.
	 * @param int $id The numeric ID of the course object. May be omitted
	 *                if creating a new object.
	 * @param array $defaultProperties The default properties of the course
	 *                object. Associative array.
	 */
    function Course($id = null, $defaultProperties = array ())
    {
    	$this->id = $id;
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
	 * Gets the default properties of this course object.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	/**
	 * Sets a default property of this course object by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	/**
	 * Get the default properties of all courses.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_LAYOUT, self :: PROPERTY_VISUAL, self :: PROPERTY_CATEGORY, self :: PROPERTY_DB, self :: PROPERTY_NAME, self :: PROPERTY_PATH, self :: PROPERTY_TITULAR, self :: PROPERTY_LANGUAGE, self :: PROPERTY_EXTLINK_URL, self :: PROPERTY_EXTLINK_NAME, self :: PROPERTY_VISIBILITY, self :: PROPERTY_SUBSCRIBE_ALLOWED, self :: PROPERTY_UNSUBSCRIBE_ALLOWED, self :: PROPERTY_THEME);
	}

	/**
	 * Returns the ID of this course object.
	 * @return int The ID.
	 */
	function get_id()
    {
    	return $this->id;
    }

	/**
	 * Returns the visual code of this course object
	 * @return string the visual code
	 */
    function get_visual()
    {
    	return $this->get_default_property(self :: PROPERTY_VISUAL);
    }

	/**
	 * Returns the category code of this course object
	 * @return string the category code
	 */
    function get_category()
    {
    	return $this->get_default_property(self :: PROPERTY_CATEGORY);
    }

	/**
	 * Returns the dbname of this course object
	 * Deprecated but still used by the course_groups manager
	 * @return string the visual code
	 */
    function get_db()
    {
    	return $this->get_default_property(self :: PROPERTY_DB);
    }

    /**
     * Returns the name (Title) of this course object
     * @return string The Name
     */
    function get_name()
    {
    	return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Returns the path (Directory) of this course object
     * @return string The Path
     */
    function get_path()
    {
    	return $this->get_default_property(self :: PROPERTY_PATH);
    }

    /**
     * Returns the titular of this course object
     * @return String The Titular
     */
    function get_titular()
    {
    	return $this->get_default_property(self :: PROPERTY_TITULAR);
    }
    
    /**
     * Returns the titular as a string
     */
    function get_titular_string()
    {
    	$titular_id = $this->get_titular();
    	
    	if (!is_null($titular_id))
    	{
			$udm = UserDataManager :: get_instance();
			$user = $udm->retrieve_user($titular_id);
			return $user->get_lastname() . ' ' . $user->get_firstname();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * Returns the language of this course object
     * @return String The Language
     */
    function get_language()
    {
    	return $this->get_default_property(self :: PROPERTY_LANGUAGE);
    }

    /**
     * Returns the ext url of this course object
     * @return String The URL
     */
    function get_extlink_url()
    {
    	return $this->get_default_property(self :: PROPERTY_EXTLINK_URL);
    }

    /**
     * Returns the ext link name of this course object
     * @return String The Name
     */
    function get_extlink_name()
    {
    	return $this->get_default_property(self :: PROPERTY_EXTLINK_NAME);
    }

    /**
     * Returns the visibility code of this course object
     * @return String The Visibility Code
     */
    function get_visibility()
    {
    	return $this->get_default_property(self :: PROPERTY_VISIBILITY);
    }

    /**
     * Returns if you can subscribe to this course object
     * @return Int
     */
    function get_subscribe_allowed()
    {
    	return $this->get_default_property(self :: PROPERTY_SUBSCRIBE_ALLOWED);
    }

    /**
     * Returns if you can unsubscribe to this course object
     * @return Int
     */
    function get_unsubscribe_allowed()
    {
    	return $this->get_default_property(self :: PROPERTY_UNSUBSCRIBE_ALLOWED);
    }
    
    /**
     * Returns the course theme
     * @return string The theme
     */
    function get_theme()
    {
    	return $this->get_default_property(self :: PROPERTY_THEME);
    }

    /**
     * Sets the ID of this course object
     * @param int $id The ID
     */
    function set_id($id)
	{
		$this->id = $id;
	}

	/**
	 * Sets the visual code of this course object
	 * @param String $visual The visual code
	 */
	function set_visual($visual)
	{
		$this->set_default_property(self :: PROPERTY_VISUAL, $visual);
	}

	/**
	 * Sets the category code of this course object
	 * @param String $visual The category code
	 */
	function set_category($category)
	{
		$this->set_default_property(self :: PROPERTY_CATEGORY, $category);
	}

	/**
	 * Sets the db name of this course object
	 * @param String $db The db name
	 */
	function set_db($db)
	{
		$this->set_default_property(self :: PROPERTY_DB, $db);
	}

	/**
	 * Sets the course name of this course object
	 * @param String $name The name of this course object
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}

	/**
	 * Sets the course path (directory) of this course object
	 * @param String $path The path of this course object
	 */
	function set_path($path)
	{
		$this->set_default_property(self :: PROPERTY_PATH, $path);
	}

	/**
	 * Sets the titular of this course object
	 * @param String $titular The titular of this course object
	 */
	function set_titular($titular)
	{
		$this->set_default_property(self :: PROPERTY_TITULAR, $titular);
	}

	/**
	 * Sets the language of this course object
	 * @param String $language The language of this course object
	 */
	function set_language($language)
	{
		$this->set_default_property(self :: PROPERTY_LANGUAGE, $language);
	}

	/**
	 * Sets the extlink URL of this course object
	 * @param String $url The URL if the extlink
	 */
	function set_extlink_url($url)
	{
		$this->set_default_property(self :: PROPERTY_EXTLINK_URL, $url);
	}

	/**
	 * Sets the extlink Name of this course object
	 * @param String $name The name of the exlink
	 */
	function set_extlink_name($name)
	{
		$this->set_default_property(self :: PROPERTY_EXTLINK_NAME, $name);
	}


	/**
	 * Sets the visibility of this course object
	 * @param String $visual The visual code
	 */
	function set_visibility($visibility)
	{
		$this->set_default_property(self :: PROPERTY_VISIBILITY, $visibility);
	}

	/**
	 * Sets if a user is allowed to subscribe on this course object
	 * @param Int $subscribe
	 */
	function set_subscribe_allowed($subscribe)
	{
		$this->set_default_property(self :: PROPERTY_SUBSCRIBE_ALLOWED, $subscribe);
	}

	/**
	 * Sets if a user is allowed to unsubscribe on this course object
	 * @param Int $subscribe
	 */
	function set_unsubscribe_allowed($subscribe)
	{
		$this->set_default_property(self :: PROPERTY_UNSUBSCRIBE_ALLOWED, $subscribe);
	}
	
	function set_layout($layout)
	{
		$this->set_default_property(self :: PROPERTY_LAYOUT, $layout);
	}
	
	function get_layout()
	{
		return $this->get_default_property(self :: PROPERTY_LAYOUT);
	}
	
	/**
	 * Sets the theme of this course object
	 * @param String $theme The theme of this course object
	 */
	function set_theme($theme)
	{
		$this->set_default_property(self :: PROPERTY_THEME, $theme);
	}

	/**
	 * Deletes the course object from persistent storage
	 * @return boolean
	 */
	function delete()
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->delete_course($this->get_id());
	}

	/**
	 * Creates the course object in persistent storage
	 * @return boolean
	 */
	function create()
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->create_course($this);
	}
	
	function create_all()
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->create_course_all($this);
	}

	/**
	 * Updates the course object in persistent storage
	 * @return boolean
	 */
	function update()
	{
		$wdm = WeblcmsDataManager :: get_instance();
		$success = $wdm->update_course($this);
		if (!$success)
		{
			return false;
		}

		return true;
	}

	/**
	 * Checks whether the given user is a course admin in this course
	 * @param int $user_id
	 * @return boolean
	 */
	function is_course_admin($user)
	{
		if ($user->is_platform_admin())
		{
			return true;
		}
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->is_course_admin($this, $user->get_id());
	}
	
	/**
	 * Determines if this course has a theme
	 * @return boolean
	 */
	function has_theme()
	{
		return (!is_null($this->get_theme()) ? true : false);
	}
	
	/**
	 * Gets the subscribed users of this course
	 * @return array An array of CourseUserRelation objects
	 */
	function get_subscribed_users()
	{
		$wdm = WeblcmsDataManager::get_instance();
		return $wdm->retrieve_course_users($this)->as_array();
	}
	/**
	 * Gets the course_groups defined in this course
	 * @return array An array of CourseGroup objects
	 */
	function get_course_groups()
	{
		$wdm = WeblcmsDataManager::get_instance();
		return $wdm->retrieve_course_groups($this->get_id())->as_array();
	}
}
?>