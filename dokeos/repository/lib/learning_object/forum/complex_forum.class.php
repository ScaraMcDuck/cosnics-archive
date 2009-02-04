<?php
/**
 * @package repository.learningobject
 * @subpackage forum
 */
require_once dirname(__FILE__) . '/../../complex_learning_object_item.class.php';

class ComplexForum extends ComplexLearningObjectItem
{	
	function get_allowed_types()
	{
		return array('forum','forum_topic');
	}
}
?>