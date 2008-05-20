<?php
/**
 * $Id:$
 * @package application.portfolio
 */
require_once Path :: get_library_path().'installer.class.php';
/**
 *	This installer can be used to create the storage structure for the
 *      search portal application.
 */
class SearchPortalInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function SearchPortalInstaller($values)
    {
    	parent :: __construct($values);
    }
    
	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>