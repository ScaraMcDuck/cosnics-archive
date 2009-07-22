<?php
/**
 * $Id: alexia_manager.class.php 21979 2009-07-10 20:51:57Z Scara84 $
 * @package application.alexia
 */
require_once dirname(__FILE__).'/../../web_application.class.php';
require_once Path :: get_library_path().'configuration/configuration.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once dirname(__FILE__).'/alexia_manager_component.class.php';
//require_once dirname(__FILE__).'/../connector/alexia_weblcms_connector.class.php';
require_once dirname(__FILE__).'/../alexia_data_manager.class.php';
//require_once dirname(__FILE__).'/../alexia_block.class.php';
/**
 * This application gives each user the possibility to maintain a personal
 * calendar.
 */
class AlexiaManager extends WebApplication
{
	const APPLICATION_NAME = 'alexia';
	
	const PARAM_DELETE_SELECTED = 'delete_selected';
	const PARAM_ALEXIA_ID = 'publication';

	const ACTION_BROWSE_PUBLICATIONS = 'browse';
	const ACTION_CREATE_PUBLICATION = 'publish';
	const ACTION_VIEW_PUBLICATION = 'view';

	/**
	 * Constructor
	 * @param int $user_id
	 */
	public function AlexiaManager($user)
	{
		parent :: __construct($user);
	}
	/**
	 * Runs the personal calendar application
	 */
	public function run()
	{
		$action = $this->get_action();

		switch ($action)
		{
			case self :: ACTION_BROWSE_PUBLICATIONS :
				$component = AlexiaManagerComponent :: factory('Browser', $this);
				break;
			case self :: ACTION_CREATE_PUBLICATION :
				$component = AlexiaManagerComponent :: factory('Publisher', $this);
				break;
			default :
				$this->set_action(self :: ACTION_BROWSE_PUBLICATIONS);
				$component = AlexiaManagerComponent :: factory('Browser', $this);
		}
		$component->run();
	}
	
    /**
     * Gets the url for viewing a profile publication
     * @param ProfilePublication
     * @return string The url
     */
    function get_publication_viewing_url($alexia_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_PUBLICATION, self :: PARAM_ALEXIA_ID => $alexia_publication->get_id()));
    }
	
    function count_alexia_publications($condition = null)
    {
        $adm = AlexiaDataManager :: get_instance();
        return $adm->count_alexia_publications($condition);
    }
    
    function retrieve_alexia_publication($id)
    {
        $adm = AlexiaDataManager :: get_instance();
        return $adm->retrieve_alexia_publication($id);
    }
    
    function retrieve_alexia_publications($condition = null, $order_by = array (), $order_dir = array (), $offset = 0, $max_objects = -1)
    {
        $adm = AlexiaDataManager :: get_instance();
        return $adm->retrieve_alexia_publications($condition, $offset, $max_objects, $order_by, $order_dir);
    }

	/**
	 * Helper function for the Application class,
	 * pending access to class constants via variables in PHP 5.3
	 * e.g. $name = $class :: APPLICATION_NAME
	 *
	 * DO NOT USE IN THIS APPLICATION'S CONTEXT
	 * Instead use:
	 * - self :: APPLICATION_NAME in the context of this class
	 * - YourApplicationManager :: APPLICATION_NAME in all other application classes
	 */
	function get_application_name()
	{
		return self :: APPLICATION_NAME;
	}
}
?>