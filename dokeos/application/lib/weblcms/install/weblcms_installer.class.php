<?php
/**
 * $Id: repositorydatamanager.class.php 9176 2006-08-30 09:08:17Z bmol $
 * @package application.weblcms
 */
/**
 *	This installer can be used to create the storage structure for the
 * weblcms application.
 */
class WeblcmsInstaller {
	/**
	 * Constructor
	 */
    function WeblcmsInstaller() {
    }
	/**
	 * Runs the install-script.
	 * @todo This function now uses the function of the RepositoryInstaller
	 * class. These shared functions should be available in a common base class.
	 */
	function install()
	{
		$repository_installer = new RepositoryInstaller();
		$repository_installer->parse_xml_file(dirname(__FILE__).'/learning_object_publication.xml');
		$repository_installer->parse_xml_file(dirname(__FILE__).'/learning_object_publication_category.xml');
		$repository_installer->parse_xml_file(dirname(__FILE__).'/learning_object_publication_group.xml');
		$repository_installer->parse_xml_file(dirname(__FILE__).'/learning_object_publication_user.xml');
	}
}
?>