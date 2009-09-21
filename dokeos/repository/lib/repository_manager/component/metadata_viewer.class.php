<?php
require_once dirname(__FILE__) . '/metadata_component.class.php';

class RepositoryManagerMetadataViewerComponent extends RepositoryManagerMetadataComponent 
{
	function run() 
	{
	    $trail = new BreadcrumbTrail(false);
		$trail->add_help('repository metadata');

		if($this->check_learning_object_from_params())
		{
			$learning_object = $this->get_learning_object_from_params(); 
		
            $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager::PARAM_ACTION => RepositoryManager :: ACTION_VIEW_LEARNING_OBJECTS, RepositoryManager::PARAM_LEARNING_OBJECT_ID => $id)), $learning_object->get_title()));
            $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager::PARAM_ACTION => RepositoryManager :: ACTION_EDIT_LEARNING_OBJECT_METADATA, RepositoryManager::PARAM_LEARNING_OBJECT_ID => $id)), Translation :: get('Metadata')));
			
            $metadata_type = $this->get_metadata_type();
            
            $mapper = null;
            $form   = null;
            switch ($metadata_type) 
            { 
            	 case self :: METADATA_FORMAT_LOM:
            		 $mapper = new IeeeLomMapper($learning_object);
            		 $form = new MetadataLOMExportForm($learning_object, $mapper);
            		 break;
            		 
             	/*
                 * Implementation of another Metadata type than LOM 
                 * could be done here
                 */
            }
            
            if(isset($form))
            {
                $form->display_metadata();
            }
		}
		else
		{
		    throw new Exception(Translation :: get('InvalidURLException'));
		}
	}
	
}
?>