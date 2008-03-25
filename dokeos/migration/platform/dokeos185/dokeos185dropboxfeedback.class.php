<?php 
/**
 * migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/importdropboxfeedback.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/learning_object/feedback/feedback.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/learningobjectpublication.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/learning_object/category/category.class.php';

/**
 * This class presents a Dokeos185 dropbox_feedback
 *
 * @author Sven Vanpoucke
 */
class Dokeos185DropboxFeedback
{
	/**
	 * Dokeos185DropboxFeedback properties
	 */
	const PROPERTY_FEEDBACK_ID = 'feedback_id';
	const PROPERTY_FILE_ID = 'file_id';
	const PROPERTY_AUTHOR_USER_ID = 'author_user_id';
	const PROPERTY_FEEDBACK = 'feedback';
	const PROPERTY_FEEDBACK_DATE = 'feedback_date';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185DropboxFeedback object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185DropboxFeedback($defaultProperties = array ())
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
		return array (self :: PROPERTY_FEEDBACK_ID, self :: PROPERTY_FILE_ID, self :: PROPERTY_AUTHOR_USER_ID, self :: PROPERTY_FEEDBACK, self :: PROPERTY_FEEDBACK_DATE);
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
	 * Returns the feedback_id of this Dokeos185DropboxFeedback.
	 * @return the feedback_id.
	 */
	function get_feedback_id()
	{
		return $this->get_default_property(self :: PROPERTY_FEEDBACK_ID);
	}

	/**
	 * Returns the file_id of this Dokeos185DropboxFeedback.
	 * @return the file_id.
	 */
	function get_file_id()
	{
		return $this->get_default_property(self :: PROPERTY_FILE_ID);
	}

	/**
	 * Returns the author_user_id of this Dokeos185DropboxFeedback.
	 * @return the author_user_id.
	 */
	function get_author_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_AUTHOR_USER_ID);
	}

	/**
	 * Returns the feedback of this Dokeos185DropboxFeedback.
	 * @return the feedback.
	 */
	function get_feedback()
	{
		return $this->get_default_property(self :: PROPERTY_FEEDBACK);
	}

	/**
	 * Returns the feedback_date of this Dokeos185DropboxFeedback.
	 * @return the feedback_date.
	 */
	function get_feedback_date()
	{
		return $this->get_default_property(self :: PROPERTY_FEEDBACK_DATE);
	}

	function is_valid($array)
	{
		$course = $array[0];
		if(!$this->get_feedback_id() || !$this->get_feedback()
			|| !$this->get_feedback_date())
		{		 
			self :: $mgdm->add_failed_element($this->get_id(),
				$course->get_db_name() . '.dropbox_feedback');
			return false;
		}
		return true;
	}
	
	/**
	 * Migration dropbox_feedback
	 */
	function convert_to_lcms($courses)
	{	
		$new_user_id = self :: $mgdm->get_id_reference($this->get_author_user_id(),'user_user');	
		$new_course_code = self :: $mgdm->get_id_reference($course->get_code(),'weblcms_course');
		
		if(!$new_user_id)
		{
			$new_user_id = self :: $mgdm->get_owner($new_course_code);
		}
		
		//dropbox_feedback parameters
		$lcms_dropbox_feedback = new Feedback();
		
		// Category for dropbox already exists?
		$lcms_category_id = self :: $mgdm->get_parent_id($new_user_id, 'dropbox',
			Translation :: get_lang('dropboxes'));
		if(!$lcms_category_id)
		{
			//Create category for tool in lcms
			$lcms_repository_category = new Category();
			$lcms_repository_category->set_owner_id($new_user_id);
			$lcms_repository_category->set_title(Translation :: get_lang('dropboxes'));
			$lcms_repository_category->set_description('...');
	
			//Retrieve repository id from course
			$repository_id = self :: $mgdm->get_parent_id($new_user_id, 
				'category', Translation :: get_lang('MyRepository'));
			$lcms_repository_category->set_parent_id($repository_id);
			
			//Create category in database
			$lcms_repository_category->create();
			
			$lcms_dropbox_feedback->set_parent_id($lcms_repository_category->get_id());
		}
		else
		{
			$lcms_dropbox_feedback->set_parent_id($lcms_category_id);	
		}
		
		
		$lcms_dropbox_feedback->set_title(substr($this->get_feedback(),0,20));	
		$lcms_dropbox_feedback->set_description($this->get_feedback());
		
		$lcms_dropbox_feedback->set_owner_id($new_user_id);
		$lcms_dropbox_feedback->set_creation_date(self :: $mgdm->make_unix_time($this->get_feedback_date()));
		$lcms_dropbox_feedback->set_modification_date(self :: $mgdm->make_unix_time($this->get_feedback_date()));
		
		$lcms_dropbox_feedback->set_state(1);
		
		//create announcement in database
		$lcms_dropbox_feedback->create_all();
		
		return $lcms_dropbox_feedback;
	}
	
	/** 
	 * Get all dropbox feedbacks from database
	 * @param Migration Data Manager $mgdm the datamanager from where the dropbox feedback should be retrieved;
	 */
	static function get_all($parameters = array())
	{
		self :: $mgdm = $parameters['mgdm'];
		
		$coursedb = $parameters['course'];
		$tablename = 'dropbox_feedback';
		$classname = 'Dokeos185DropboxFeedback';
			
		return self :: $mgdm->get_all($coursedb, $tablename, $classname, $tool_name);	
	}
}

?>