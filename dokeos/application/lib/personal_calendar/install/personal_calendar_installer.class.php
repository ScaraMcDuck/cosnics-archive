<?php
/**
 * $Id$
 * @package application.personal_calendar
 */
/**
 *	This installer can be used to create the storage structure for the
 * personal calendar application.
 */
class PersonalCalendarInstaller {
	/**
	 * Constructor
	 */
    function PersonalCalendarInstaller() {
    }
	/**
	 * Runs the install-script.
	 * @todo This function now uses the function of the RepositoryInstaller
	 * class. These shared functions should be available in a common base class.
	 */
	function install()
	{
		$repository_installer = new RepositoryInstaller();
		$repository_installer->parse_xml_file(dirname(__FILE__).'/personal_calendar.xml');
	}
}
?>