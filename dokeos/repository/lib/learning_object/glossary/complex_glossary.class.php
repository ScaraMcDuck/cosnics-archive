<?php
/**
 * @package repository.learningobject
 * @subpackage learning_path_chapter
 */
require_once dirname(__FILE__) . '/../../complex_learning_object_item.class.php';

class ComplexGlossary extends ComplexLearningObjectItem
{
	function get_allowed_types()
	{
		return array('glossary_item');
	}
}
?>