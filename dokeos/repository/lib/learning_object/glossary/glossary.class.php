<?php
/**
 * $Id: glossary.class.php 15410 2008-05-26 13:41:21Z Scara84 $
 * @package repository.learningobject
 * @subpackage glossary
 */
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * This class represents an glossary
 */
class Glossary extends LearningObject
{
	
	function get_allowed_types()
	{
		return array('glossary_item');
	}
}
?>