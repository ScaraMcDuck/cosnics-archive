<?php
/**
 * $Id: portal_home_form.class.php 15410 2008-05-26 13:41:21Z Scara84 $
 * @package repository.learningobject
 * @subpackage portal_home
 */
require_once dirname(__FILE__).'/../../content_object_form.class.php';
require_once dirname(__FILE__).'/portal_home.class.php';
/**
 * This class represents a form to create or update portal_homes
 */
class PortalHomeForm extends ContentObjectForm
{
	// Inherited
	function create_content_object()
	{
		$object = new PortalHome();
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
