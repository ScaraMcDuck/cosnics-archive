<?php
/**
 * $Id$
 * User tool
 * @package application.weblcms.tool
 * @subpackage user
 */
require_once dirname(__FILE__).'/../tool.class.php';
/**
 * Tool to manage users in the course.
 * @todo: Implementation (recycle old user tool)
 */
class UserTool extends Tool
{
	function run()
	{
		$this->display_header();
		echo 'User Tool';
		$this->display_footer();
	}
}
?>