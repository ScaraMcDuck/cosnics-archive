<?php
require_once dirname(__FILE__) . '/../../learningobject.class.php';
/**
 * @package repository.learningobject
 * @subpackage portfolio
 */
class PortfolioItem extends LearningObject
{
/*
	// Inherited
	function is_master_type()
	{
		return false;
	}
*/
	function supports_attachments()
	{
		return true;
	}
}
?>