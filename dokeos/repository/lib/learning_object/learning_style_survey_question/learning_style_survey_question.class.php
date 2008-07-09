<?php

require_once dirname(__FILE__) . '/../../learning_object.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyQuestion extends LearningObject
{
	const PROPERTY_QUESTION_CATEGORY_IDS = 'question_category_ids';
	
	function get_question_category_ids () {
		$imploded = $this->get_additional_property(self :: PROPERTY_QUESTION_CATEGORY_IDS);
		return (is_null($imploded) ? array() : explode(',', $imploded));
	}
	
	function set_question_category_ids ($cids) {
		return $this->set_additional_property(self :: PROPERTY_QUESTION_CATEGORY_IDS,
			(count($cids) ? implode(',', $cids) : null));
	}
	
	function get_question_answers()
	{
		$dm = RepositoryDataManager :: get_instance();
		return $dm->retrieve_learning_objects(
			'learning_style_survey_answer',
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
	
	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_QUESTION_CATEGORY_IDS);
	}
}

?>