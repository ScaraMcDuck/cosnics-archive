<?php
/**
 * @package application.lib.persnal_messenger.installer
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../personal_messenger_data_manager.class.php';
require_once Path :: get_library_path().'installer.class.php';
/**
 *	This installer can be used to create the storage structure for the
 * personal messenger application.
 */
class PersonalMessengerInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function PersonalMessengerInstaller($values)
    {
    	parent :: __construct($values, PersonalMessengerDataManager :: get_instance());
    }
	
	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>