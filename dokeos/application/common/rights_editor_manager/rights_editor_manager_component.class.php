<?php
require_once Path :: get_common_path() . 'sub_manager_component.class.php';

class RightsEditorManagerComponent extends SubManagerComponent
{
	function get_location()
    {
    	return $this->get_parent()->get_location();
    }
    
	function get_available_rights()
	{
		return $this->get_parent()->get_available_rights();
	}
}
?>