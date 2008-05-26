<?php
/**
 * @package repository.learningobject
 * @subpackage userinfo_def
 * @author Sven Vanpoucke
 */
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * This class represents a userinfo definition
 */
class UserinfoDef extends LearningObject
{
	//Inherited
	function supports_attachments()
	{
		return true;
	}
}
?>