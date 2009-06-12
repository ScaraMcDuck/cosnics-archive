<?php
/**
 * wiki.install
 */

require_once dirname(__FILE__).'/../wiki_data_manager.class.php';
require_once Path :: get_library_path().'installer.class.php';

/**
 * This installer can be used to create the storage structure for the
 * wiki application.
 * @author Sven Vanpoucke & Stefan Billiet
 */
class WikiInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function WikiInstaller($values)
    {
    	parent :: __construct($values, WikiDataManager :: get_instance());
    }

	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>