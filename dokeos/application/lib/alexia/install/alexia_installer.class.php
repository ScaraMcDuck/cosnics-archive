<?php
/**
 * @package alexia
 * @subpackage install
 */

require_once dirname(__FILE__) . '/../alexia_data_manager.class.php';
require_once Path :: get_library_path() . 'installer.class.php';

/**
 * This installer can be used to create the storage structure for the
 * Alexia application.
 * @author Hans De Bisschop
 */
class AlexiaInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function AlexiaInstaller($values)
    {
    	parent :: __construct($values, AlexiaDataManager :: get_instance());
    }

	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>