<?php
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
require_once dirname(__FILE__) . '/../../complex_content_object_item.class.php';

class ComplexPortfolio extends ComplexContentObjectItem
{
	function get_allowed_types()
	{
		return array('portfolio', 'portfolio_item');
	}
}
?>