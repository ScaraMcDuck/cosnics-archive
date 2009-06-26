<?php
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
require_once dirname(__FILE__) . '/../../complex_learning_object_item.class.php';

class ComplexPortfolio extends ComplexLearningObjectItem
{
	function get_allowed_types()
	{
		return array('portfolio', 'portfolio_item');
	}
}
?>