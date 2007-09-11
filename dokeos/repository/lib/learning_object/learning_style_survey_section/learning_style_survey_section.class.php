<?php

require_once dirname(__FILE__) . '/../../learningobject.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveySection extends LearningObject
{
	private $sections;
	
	function get_section_questions()
	{
		if (!$this->sections)
		{
			$dm = RepositoryDataManager :: get_instance();
			$this->sections = $dm->retrieve_learning_objects(
				'learning_style_survey_question',
				new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID, $this->get_id())
			)->as_array();
		}
		return $this->sections;
	}
	
	function is_ordered()
	{
		return true;
	}

	function is_master_type()
	{
		return false;
	}

	function is_versionable()
	{
		return false;
	}
}

?>