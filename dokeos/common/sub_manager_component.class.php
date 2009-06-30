<?php
require_once Path :: get_library_path() . 'application_component.class.php';

class SubManagerComponent extends ApplicationComponent
{
	/**
	 * The SubManagerComponent constructor
	 * @see ApplicationComponent :: __construct()
	 */
    function SubManagerComponent($manager)
    {
        parent :: __construct($manager);
    }
}
?>