<?php

require_once dirname(__FILE__) . '/../../learningobject.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveySection extends LearningObject
{
	function get_section_questions()
	{
		$dm = RepositoryDataManager :: get_instance();
		return $dm->retrieve_learning_objects(
			'learning_style_survey_question',
			new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID, $this->get_id())
		)->as_array();
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