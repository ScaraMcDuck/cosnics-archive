<?php
/**
 * @package application.lib.persnal_messenger.installer
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../personalmessengerdatamanager.class.php';
require_once dirname(__FILE__).'/../../../../common/installer.class.php';
/**
 *	This installer can be used to create the storage structure for the
 * personal messenger application.
 */
class PersonalMessengerInstaller extends Installer {

	private $pmdm;
	/**
	 * Constructor
	 */
    function PersonalMessengerInstaller() {
    	$this->pmdm = PersonalMessengerDataManager :: get_instance();
    }
	/**
	 * Runs the install-script.
	 */
	function install()
	{
		$this->create_storage_unit(dirname(__FILE__).'/personal_messenger_publication.xml');
	}

	function create_storage_unit($path)
	{
		$storage_unit_info = parent::parse_xml_file($path);
		echo '<pre>Creating PersonalMessenger Storage Unit: '.$storage_unit_info['name'].'</pre>';flush();
		$this->pmdm->create_storage_unit($storage_unit_info['name'],$storage_unit_info['properties'],$storage_unit_info['indexes']);
	}
}
?>