<?php
/**
 * $Id: physical_location.class.php 15410 2008-05-26 13:41:21Z Scara84 $
 * @package repository.learningobject
 * @subpackage physical_location
 */
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * This class represents an physical_location
 */
class PhysicalLocation extends LearningObject
{
	//Inherited
	function supports_attachments()
	{
		return true;
	}
}
?>