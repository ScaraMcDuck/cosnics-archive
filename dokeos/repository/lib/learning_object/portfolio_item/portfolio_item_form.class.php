<?php
require_once dirname(__FILE__) . '/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/portfolio_item.class.php';
/**
 * @package repository.learningobject
 * @subpackage portfolio
 */
class PortfolioItemForm extends LearningObjectForm
{
	function create_learning_object()
	{
		$object = new PortfolioItem();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}
?>