<?php

require_once dirname(__FILE__) . '/../../learning_object.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyAnswer extends LearningObject
{
	const PROPERTY_ANSWER_CATEGORY_IDS = 'answer_category_ids';
	
	function get_answer_category_ids () {
		$imploded = $this->get_additional_property(self :: PROPERTY_ANSWER_CATEGORY_IDS);
		return (is_null($imploded) ? array() : explode(',', $imploded));
	}
	
	function set_answer_category_ids ($cids) {
		return $this->set_additional_property(self :: PROPERTY_ANSWER_CATEGORY_IDS,
			(count($cids) ? implode(',', $cids) : null));
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
		return array (self :: PROPERTY_ANSWER_CATEGORY_IDS);
	}
}

?>