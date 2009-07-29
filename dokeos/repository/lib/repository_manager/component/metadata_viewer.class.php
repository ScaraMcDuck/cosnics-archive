<?php
require_once dirname(__FILE__) . '/metadata_component.class.php';

class RepositoryManagerMetadataViewerComponent extends RepositoryManagerMetadataComponent 
{
	function run() 
	{
	    $trail = new BreadcrumbTrail(false);
		$trail->add_help('repository metadata');

		$id = Request :: get(RepositoryManager :: PARAM_LEARNING_OBJECT_ID);
		if ($id)
		{
		    $object  = $this->retrieve_learning_object($id);
			
            $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager::PARAM_ACTION => RepositoryManager :: ACTION_VIEW_LEARNING_OBJECTS, RepositoryManager::PARAM_LEARNING_OBJECT_ID => $id)), $object->get_title()));
            $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager::PARAM_ACTION => RepositoryManager :: ACTION_EDIT_LEARNING_OBJECT_METADATA, RepositoryManager::PARAM_LEARNING_OBJECT_ID => $id)), Translation :: get('Metadata')));
			
            $metadata_type = $this->get_metadata_type();
            
            $mapper = null;
            $form   = null;
            switch ($metadata_type) 
            { 
            	 case self :: METADATA_FORMAT_LOM:
            		 $mapper = new IeeeLomMapper($object);
            		 $form = new MetadataLOMExportForm($object, $mapper);
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
	}
	
}
?>