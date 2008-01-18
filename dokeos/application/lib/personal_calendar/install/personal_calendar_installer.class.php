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
		echo '<div class="learning_object" style="padding: 15px 15px 15px 76px; background-image: url(../img/admin_personal_calendar.gif);">';
		echo '<div class="title">'. get_lang('AppPersonalCalendar') .'</div>';
		echo '<div class="description">';
		$this->create_storage_unit(dirname(__FILE__).'/personal_calendar.xml');
		
		echo '<br /><span style="color: #008000; font-weight: bold;">'. get_lang('ApplicationSuccess') .'</span>';
		echo '</div>';
		echo '</div>';
	}
	function create_storage_unit($path)
	{
		$storage_unit_info = parent::parse_xml_file($path);
		$dm = PersonalCalendarDataManager :: get_instance();
		echo 'Creating Personal Calendar Storage Unit: '.$storage_unit_info['name'].'<br />';flush();
		$dm->create_storage_unit($storage_unit_info['name'],$storage_unit_info['properties'],$storage_unit_info['indexes']);
	}
}
?>