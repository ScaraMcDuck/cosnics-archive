<?php
require_once dirname(__FILE__) . '/external_repository_export_component.class.php';
require_once dirname(__FILE__) . '/../../forms/external_export_export_form.class.php';
require_once dirname(__FILE__) . '/../../export/external_export/base_external_exporter.class.php';

class RepositoryManagerExternalRepositoryExportExportComponent extends RepositoryManagerExternalRepositoryExportComponent 
{
    const PARAM_FORCE_EXPORT = 'force_export';
    
	function run() 
	{
	    if($this->check_learning_object_from_params())
		{
		    $export = $this->get_external_export_from_param();
		    if(isset($export) && $export->get_enabled() == 1)
		    {    
		        try
        	    {
        	        $learning_object = $this->get_learning_object_from_params(); 
    		        
    		        $trail = new BreadcrumbTrail(false);
    		        $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager::PARAM_ACTION => RepositoryManager :: ACTION_VIEW_LEARNING_OBJECTS, RepositoryManager::PARAM_LEARNING_OBJECT_ID => $learning_object->get_id())), $learning_object->get_title()));
    		        $trail->add(new BreadCrumb($this->get_url(array(RepositoryManager::PARAM_ACTION => RepositoryManager :: ACTION_EXTERNAL_REPOSITORY_BROWSE, RepositoryManager :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id())), Translation :: translate('ExternalExport')));
            	    $trail->add(new BreadCrumb(null, $export->get_title()));
    		        
            	    //do not put display_header(...) here, as it would block an eventual redirection made by the ->export() method
            	    //$this->display_header($trail, false, true);
            	   
            	    $form = ExternalExportExportForm :: get_instance($learning_object, $export, $this->get_url(array(parent :: PARAM_EXPORT_ID => $export->get_id(), RepositoryManager :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id())), null);
            	    
            	    $force_export = Request :: get(self :: PARAM_FORCE_EXPORT);
            	    if(!$form->isSubmitted() && !isset($force_export))
            	    {
            	        $this->display_header($trail, false, true);
            	        $form->display();
            	    }
            	    else
            	    {
            	        if($form->validate())
            	        {
                	        $exporter = BaseExternalExporter :: get_instance($export);
                	        
                	        $this->display_header($trail, false, true);
                	        
                	        if($exporter->export($learning_object))
                	        {
                	            
                	            $repository_uid = $exporter->get_existing_repository_uid($learning_object);
                	            
                	            $form->display_export_success($repository_uid);
                	        }
            	        }
            	        else
            	        {
            	            $this->display_header($trail, false, true);
            	            $form->display();
            	        }
            	    }
        	    }
        	    catch(Exception $ex)
        	    {
        	        $this->display_error_message($ex->getMessage());
        	    }
        	    
        	    $this->display_footer();
		    }
		    else
		    {
		        throw new Exception('The external export is undefined');
		    }
		}
		else
		{
		    throw new Exception('The object to export is undefined');
		}   
	}
	
	
}
?>