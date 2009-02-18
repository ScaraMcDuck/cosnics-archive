<?php
/**
 * $Id: glossary_item.class.php 15410 2008-05-26 13:41:21Z Scara84 $
 * @package repository.learningobject
 * @subpackage glossary_item
 */
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * This class represents an glossary_item
 */
class GlossaryItem extends LearningObject
{
	//Inherited
	function supports_attachments()
	{
		return true;
	}
}
?>