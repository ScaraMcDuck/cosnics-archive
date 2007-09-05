<?php

require_once dirname(__FILE__) . '/../../learningobject.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyQuestion extends LearningObject
{
	const PROPERTY_QUESTION_CATEGORY_ID = 'question_category_id';
	
	function get_question_category_id () {
		return $this->get_additional_property(self :: PROPERTY_QUESTION_CATEGORY_ID);
	}
	
	function set_question_category_id ($cid) {
		return $this->set_additional_property(self :: PROPERTY_QUESTION_CATEGORY_ID, $cid);
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