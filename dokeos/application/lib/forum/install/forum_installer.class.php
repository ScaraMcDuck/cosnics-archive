<?php
/**
 * forum.install
 */

require_once dirname(__FILE__).'/../forum_data_manager.class.php';
require_once Path :: get_library_path().'installer.class.php';

/**
 * This installer can be used to create the storage structure for the
 * forum application.
 * @author Sven Vanpoucke & Michael Kyndt
 */
class ForumInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function ForumInstaller($values)
    {
    	parent :: __construct($values, ForumDataManager :: get_instance());
    }

	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>