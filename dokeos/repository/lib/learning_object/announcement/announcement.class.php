<?php
/**
 * $Id$
 * @package repository.learningobject
 * @subpackage announcement
 */
require_once dirname(__FILE__) . '/../../learningobject.class.php';
/**
 * This class represents an announcement
 */
class Announcement extends LearningObject
{
	//Inherited
	function supports_attachments()
	{
		return true;
	}
}
?>