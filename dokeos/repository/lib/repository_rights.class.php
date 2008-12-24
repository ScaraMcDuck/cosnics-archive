<?php
require_once Path :: get_rights_path() . 'lib/rights_utilities.class.php';
require_once Path :: get_repository_path() . 'lib/repository_manager/repository_manager.class.php';

class RepositoryRights
{
	const VIEW_RIGHT	= '1';
	const ADD_RIGHT		= '2';
	const EDIT_RIGHT	= '3';
	const DELETE_RIGHT	= '4';
	
	function get_available_rights()
	{
	    $reflect = new ReflectionClass('RepositoryRights');
	    return $reflect->getConstants();
	}
	
	function is_allowed($right, $location, $type)
	{
		return RightsUtilities :: is_allowed($right, $location, $type, RepositoryManager :: APPLICATION_NAME);
	}
	
	function get_location_by_identifier($type, $identifier)
	{
		return RightsUtilities :: get_location_by_identifier(RepositoryManager :: APPLICATION_NAME, $type, $identifier);
	}
	
	function get_location_id_by_identifier($type, $identifier)
	{
		return RightsUtilities :: get_location_id_by_identifier(RepositoryManager :: APPLICATION_NAME, $type, $identifier);
	}
	
	function get_root_id()
	{
		return RightsUtilities :: get_root_id(RepositoryManager :: APPLICATION_NAME);
	}
	
	function get_root()
	{
		return RightsUtilities :: get_root(RepositoryManager :: APPLICATION_NAME);
	}
}
?>