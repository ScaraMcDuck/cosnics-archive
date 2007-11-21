<?php
/**
 * @package repository.learningobject
 * @subpackage feedback
 */
require_once dirname(__FILE__) . '/../../learningobject.class.php';
/**
 * A feedback
 */
class Feedback extends LearningObject {
	function supports_attachments()
	{	
		return true;

	}
}
?>