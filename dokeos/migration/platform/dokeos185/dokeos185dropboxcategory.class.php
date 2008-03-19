<?php 
/**
 * migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importdropboxcategory.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/learningobjectpublicationcategory.class.php';

/**
 * This class presents a Dokeos185 dropbox_category
 *
 * @author Sven Vanpoucke
 */
class Dokeos185DropboxCategory
{
	/**
	 * Dokeos185DropboxCategory properties
	 */
	const PROPERTY_CAT_ID = 'cat_id';
	const PROPERTY_CAT_NAME = 'cat_name';
	const PROPERTY_RECEIVED = 'received';
	const PROPERTY_SENT = 'sent';
	const PROPERTY_USER_ID = 'user_id';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185DropboxCategory object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185DropboxCategory($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Gets a default property by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	/**
	 * Gets the default properties
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (SELF :: PROPERTY_CAT_ID, SELF :: PROPERTY_CAT_NAME, SELF :: PROPERTY_RECEIVED, SELF :: PROPERTY_SENT, SELF :: PROPERTY_USER_ID);
	}

	/**
	 * Sets a default property by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	/**
	 * Sets the default properties of this class
	 */
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Returns the cat_id of this Dokeos185DropboxCategory.
	 * @return the cat_id.
	 */
	function get_cat_id()
	{
		return $this->get_default_property(self :: PROPERTY_CAT_ID);
	}

	/**
	 * Returns the cat_name of this Dokeos185DropboxCategory.
	 * @return the cat_name.
	 */
	function get_cat_name()
	{
		return $this->get_default_property(self :: PROPERTY_CAT_NAME);
	}

	/**
	 * Returns the received of this Dokeos185DropboxCategory.
	 * @return the received.
	 */
	function get_received()
	{
		return $this->get_default_property(self :: PROPERTY_RECEIVED);
	}

	/**
	 * Returns the sent of this Dokeos185DropboxCategory.
	 * @return the sent.
	 */
	function get_sent()
	{
		return $this->get_default_property(self :: PROPERTY_SENT);
	}

	/**
	 * Returns the user_id of this Dokeos185DropboxCategory.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}
	
	/**
	 * Sets the code of this category.
	 * @param String $code The code.
	 */
	function set_cat_id($code)
	{
		$this->set_default_property(self :: PROPERTY_CAT_ID, $code);
	}
	
	function is_valid($courses)
	{
		if(!$this->get_cat_name())
		{
			self :: $mgdm->add_failed_element($this->get_cat_id(),
				$course->get_db_name() . '.dropbox_category');
			return false;
		}
		
		return true;
	}
	
	/**
	 * Migration dropbox_category
	 */
	function convert_to_lcms($courses)
	{	
		//Course category parameters
		$lcms_dropbox_category = new LearningObjectPublicationCategory();
		$course = $courses[0];
		$lcms_dropbox_category->set_title($this->get_cat_name());
		
		$old_id = $this->get_cat_id();
		$index = 0;
		while(self :: $mgdm->code_available('weblcms_learning_object_publication_category',$this->get_cat_id()))
		{
			$this->set_code($this->get_cat_id() . ($index ++));
		}
		
		$lcms_dropbox_category->set_id($this->get_cat_id());
		
		//Add id references to temp table
		self :: $mgdm->add_id_reference($old_id, $lcms_dropbox_category->get_id(), 'weblcms_learning_object_publication_category');
		
		$lcms_dropbox_category->set_parent(0);
		
		$lcms_dropbox_category->set_course(self :: $mgdm->get_id_reference($course->get_code(),'weblcms_course'));
		
		$lcms_dropbox_category->set_tool('dropbox');
		
		//create course_category in database
		$lcms_dropbox_category->create();
		
		return $lcms_dropbox_category;
	}
	
	/** 
	 * Get all course categories from database
	 * @param Migration Data Manager $mgdm the datamanager from where the courses should be retrieved;
	 */
	static function get_all($array)
	{
		self :: $mgdm = $array[0];
		return self :: $mgdm->get_all($array[1], $array[2]);	
	}
}

?>