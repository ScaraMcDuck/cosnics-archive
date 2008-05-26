<?php

require_once dirname(__FILE__) . '/../../learning_object.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyCategory extends LearningObject
{
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