<?php
/**
 * $Id$
 * @package repository.repositorymanager
 *
 * @author Bart Mollet
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__) . '/metadata_component.class.php';

/**
 * Repository manager component to edit the metadata of a learning object.
 */
class RepositoryManagerMetadataEditorComponent extends RepositoryManagerMetadataComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail(false);
		$trail->add_help('repository metadata');

		$id = Request :: get(RepositoryManager :: PARAM_LEARNING_OBJECT_ID);
		if (isset($id) && is_numeric($id))
		{
			$object  = $this->retrieve_learning_object($id);
			
            $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager::PARAM_ACTION => RepositoryManager :: ACTION_VIEW_LEARNING_OBJECTS, RepositoryManager::PARAM_LEARNING_OBJECT_ID => $id)), $object->get_title()));
            $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager::PARAM_ACTION => RepositoryManager :: ACTION_EDIT_LEARNING_OBJECT_METADATA, RepositoryManager::PARAM_LEARNING_OBJECT_ID => $id)), Translation :: translate('Metadata')));
			
            $metadata_type = $this->get_metadata_type();
			
            $form   = null;
            $mapper = null;
            switch ($metadata_type) 
            {
                case self :: METADATA_FORMAT_LOM:
                                        
                    $mapper = new IeeeLomMapper($object);
                    $form = new MetadataLOMEditForm($id, $mapper, $this->get_url(array(RepositoryManager :: PARAM_LEARNING_OBJECT_ID => $id)), $this->get_catalogs());
                    break;
                
                /*
                 * Implementation of another Metadata type than LOM 
                 * could be done here
                 */
            }
            
            if(isset($form))
            {
                $form->build_editing_form();
                
                if($form->must_save())
                {
                    $this->display_header($trail, false, true);
                    
                    if(isset($mapper))
                    {
                        $this->render_action_bar($id);
                        
                        $result = $mapper->save_submitted_values($form->getSubmitValues());
                        if(!$result)
                        {
                            $this->display_error_message($mapper->get_errors_as_html());
                        }
                        else
                        {
                            $this->display_message(Translation :: translate('MetadataSaved'));
                        }
                        
                        $form->set_constant_values($mapper->get_constant_values(), true);
                        $form->display();
                    }
                    else
                    {
                        $this->display_error_message(Translation :: translate('MetadataMapperNotFound'));
                    }
                    
                    $this->display_footer();
                }
                else
                {
                    $this->display_header($trail, false, true);

                    $this->render_action_bar($id);
                    $form->display();
                    //$this->display_lom_xml($object, $ieeeLomDecorator, true);
                    
                    $this->display_footer();    
                }
            }
		}
		else
		{
			throw new Exception(Translation :: get('InvalidURLException'));
		}
	}
	

	/**
	 * Displays an action bar
	 */
	function render_action_bar($id)
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		$action_bar->add_common_action(new ToolbarItem('XML', Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array('go'=> RepositoryManager :: ACTION_VIEW_LEARNING_OBJECT_METADATA, RepositoryManager :: PARAM_LEARNING_OBJECT_ID => $id))));

		echo $action_bar->as_html();
	}
	
}
?>