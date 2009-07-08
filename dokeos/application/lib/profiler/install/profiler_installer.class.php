<?php
/**
 * @package application.lib.profiler.install
 */
require_once dirname(__FILE__) . '/../profiler_data_manager.class.php';
require_once Path :: get_library_path() . 'installer.class.php';
/**
 * This installer can be used to create the storage structure for the
 * profiler application.
 */
class ProfilerInstaller extends Installer
{

    /**
     * Constructor
     */
    function ProfilerInstaller($values)
    {
        parent :: __construct($values, ProfilerDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>