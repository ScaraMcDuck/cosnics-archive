<?php
require_once dirname(__FILE__) . '/metadata_component.class.php';
require_once dirname(__FILE__) . '/../../external_export.class.php';

class RepositoryManagerExternalRepositoryExportComponent extends RepositoryManagerMetadataComponent 
{
    const PARAM_EXPORT_ID = 'ext_rep_id';
    
    private $already_required_types = array();
    
    
    function get_catalogs()
	{
	     $catalogs = array();
	     
	     $catalogs[ExternalExport :: CATALOG_EXPORT_LIST]  = ExternalExport :: retrieve_external_export();
	     
	     return $catalogs;
	}
	
	/**
	 * Check wether a learning object can be retrieved by using the URL params
	 * @return boolean
	 */
	function check_learning_object_from_params()
	{
	    $learning_object = $this->get_learning_object_from_params();
	    if(isset($learning_object))
	    {
	        $this->check_user_can_access_learning_object($learning_object, true);
	        
	        return true;
	    }
	    else
	    {
	        return false;
	    }
	}
	
	function get_learning_object_from_params()
	{
		/*
	     * Check if the learning object is given in the URL params  
	     */
	    $lo_id = Request :: get(RepositoryManager :: PARAM_LEARNING_OBJECT_ID);
	    
	    if(isset($lo_id) && is_numeric($lo_id))
	    {
	        /*
	         * Check if the learning object does exist 
	         */
	        $dm = RepositoryDataManager :: get_instance();
	        return $dm->retrieve_learning_object($lo_id);
	    }
	    else
	    {
	        return null;
	    }
	}
	
	function get_external_export_from_param()
	{
	    $export_id = Request :: get(self :: PARAM_EXPORT_ID);
	    
	    if(isset($export_id) && strlen($export_id) > 0)
	    {
	        $export = new ExternalExport();
	        $export->set_id($export_id);
	        $export = $export->get_typed_export_object();
	        
	        return $export;
	    }
	    else
	    {
	        return null;
	    }
	}
	
	
	/**
	 * Check if a user has the right to export the learning object to an external repository
	 * 
	 * @param $learning_object LearningObject
	 * @param $with_error_display boolean Indicates wether the 'not allowed' form must be displayed when a user doesn't have the required access rights
	 * @return boolean
	 */
	protected function check_user_can_access_learning_object($learning_object, $with_error_display = false)
	{
	    if ($learning_object->get_owner_id() != $this->get_user_id() && !$this->get_parent()->has_right($learning_object, $this->get_user_id(), RepositoryRights :: REUSE_RIGHT))
		{
		    if($with_error_display)
		    {
		        $trail = new BreadcrumbTrail(false);
    		    $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager::PARAM_ACTION => RepositoryManager :: ACTION_VIEW_LEARNING_OBJECTS, RepositoryManager::PARAM_LEARNING_OBJECT_ID => $learning_object->get_id())), $learning_object->get_title()));
    		        
		        $this->not_allowed($trail);
		    }
		    
			return false;
		}
		else
		{
		    return true;
		}
	}
}

?>