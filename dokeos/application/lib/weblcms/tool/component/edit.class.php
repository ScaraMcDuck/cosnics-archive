<?php

require_once dirname(__FILE__) . '/../../learning_object_publication_form.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';

class ToolEditComponent extends ToolComponent
{
	function run()
	{
		if($this->is_allowed(EDIT_RIGHT))
		{
			$pid = isset($_GET[Tool :: PARAM_PUBLICATION_ID]) ? $_GET[Tool :: PARAM_PUBLICATION_ID] : $_POST[Tool :: PARAM_PUBLICATION_ID];
            
                $datamanager = WeblcmsDataManager :: get_instance();
                $publication = $datamanager->retrieve_learning_object_publication($pid);

                $learning_object = $publication->get_learning_object(); //RepositoryDataManager :: get_instance()->retrieve_learning_object($publication->get_learning_object()->get_id());
                $form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_EDIT, $learning_object, 'edit', 'post', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, Tool :: PARAM_PUBLICATION_ID => $pid)));
                
                if( $form->validate() || $_GET['validated'])
                {
                    if(!$_GET['validated'])
                        $form->update_learning_object();

                    if($form->is_version())
                    {
                        $publication->set_learning_object($learning_object->get_latest_version());
                        $publication->update();
                    }

                    $publication_form = new LearningObjectPublicationForm(LearningObjectPublicationForm :: TYPE_SINGLE, $publication->get_learning_object(),$this, false, $this->get_course(), false, array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, Tool :: PARAM_PUBLICATION_ID => $pid, 'validated' => 1));
                    $publication_form->set_publication($publication);

                    if( $publication_form->validate())
                    {
                        $publication_form->update_learning_object_publication();
                        $message = htmlentities(Translation :: get('LearningObjectUpdated'));

                        $params = array();
                        if($_GET['details'] == 1)
                        {
                            $params['pid'] = $pid;
                            $params['tool_action'] = 'view';
                        }

                        $this->redirect(null, $message, '', $params);
                    }
                    else
                    {
                        $this->display_header(new BreadCrumbTrail());
                        $publication_form->display();
                        $this->display_footer();
                    }
                }
                else
                {
                    $this->display_header(new BreadCrumbTrail());
                    $form->display();
                    $this->display_footer();
                }
            }
			/**/
		}
	

}
?>