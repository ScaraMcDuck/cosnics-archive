<?php
/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
require_once dirname(__FILE__).'/../repositorytool.class.php';
require_once dirname(__FILE__).'/inc/maintenancewizard.class.php';

class MaintenanceTool extends RepositoryTool
{
	function run()
	{
		$wizard = new MaintenanceWizard($this);
		$wizard->run();
	}
}
?>