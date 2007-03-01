<?php
/**
 * $Id$
 * @package repository.learningobject
 * @subpackage category
 */
require_once dirname(__FILE__) . '/../../learningobject.class.php';
require_once dirname(__FILE__) . '/../../repositorydatamanager.class.php';
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