<?php
/**
 * $Id: announcement.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage announcement
 */
require_once dirname(__FILE__) . '/../../learningobject.class.php';
/**
 * This class represents a personal message
 */
class PersonalMessage extends LearningObject
{
	//Inherited
	function supports_attachments()
	{
		return true;
	}

	function is_versionable()
	{
		return false;
	}
}
?>