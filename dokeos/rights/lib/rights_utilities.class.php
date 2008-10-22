<?php
require_once Path :: get_rights_path() . 'lib/rights_data_manager.class.php';
require_once Path :: get_rights_path() . 'lib/location.class.php';
require_once Path :: get_library_path() . 'configuration/configuration.class.php';
require_once 'Tree/Tree.php';

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
		$configuration = Configuration :: get_instance();
		$dsn = $configuration->get_parameter('database', 'connection_string');
    	
		$config = array(
		    'type' => 'Nested',
		    'storage' => array(
		        'name' => 'MDB2',
		        'dsn' => $dsn
		        ,
		        // 'connection' =>
		    ),
		    'options' => array(
		        'table' => 'rights_location',
		        'order' =>  'id',
		        'fields' => array(
		            'id' => array('type' => 'integer', 'name' => 'id'),
		            'name' => array('type' => 'text', 'name' => 'location'),
		            'left'      =>  array('type' => 'text', 'name' => 'left_value'),
		            'right'     =>  array('type' => 'text', 'name' => 'right_value'),
		            'parent_id'  =>  array('type' => 'integer', 'name' => 'parent')
		        ),
		    ),
		);
		
		$tree = Tree :: factoryDynamic($config);
		
		$tree->add( array("name"=>"c0"));
		
		return true;
    }
}
?>