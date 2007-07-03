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
		$this->create_storage_unit(dirname(__FILE__).'/profiler_publication.xml');
	}
	function create_storage_unit($path)
	{
		$storage_unit_info = parent::parse_xml_file($path);
		echo '<pre>Creating Profiler Storage Unit: '.$storage_unit_info['name'].'</pre>';flush();
		$this->pdm->create_storage_unit($storage_unit_info['name'],$storage_unit_info['properties'],$storage_unit_info['indexes']);
	}
}
?>