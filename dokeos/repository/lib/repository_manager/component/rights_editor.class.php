<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once Path :: get_application_library_path() . 'rights_editor_manager/rights_editor_manager.class.php';

/**
 * Repository manager component to edit the rights for the learning objects in
 * the repository.
 */
class RepositoryManagerRightsEditorComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$object = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
		$location = RepositoryRights :: get_location_by_identifier('content_object', $object);
		
		$manager = new RightsEditorManager($this, $location);
		$manager->run();
	}
	
	function get_available_rights()
	{
		$array = RepositoryRights :: get_available_rights();
		unset($array['ADD_RIGHT']);
		unset($array['EDIT_RIGHT']);
		unset($array['DELETE_RIGHT']);

		return $array;
	}
	
	function display_header($trail)
	{
		$this->get_parent()->display_header($trail, false);
	}
	
	function get_url($parameters)
	{
		$parameters[RepositoryManager :: PARAM_CONTENT_OBJECT_ID] = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
		
		return parent :: get_url($parameters);
	}
	
	function get_parameters()
	{
		$parameters = parent :: get_parameters();
		$parameters[RepositoryManager :: PARAM_CONTENT_OBJECT_ID] = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
		return $parameters;
	}

}
?>