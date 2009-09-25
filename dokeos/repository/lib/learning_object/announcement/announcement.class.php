<?php
/**
 * $Id$
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