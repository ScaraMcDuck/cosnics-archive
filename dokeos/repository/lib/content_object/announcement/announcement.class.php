<?php
/**
 * $Id: announcement.class.php 23130 2009-09-25 12:40:53Z vanpouckesven $
 * @package repository.learningobject
 * @subpackage announcement
 */
require_once dirname(__FILE__) . '/../../content_object.class.php';
/**
 * This class represents an announcement
 */
class Announcement extends ContentObject
{
	//Inherited
	function supports_attachments()
	{
		return true;
	}
}
?>