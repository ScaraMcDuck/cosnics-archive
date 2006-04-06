<?php
require_once dirname(__FILE__) . '/../../learningobject.class.php';
require_once dirname(__FILE__) . '/../../repositorydatamanager.class.php';
/**
 * @package learningobject.category
 */
class Category extends LearningObject
{
	function move_allowed($target)
	{
		if ($target == $this->get_id())
		{
			return false;
		}
		$targetObj = RepositoryDataManager :: get_instance()->retrieve_learning_object($target);
		return !$targetObj->has_ancestor($this->get_id());
	}
}
?>