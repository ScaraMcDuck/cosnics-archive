<?php
/**
 * $Id: blog_item_form.class.php 15410 2008-05-26 13:41:21Z Scara84 $
 * @package repository.learningobject
 * @subpackage blog_item
 */
require_once dirname(__FILE__).'/../../content_object_form.class.php';
require_once dirname(__FILE__).'/blog_item.class.php';
/**
 * This class represents a form to create or update blog_items
 */
class BlogItemForm extends ContentObjectForm
{
	// Inherited
	function create_content_object()
	{
		$object = new BlogItem();
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
