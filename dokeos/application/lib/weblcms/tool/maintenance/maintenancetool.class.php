<?php
/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
require_once dirname(__FILE__).'/../repositorytool.class.php';
require_once dirname(__FILE__).'/inc/recycler.class.php';
class MaintenanceTool extends RepositoryTool
{
	function run()
	{
		$this->display_header();
		echo '<strong>Only sample recycle tool implemented at this moment.</strong>';
		$recycler = new Recycler($this);
		$recycler->run();
		$this->display_footer();
	}
}
?>