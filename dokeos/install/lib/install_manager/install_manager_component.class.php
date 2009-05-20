<?php

require_once Path :: get_library_path() . 'core_application_component.class.php';

abstract class InstallManagerComponent extends CoreApplicationComponent  
{
	/**
	 * Constructor
	 * @param InstallManager $install_manager The install manager which
	 * provides this component
	 */
	protected function InstallManagerComponent($install_manager) 
	{
		parent :: __construct($install_manager);
	}
}
?>