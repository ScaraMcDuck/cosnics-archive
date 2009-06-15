<?php
/**
 * webconferencing.install
 */

require_once dirname(__FILE__).'/../webconferencing_data_manager.class.php';
require_once Path :: get_library_path().'installer.class.php';

/**
 * This installer can be used to create the storage structure for the
 * webconferencing application.
 * @author Stefaan Vanbillemont
 */
class WebconferencingInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function WebconferencingInstaller($values)
    {
    	parent :: __construct($values, WebconferencingDataManager :: get_instance());
    }

	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>