<?php
/**
 * @package repository.learningobject
 * @subpackage userinfo_content
 * @author Sven Vanpoucke
 */
require_once dirname(__FILE__) . '/../../learningobject.class.php';
/**
 * This class represents an userinfo content
 */
class UserinfoContent extends LearningObject
{
	//Inherited
	function supports_attachments()
	{
		return true;
	}
}
?>