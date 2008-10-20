<?php
require_once Path :: get_rights_path() . 'lib/rights_data_manager.class.php';
require_once Path :: get_rights_path() . 'lib/location.class.php';

/*
 * This should become the class which all applications use
 * to retrieve and add rights. This class should NOT be used by the
 * RightsManager itself. Its is meant to be be used as an interface
 * to the RightsManager / RightsDataManager functionality.
 */

class RightsUtilities
{

    function RightsUtilities()
    {
    }
    
    function create_application_root_location($application)
    {
		$location = new Location();
		
		$location->set_location($application);
		$location->set_application($application);
		$location->set_type('root');
		$location->set_identifier('0');
		$location->set_left_value('1');
		$location->set_right_value('2');
		$location->set_parent('0');
		
		if ($location->create())
		{
			return true;
		}
		else
		{
			return false;
		}
    }
}
?>