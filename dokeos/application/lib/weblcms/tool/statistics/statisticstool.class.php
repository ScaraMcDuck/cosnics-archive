<?php
/**
 * $Id$
 * Statistics tool
 * @package application.weblcms.tool
 * @subpackage statistics
 */
require_once dirname(__FILE__).'/../tool.class.php';

class StatisticsTool extends Tool
{
	function run()
	{
		$this->display_header();
		echo 'Group Tool';
		$this->display_footer();
	}
}
?>