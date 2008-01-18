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
		echo '<div class="learning_object" style="padding: 15px 15px 15px 76px; background-image: url(../img/admin_personal_messenger.gif);">';
		echo '<div class="title">'. get_lang('AppPersonalMessenger') .'</div>';
		echo '<div class="description">';
		$this->create_storage_unit(dirname(__FILE__).'/personal_messenger_publication.xml');
		echo '<br /><span style="color: #008000; font-weight: bold;">'. get_lang('ApplicationSuccess') .'</span>';
		echo '</div>';
		echo '</div>';
	}

	function create_storage_unit($path)
	{
		$storage_unit_info = parent::parse_xml_file($path);
		echo 'Creating Personal Messenger Storage Unit: '.$storage_unit_info['name'].'<br />';flush();
		$this->pmdm->create_storage_unit($storage_unit_info['name'],$storage_unit_info['properties'],$storage_unit_info['indexes']);
	}
}
?>