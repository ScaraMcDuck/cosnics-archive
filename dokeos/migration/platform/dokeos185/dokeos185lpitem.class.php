<?php 
/**
 * migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/importlpitem.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/learning_object/learning_path_chapter/learning_path_chapter.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/learning_object/learning_path_item/learning_path_item.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/learningobjectpublication.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/learning_object/category/category.class.php';

/**
 * This class presents a Dokeos185 lp_item
 *
 * @author Sven Vanpoucke
 */
class Dokeos185LpItem extends ImportLpItem
{
	/**
	 * Migration data manager
	 */
	private static $mgdm;
	
	/**
	 * Dokeos185LpItem properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_LP_ID = 'lp_id';
	const PROPERTY_ITEM_TYPE = 'item_type';
	const PROPERTY_REF = 'ref';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_PATH = 'path';
	const PROPERTY_MIN_SCORE = 'min_score';
	const PROPERTY_MAX_SCORE = 'max_score';
	const PROPERTY_MASTERY_SCORE = 'mastery_score';
	const PROPERTY_PARENT_ITEM_ID = 'parent_item_id';
	const PROPERTY_PREVIOUS_ITEM_ID = 'previous_item_id';
	const PROPERTY_NEXT_ITEM_ID = 'next_item_id';
	const PROPERTY_DISPLAY_ORDER = 'display_order';
	const PROPERTY_PREREQUISITE = 'prerequisite';
	const PROPERTY_PARAMETERS = 'parameters';
	const PROPERTY_LAUNCH_DATA = 'launch_data';
	const PROPERTY_MAX_TIME_ALLOWED = 'max_time_allowed';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185LpItem object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185LpItem($defaultProperties = array ())
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_LP_ID, self :: PROPERTY_ITEM_TYPE, self :: PROPERTY_REF, self :: PROPERTY_TITLE, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_PATH, self :: PROPERTY_MIN_SCORE, self :: PROPERTY_MAX_SCORE, self :: PROPERTY_MASTERY_SCORE, self :: PROPERTY_PARENT_ITEM_ID, self :: PROPERTY_PREVIOUS_ITEM_ID, self :: PROPERTY_NEXT_ITEM_ID, self :: PROPERTY_DISPLAY_ORDER, self :: PROPERTY_PREREQUISITE, self :: PROPERTY_PARAMETERS, self :: PROPERTY_LAUNCH_DATA, self :: PROPERTY_MAX_TIME_ALLOWED);
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
	 * Returns the id of this Dokeos185LpItem.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the lp_id of this Dokeos185LpItem.
	 * @return the lp_id.
	 */
	function get_lp_id()
	{
		return $this->get_default_property(self :: PROPERTY_LP_ID);
	}

	/**
	 * Returns the item_type of this Dokeos185LpItem.
	 * @return the item_type.
	 */
	function get_item_type()
	{
		return $this->get_default_property(self :: PROPERTY_ITEM_TYPE);
	}

	/**
	 * Returns the ref of this Dokeos185LpItem.
	 * @return the ref.
	 */
	function get_ref()
	{
		return $this->get_default_property(self :: PROPERTY_REF);
	}

	/**
	 * Returns the title of this Dokeos185LpItem.
	 * @return the title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}

	/**
	 * Returns the description of this Dokeos185LpItem.
	 * @return the description.
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}

	/**
	 * Returns the path of this Dokeos185LpItem.
	 * @return the path.
	 */
	function get_path()
	{
		return $this->get_default_property(self :: PROPERTY_PATH);
	}

	/**
	 * Returns the min_score of this Dokeos185LpItem.
	 * @return the min_score.
	 */
	function get_min_score()
	{
		return $this->get_default_property(self :: PROPERTY_MIN_SCORE);
	}

	/**
	 * Returns the max_score of this Dokeos185LpItem.
	 * @return the max_score.
	 */
	function get_max_score()
	{
		return $this->get_default_property(self :: PROPERTY_MAX_SCORE);
	}

	/**
	 * Returns the mastery_score of this Dokeos185LpItem.
	 * @return the mastery_score.
	 */
	function get_mastery_score()
	{
		return $this->get_default_property(self :: PROPERTY_MASTERY_SCORE);
	}

	/**
	 * Returns the parent_item_id of this Dokeos185LpItem.
	 * @return the parent_item_id.
	 */
	function get_parent_item_id()
	{
		return $this->get_default_property(self :: PROPERTY_PARENT_ITEM_ID);
	}

	/**
	 * Returns the previous_item_id of this Dokeos185LpItem.
	 * @return the previous_item_id.
	 */
	function get_previous_item_id()
	{
		return $this->get_default_property(self :: PROPERTY_PREVIOUS_ITEM_ID);
	}

	/**
	 * Returns the next_item_id of this Dokeos185LpItem.
	 * @return the next_item_id.
	 */
	function get_next_item_id()
	{
		return $this->get_default_property(self :: PROPERTY_NEXT_ITEM_ID);
	}

	/**
	 * Returns the display_order of this Dokeos185LpItem.
	 * @return the display_order.
	 */
	function get_display_order()
	{
		return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
	}

	/**
	 * Returns the prerequisite of this Dokeos185LpItem.
	 * @return the prerequisite.
	 */
	function get_prerequisite()
	{
		return $this->get_default_property(self :: PROPERTY_PREREQUISITE);
	}

	/**
	 * Returns the parameters of this Dokeos185LpItem.
	 * @return the parameters.
	 */
	function get_parameters()
	{
		return $this->get_default_property(self :: PROPERTY_PARAMETERS);
	}

	/**
	 * Returns the launch_data of this Dokeos185LpItem.
	 * @return the launch_data.
	 */
	function get_launch_data()
	{
		return $this->get_default_property(self :: PROPERTY_LAUNCH_DATA);
	}

	/**
	 * Returns the max_time_allowed of this Dokeos185LpItem.
	 * @return the max_time_allowed.
	 */
	function get_max_time_allowed()
	{
		return $this->get_default_property(self :: PROPERTY_MAX_TIME_ALLOWED);
	}
	
	/**
	 * Check if the lp item is valid
	 * @param array $array the parameters for the validation
	 * @return true if the lp item is valid 
	 */
	function is_valid($array)
	{
		$course = $array['course'];	
		if(!$this->get_id() || !$this->get_item_type() || 
		!($this->get_title() || $this->get_description())
		|| !self :: $mgdm->get_id_reference($this->get_lp_id(),'repository_learning_path'))
		{		 
			self :: $mgdm->add_failed_element($this->get_id(),
				$course->get_db_name() . '.lp_item');
			return false;
		}
		return true;
	}
	
	/**
	 * Convert to new lp item
	 * @param array $array the parameters for the conversion
	 * @return the new lp item
	 */
	function convert_to_lcms($array)
	{
		$id = self :: $mgdm->get_id_reference($this->get_lp_id(),'repository_learning_path');
		$course = $array['course'];
		$new_course_code = self :: $mgdm->get_id_reference($course->get_code(),'weblcms_course');

		if($id)		
		{
			$lo = self :: $mgdm->get_owner_learning_object($id,'learning_path');
			$new_user_id = $lo->get_owner_id();
		}
		else
		{
			$new_user_id = self :: $mgdm->get_owner($new_course_code);
		}
		
		$new_user_id = $lo->get_owner_id();
		$course = $array['course'];
		$new_course_code = self :: $mgdm->get_id_reference($course->get_code(),'weblcms_course');
		
		if($this->get_item_type() == 'dokeos_chapter')
		{
			$lcms_lp_item = new LearningPathChapter();
		}
		else
		{
			$referentie = 0;
			$lcms_lp_item = new LearningPathItem();
			//forum parameters
			switch($this->get_item_type())
			{
				case 'document': $referentie = self :: $mgdm->get_id_reference($this->get_path(),'repository_document'); break;
				case 'quiz': $referentie = self :: $mgdm->get_id_reference($this->get_path(),'exercice'); break;
				case 'link': $referentie = self :: $mgdm->get_id_reference($this->get_path(),'repository_link'); break;
				case 'student_publication': $referentie = self :: $mgdm->get_id_reference($this->get_path(),'repository_work'); break;
				case 'forum': $referentie = self :: $mgdm->get_id_reference($this->get_path(),'repository_forum'); break;
				case 'thread': $referentie = self :: $mgdm->get_id_reference($this->get_path(),'repository_forum_thread'); break;
			}
		}
		
		// Category for lp item/chapter already exists?
		$lcms_category_id = self :: $mgdm->get_parent_id($new_user_id, 'category',
			Translation :: get('learning_paths'));
		if(!$lcms_category_id)
		{
			//Create category for tool in lcms
			$lcms_repository_category = new Category();
			$lcms_repository_category->set_owner_id($new_user_id);
			$lcms_repository_category->set_title(Translation :: get('learning_paths'));
			$lcms_repository_category->set_description('...');
	
			//Retrieve repository id from course
			$repository_id = self :: $mgdm->get_parent_id($new_user_id, 
				'category', Translation :: get('MyRepository'));
			$lcms_repository_category->set_parent_id($repository_id);
			
			//Create category in database
			$lcms_repository_category->create();
			
			$lcms_lp_item->set_parent_id($lcms_repository_category->get_id());
		}
		else
		{
			$lcms_lp_item->set_parent_id($lcms_category_id);	
		}
		if(!$this->get_title())
		{	
			$lcms_lp_item->set_title(substr($this->get_description(),0,20));
		}
		else
			$lcms_lp_item->set_title($this->get_title());
		
		if(!$this->get_description())
			$lcms_lp_item->set_description($this->get_title());
		else
			$lcms_lp_item->set_description($this->get_description());
		
		$lcms_lp_item->set_owner_id($new_user_id);
		$lcms_lp_item->set_display_order_index($this->get_display_order());
		
		//create announcement in database
		$lcms_lp_item->create_all();
		
		//Add id references to temp table
		self :: $mgdm->add_id_reference($this->get_id(), $lcms_lp_item->get_id(), 'lp_item');
		
		/*
		//publication
		if($this->item_property->get_visibility() <= 1) 
		{
			$publication = new LearningObjectPublication();
			
			$publication->set_learning_object($lcms_announcement);
			$publication->set_course_id($new_course_code);
			$publication->set_publisher_id($new_user_id);
			$publication->set_tool('announcement');
			$publication->set_category_id(0);
			//$publication->set_from_date(self :: $mgdm->make_unix_time($this->item_property->get_start_visible()));
			//$publication->set_to_date(self :: $mgdm->make_unix_time($this->item_property->get_end_visible()));
			$publication->set_from_date(0);
			$publication->set_to_date(0);
			$publication->set_publication_date(self :: $mgdm->make_unix_time($this->item_property->get_insert_date()));
			$publication->set_modified_date(self :: $mgdm->make_unix_time($this->item_property->get_lastedit_date()));
			//$publication->set_modified_date(0);
			//$publication->set_display_order_index($this->get_display_order());
			$publication->set_display_order_index(0);
			
			if($this->get_email_sent())
				$publication->set_email_sent($this->get_email_sent());
			else
				$publication->set_email_sent(0);
			
			$publication->set_hidden($this->item_property->get_visibility() == 1?0:1);
			
			//create publication in database
			$publication->create();
		}
		*/
		return $lcms_lp_item;
	}
	
	/**
	 * Retrieve all lp items from the database
	 * @param array $parameters parameters for the retrieval
	 * @return array of lp items
	 */
	static function get_all($parameters)
	{
		self :: $mgdm = $parameters['mgdm'];

		if($parameters['del_files'] =! 1)
			$tool_name = 'lp_item';
		
		$coursedb = $parameters['course']->get_db_name();
		$tablename = 'lp_item';
		$classname = 'Dokeos185LpItem';
			
		return self :: $mgdm->get_all($coursedb, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);	
	}

	static function get_database_table($parameters)
	{
		$array = array();
		$array['database'] = $parameters['course']->get_db_name();
		$array['table'] = 'lp_item';
		return $array;
	}
}

?>