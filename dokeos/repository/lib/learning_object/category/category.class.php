<?php
/**
 * $Id$
 * @package repository.learningobject
 * @subpackage category
 */
require_once dirname(__FILE__) . '/../../learning_object.class.php';
require_once dirname(__FILE__) . '/../../repository_data_manager.class.php';
/**
 * A category
 */
class Category extends LearningObject
{
	function is_versionable()
	{
		return false;
	}
}
?>