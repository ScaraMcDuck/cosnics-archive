<?php
/**
 * $Id$
 * @package repository.learningobject
 * @subpackage announcement
 */
require_once dirname(__FILE__).'/../../content_object_form.class.php';
require_once dirname(__FILE__).'/announcement.class.php';
/**
 * This class represents a form to create or update announcements
 */
class AnnouncementForm extends ContentObjectForm
{
	// Inherited
	function create_content_object()
	{
		$object = new Announcement();
		$this->set_content_object($object);
		return parent :: create_content_object();
	}

	function set_csv_values($valuearray)
	{
		$defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
		$defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];	
		parent :: set_values($defaults);			
	}	
}
?>
