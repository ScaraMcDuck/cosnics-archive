<?php
/**
 * $Id: blog_form.class.php 15410 2008-05-26 13:41:21Z Scara84 $
 * @package repository.learningobject
 * @subpackage blog
 */
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/blog.class.php';
/**
 * This class represents a form to create or update blogs
 */
class BlogForm extends LearningObjectForm
{
	// Inherited
	function create_learning_object()
	{
		$object = new Blog();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}

	function set_csv_values($valuearray)
	{
		$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[LearningObject :: PROPERTY_PARENT_ID] = $valuearray[1];
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[2];	
		parent :: set_values($defaults);			
	}	
}
?>
