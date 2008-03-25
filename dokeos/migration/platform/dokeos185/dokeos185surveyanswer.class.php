<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 survey_answer
 *
 * @author Sven Vanpoucke
 */
class Dokeos185SurveyAnswer
{
	private static $mgdm;
	/**
	 * Dokeos185SurveyAnswer properties
	 */
	const PROPERTY_ANSWER_ID = 'answer_id';
	const PROPERTY_SURVEY_ID = 'survey_id';
	const PROPERTY_QUESTION_ID = 'question_id';
	const PROPERTY_OPTION_ID = 'option_id';
	const PROPERTY_VALUE = 'value';
	const PROPERTY_USER = 'user';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185SurveyAnswer object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185SurveyAnswer($defaultProperties = array ())
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
		return array (self :: PROPERTY_ANSWER_ID, self :: PROPERTY_SURVEY_ID, self :: PROPERTY_QUESTION_ID, self :: PROPERTY_OPTION_ID, self :: PROPERTY_VALUE, self :: PROPERTY_USER);
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
	 * Returns the answer_id of this Dokeos185SurveyAnswer.
	 * @return the answer_id.
	 */
	function get_answer_id()
	{
		return $this->get_default_property(self :: PROPERTY_ANSWER_ID);
	}

	/**
	 * Returns the survey_id of this Dokeos185SurveyAnswer.
	 * @return the survey_id.
	 */
	function get_survey_id()
	{
		return $this->get_default_property(self :: PROPERTY_SURVEY_ID);
	}

	/**
	 * Returns the question_id of this Dokeos185SurveyAnswer.
	 * @return the question_id.
	 */
	function get_question_id()
	{
		return $this->get_default_property(self :: PROPERTY_QUESTION_ID);
	}

	/**
	 * Returns the option_id of this Dokeos185SurveyAnswer.
	 * @return the option_id.
	 */
	function get_option_id()
	{
		return $this->get_default_property(self :: PROPERTY_OPTION_ID);
	}

	/**
	 * Returns the value of this Dokeos185SurveyAnswer.
	 * @return the value.
	 */
	function get_value()
	{
		return $this->get_default_property(self :: PROPERTY_VALUE);
	}

	/**
	 * Returns the user of this Dokeos185SurveyAnswer.
	 * @return the user.
	 */
	function get_user()
	{
		return $this->get_default_property(self :: PROPERTY_USER);
	}
	
	static function get_all($parameters)
	{
		self :: $mgdm = $parameters['mgdm'];

		if($parameters['del_files'] =! 1)
			$tool_name = 'survey_answer';
		
		$coursedb = $parameters['course']->get_db_name();
		$tablename = 'survey_answer';
		$classname = 'Dokeos185SurveyAnswer';
			
		return self :: $mgdm->get_all($coursedb, $tablename, $classname, $tool_name);	
	}
	function is_valid($array)
	{
		$course = $array['course'];

		if(!$this->get_value() || !$this->get_user)
		{		 
			self :: $mgdm->add_failed_element($this->get_id(),
				$course->get_db_name() . '.survey_answer');
			return false;
		}
		return true;
	}
	
	function convert_to_lcms($array)
	{
		$course = $array['course'];
		$new_user_id = self :: $mgdm->get_id_reference($this->get_user(),'user_user');	
		$new_course_code = self :: $mgdm->get_id_reference($course->get_code(),'weblcms_course');
		
		if(!$new_user_id)
		{
			$new_user_id = self :: $mgdm->get_owner($new_course_code);
		}
		
		//survey parameters
		$lcms_survey_answer = new LearningStyleSurveyAnswer();
		
		// Category for surveys already exists?
		$lcms_category_id = self :: $mgdm->get_parent_id($new_user_id, 'category',
			Translation :: get_lang('surveys'));
		if(!$lcms_category_id)
		{
			//Create category for tool in lcms
			$lcms_repository_category = new Category();
			$lcms_repository_category->set_owner_id($new_user_id);
			$lcms_repository_category->set_title(Translation :: get_lang('surveys'));
			$lcms_repository_category->set_description('...');
	
			//Retrieve repository id from course
			$repository_id = self :: $mgdm->get_parent_id($new_user_id, 
				'category', Translation :: get_lang('MyRepository'));
			$lcms_repository_category->set_parent_id($repository_id);
			
			//Create category in database
			$lcms_repository_category->create();
			
			$lcms_survey_answer->set_parent_id($lcms_repository_category->get_id());
		}
		else
		{
			$lcms_survey_answer->set_parent_id($lcms_category_id);	
		}
		
		$lcms_survey_answer->set_description($this->get_value());
		
		$lcms_survey_answer->set_owner_id($new_user_id);
		
		//create announcement in database
		$lcms_survey->create_all();
		
		//publication
		/*
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
		return $lcms_survey_answer;
	}
}

?>