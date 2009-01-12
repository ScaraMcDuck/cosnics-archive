<?php
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * @package repository.learningobject
 * @subpackage wiki
 */
class Wiki extends LearningObject 
{
	function get_allowed_types()
	{
		return array('wiki_page');
	}
}
?>