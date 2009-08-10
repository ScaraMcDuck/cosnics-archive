<?php
/**
 * $Id: personal_calendar_installer.class.php 12686 2007-07-03 11:32:57Z bmol $
 * @package users.install
 */
require_once dirname(__FILE__).'/../reservations_data_manager.class.php';
require_once Path :: get_library_path().'installer.class.php';

/**
 * This installer can be used to create the storage structure for the
 * users application.
 */
class ReservationsInstaller extends Installer
{
	/**
	 * Constructor
	 */
	function ReservationsInstaller($values)
    {
    	parent :: __construct($values, ReservationsDataManager :: get_instance());
    }
    
	function get_path()
	{
		return dirname(__FILE__);
	}	
}
?>
