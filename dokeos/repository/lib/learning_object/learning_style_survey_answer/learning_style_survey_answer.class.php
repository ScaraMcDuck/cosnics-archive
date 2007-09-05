<?php

require_once dirname(__FILE__) . '/../../learningobject.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyAnswer extends LearningObject
{
	const PROPERTY_ANSWER_CATEGORY_ID = 'answer_category_id';
	
	function get_answer_category_id () {
		return $this->get_additional_property(self :: PROPERTY_ANSWER_CATEGORY_ID);
	}
	
	function set_answer_category_id ($cid) {
		return $this->set_additional_property(self :: PROPERTY_ANSWER_CATEGORY_ID, $cid);
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