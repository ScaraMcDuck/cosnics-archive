<?php
/**
 * $Id$
 * @package application.personal_calendar
 */
require_once dirname(__FILE__).'/../personalcalendardatamanager.class.php';
require_once dirname(__FILE__).'/../../../../common/installer.class.php';
/**
 *	This installer can be used to create the storage structure for the
 * personal calendar application.
 */
class PersonalCalendarInstaller extends Installer{
	/**
	 * Constructor
	 */
    function PersonalCalendarInstaller() {
    }
	/**
	 * Runs the install-script.
	 */
	function install()
	{
		$this->create_storage_unit(dirname(__FILE__).'/personal_calendar.xml');
	}
	function create_storage_unit($path)
	{
		$storage_unit_info = parent::parse_xml_file($path);
		$dm = PersonalCalendarDataManager :: get_instance();
		echo '<pre>Creating Personal Calendar Storage Unit: '.$storage_unit_info['name'].'</pre>';flush();
		$dm->create_storage_unit($storage_unit_info['name'],$storage_unit_info['properties'],$storage_unit_info['indexes']);
	}
}
?>