<?php
/**
 * $Id$
 * @package application.personal_calendar
 */
require_once dirname(__FILE__).'/../personalcalendardatamanager.class.php';
require_once Path :: get_library_path().'installer.class.php';
/**
 *	This installer can be used to create the storage structure for the
 * personal calendar application.
 */
class PersonalCalendarInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function PersonalCalendarInstaller($values)
    {
		parent :: __construct($values, PersonalCalendarDataManager :: get_instance());
    }
	
	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>