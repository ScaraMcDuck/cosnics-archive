<?php
/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importsystemannouncement.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object/announcement/announcement.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/learning_object/category/category.class.php';

/**
 * This class represents an old Dokeos 1.8.5 system announcement
 *
 * @author David Van Wayenberghµ
 * @author Sven Vanpoucke
 */
 
class Dokeos185SystemAnnouncement extends Import
{
	/**
	 * Migration data manager
	 */
	private static $mgdm;

	/**
	 * course relation user properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_CONTENT = 'content';
	const PROPERTY_DATE_START = 'date_start';
	const PROPERTY_DATE_END = 'date_end';
	const PROPERTY_VISIBLE_TEACHER = 'visible_teacher';
	const PROPERTY_VISIBLE_STUDENT = 'visible_student';
	const PROPERTY_VISIBLE_GUEST = 'visible_guest';
	const PROPERTY_LANG = 'lang';
	
	
	/**
	 * Default properties of the system annoucement object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new system annoucement object.
	 * @param array $defaultProperties The default properties of the system annoucement
	 *                                 object. Associative array.
	 */
	function Dokeos185SystemAnnouncement($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this system annoucement object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this system annoucement.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all system annoucement.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self::PROPERTY_TITLE,
		self::PROPERTY_CONTENT, self::PROPERTY_DATE_START,
		self::PROPERTY_DATE_END,self::PROPERTY_VISIBLE_TEACHER,
		self::PROPERTY_VISIBLE_STUDENT,self::PROPERTY_VISIBLE_GUEST,
		self::PROPERTY_LANG);
	}
	
	/**
	 * Sets a default property of this system annoucement by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default system annoucement
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
	 * Sets the default properties of this class
	 */
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Returns the id of this system announcement.
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	
	/**
	 * Returns the title of this system announcement.
	 * @return String The title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}
	
	/**
	 * Returns the content of this system announcement.
	 * @return String The content.
	 */
	function get_content()
	{
		return $this->get_default_property(self :: PROPERTY_CONTENT);
	}
	
	/**
	 * Returns the date_start of this system announcement.
	 * @return String The date_start.
	 */
	function get_date_start()
	{
		return $this->get_default_property(self :: PROPERTY_DATE_START);
	}
	
	/**
	 * Returns the date_end of this system announcement.
	 * @return String The date_end.
	 */
	function get_date_end()
	{
		return $this->get_default_property(self :: PROPERTY_DATE_END);
	}
	
	/**
	 * Returns the visible_teacher of this system announcement.
	 * @return int The visible_teacher.
	 */
	function get_visible_teacher()
	{
		return $this->get_default_property(self :: PROPERTY_VISIBLE_TEACHER);
	}
	
	/**
	 * Returns the visible_student of this system announcement.
	 * @return int The visible_student.
	 */
	function get_visible_student()
	{
		return $this->get_default_property(self :: PROPERTY_VISIBLE_STUDENT);
	}
	
	/**
	 * Returns the visible_guest of this system announcement.
	 * @return int The visible_guest.
	 */
	function get_visible_guest()
	{
		return $this->get_default_property(self :: PROPERTY_VISIBLE_GUEST);
	}
	
	/**
	 * Returns the lang of this system announcement.
	 * @return String The lang.
	 */
	function get_lang()
	{
		return $this->get_default_property(self :: PROPERTY_LANG);
	}
	
	function is_valid_system_announcement()
	{
		if(!($this->get_title() || $this->get_content()))
		{
			self :: $mgdm->add_failed_element($this->get_id(), 'dokeos_main.sys_announcement');
			return false;
		}
		
		return true;
	}
	
	function convert_to_new_system_announcement($admin_id)
	{	
		$lcms_repository_announcement = new Announcement();
		$lcms_repository_announcement->set_owner_id($admin_id);
		
		if(!$this->get_title())
			$lcms_repository_announcement->set_title(substr($this->get_content(),0,20));
		else
			$lcms_repository_announcement->set_title($this->get_title());
		
		if(!$this->get_content())
			$lcms_repository_announcement->set_description($this->get_title());
		else
			$lcms_repository_announcement->set_description($this->get_content());
		
		// Category for announcements already exists?
		$lcms_category_id = self :: $mgdm->get_parent_id($admin_id, 'category',
			Translation :: get_lang('system_announcements'));
		if(!$lcms_category_id)
		{
			//Create category for tool in lcms
			$lcms_repository_category = new Category();
			$lcms_repository_category->set_owner_id($admin_id);
			$lcms_repository_category->set_title(Translation :: get_lang('system_announcements'));
			$lcms_repository_category->set_description('...');
	
			//Retrieve repository id from user
			$repository_id = self :: $mgdm->get_parent_id($admin_id, 
				'category', Translation :: get_lang('MyRepository'));
	
			$lcms_repository_category->set_parent_id($repository_id);
			
			//Create category in database
			$lcms_repository_category->create();
			
			$lcms_repository_announcement->set_parent_id($lcms_repository_category->get_id());
		}
		else
		{
			$lcms_repository_announcement->set_parent_id($lcms_category_id);
		}
		
		//Create announcement in database
		$lcms_repository_announcement->create();
		
		return $lcms_repository_announcement;
	}
	
	static function get_all($parameters)
	{
		self :: $mgdm = $parameters['mgdm'];
		
		$db = 'main_database';
		$tablename = 'sys_announcement';
		$classname = 'Dokeos185SystemAnnouncement';
			
		return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name);	
	}
}
?>