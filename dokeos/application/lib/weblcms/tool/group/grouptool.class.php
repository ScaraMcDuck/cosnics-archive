<?php
/**
 * $Id$
 * Group tool
 * @package application.weblcms.tool
 * @subpackage group
 */
require_once dirname(__FILE__).'/../tool.class.php';

class GroupTool extends Tool
{
	function run()
	{
		$this->display_header();
		echo 'Group Tool';
		$this->display_footer();
	}
}
?>