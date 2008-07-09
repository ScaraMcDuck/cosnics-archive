<?php

require_once dirname(__FILE__) . '/../../learning_object.class.php';
require_once dirname(__FILE__) . '/inc/learning_style_survey_model.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurvey extends LearningObject
{
	const PROPERTY_SURVEY_TYPE = 'survey_type';

	private $categories;
	
	private $sections;
	
	private $model;
	
	function get_survey_model ()
	{
		if (!$this->model)
		{
			$this->model = LearningStyleSurveyModel :: factory($this->get_survey_type());
		}
		return $this->model;
	}
	
	function get_survey_type ()
	{
		return $this->get_additional_property(self :: PROPERTY_SURVEY_TYPE);
	}
	
	function set_survey_type ($type)
	{
		return $this->set_additional_property(self :: PROPERTY_SURVEY_TYPE, $type);
	}
	
	function is_versionable()
	{
		return false;
	}
	
	function get_additional_survey_parameters()
	{
		return $this->get_survey_model()->get_additional_parameters();
	}
	
	function get_survey_categories()
	{
		if (!$this->categories)
		{
			$dm = RepositoryDataManager :: get_instance();
			$this->categories = $dm->retrieve_learning_objects(
				'learning_style_survey_category',
				new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID, $this->get_id())
			)->as_array();
		}
		return $this->categories;
	}
	
	function get_survey_sections()
	{
		if (!$this->sections)
		{
			$dm = RepositoryDataManager :: get_instance();
			$this->sections = $dm->retrieve_learning_objects(
				'learning_style_survey_section',
				new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID, $this->get_id())
			)->as_array();
		}
		return $this->sections;
	}
	
	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_SURVEY_TYPE);
	}
}

?>