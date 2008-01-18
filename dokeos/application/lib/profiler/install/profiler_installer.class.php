<?php
/**
 * @package application.lib.profiler.install
 */
require_once dirname(__FILE__).'/../profilerdatamanager.class.php';
require_once dirname(__FILE__).'/../../../../common/installer.class.php';
/**
 * This installer can be used to create the storage structure for the
 * profiler application.
 */
class ProfilerInstaller extends Installer {

	private $pdm;
	/**
	 * Constructor
	 */
    function ProfilerInstaller() {
    	$this->pdm = ProfilerDataManager :: get_instance();
    }
	/**
	 * Runs the install-script.
	 */
	function install()
	{
		echo '<div class="learning_object" style="padding: 15px 15px 15px 76px; background-image: url(../img/admin_profiler.gif);">';
		echo '<div class="title">'. get_lang('AppProfiler') .'</div>';
		echo '<div class="description">';
		$this->create_storage_unit(dirname(__FILE__).'/profiler_publication.xml');
		echo '<br /><span style="color: #008000; font-weight: bold;">'. get_lang('ApplicationSuccess') .'</span>';
		echo '</div>';
		echo '</div>';
	}
	function create_storage_unit($path)
	{
		$storage_unit_info = parent::parse_xml_file($path);
		echo 'Creating Profiler Storage Unit: '.$storage_unit_info['name'].'<br />';flush();
		$this->pdm->create_storage_unit($storage_unit_info['name'],$storage_unit_info['properties'],$storage_unit_info['indexes']);
	}
}
?>