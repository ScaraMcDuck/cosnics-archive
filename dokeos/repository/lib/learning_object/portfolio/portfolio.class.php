<?php
/**
 * $Id: portfolio.class.php 15410 2008-05-26 13:41:21Z Scara84 $
 * @package repository.learningobject
 * @subpackage portfolio
 */
require_once dirname(__FILE__) . '/../../content_object.class.php';
/**
 * This class represents an portfolio
 */
class Portfolio extends ContentObject
{
	function get_allowed_types()
	{
		return array('portfolio', 'portfolio_item');
	}
}
?>