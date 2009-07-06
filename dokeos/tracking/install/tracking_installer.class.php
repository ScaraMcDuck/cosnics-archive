<?php
/**
 * @package contentboxes.install
 */
require_once dirname(__FILE__) . '/../lib/tracking_data_manager.class.php';
require_once Path :: get_library_path() . 'installer.class.php';
require_once Path :: get_tracking_path() . 'lib/tracker_registration.class.php';
require_once Path :: get_tracking_path() . 'lib/event_rel_tracker.class.php';
require_once Path :: get_library_path() . 'filesystem/filesystem.class.php';
/**
 * This	 installer can be used to create the contentboxes structure
 */
class TrackingInstaller extends Installer
{

    /**
     * Constructor
     */
    function TrackingInstaller($values)
    {
        parent :: __construct($values, TrackingDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>