<?php
require_once Path :: get_rights_path() . 'lib/rights_utilities.class.php';

class WebserviceRights
{
	const VIEW_RIGHT	= '1';
	const ADD_RIGHT		= '2';
	const EDIT_RIGHT	= '3';
	const DELETE_RIGHT	= '4';
	
	function get_available_rights()
	{
	    $reflect = new ReflectionClass('WebserviceRights');
	    return $reflect->getConstants();
	}
	
	function is_allowed($right, $location, $type)
	{
		return RightsUtilities :: is_allowed($right, $location, $type, 'admin');
	}
}
?>