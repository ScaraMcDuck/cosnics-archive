<?php
/**
 * $Id: blog.class.php 15410 2008-05-26 13:41:21Z Scara84 $
 * @package repository.learningobject
 * @subpackage blog
 */
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * This class represents an blog
 */
class Blog extends LearningObject
{
	function get_allowed_types()
	{
		return array('blog_item');
	}
}
?>