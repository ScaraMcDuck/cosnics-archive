<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__).'/../lib/reporting_data_manager.class.php';
require_once Path :: get_library_path().'installer.class.php';
require_once Path :: get_library_path().'filesystem/filesystem.class.php';

class ReportingInstaller extends Installer
{
	function ReportingInstaller($values)
	{
		parent :: __construct($values, ReportingDataManager :: get_instance());
	}
	
	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>