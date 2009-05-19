<?php
require_once Path :: get_library_path() . 'application_component.class.php';

class CoreApplicationComponent extends ApplicationComponent
{
	/**
	 * The CoreApplicationComponent constructor
	 * @see ApplicationComponent :: __construct()
	 */
    function CoreApplicationComponent($manager)
    {
        parent :: __construct($manager);
    }
}
?>