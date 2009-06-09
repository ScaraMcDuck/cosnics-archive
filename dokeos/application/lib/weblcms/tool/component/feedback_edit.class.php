<?php

require_once dirname(__FILE__) . '/../../learning_object_publication_form.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';
require_once Path :: get_repository_path().'lib/learning_object_pub_feedback.class.php';

class ToolFeedbackEditComponent extends ToolComponent
{
	function run()
	{
        if($this->is_allowed(EDIT_RIGHT))
		{
			$cid = Request :: get(Tool :: PARAM_COMPLEX_ID) ? Request :: get(Tool :: PARAM_COMPLEX_ID) : $_POST[Tool :: PARAM_COMPLEX_ID];
            $pid = Request :: get(Tool :: PARAM_PUBLICATION_ID) ? Request :: get(Tool :: PARAM_PUBLICATION_ID) : $_POST[Tool :: PARAM_PUBLICATION_ID];
            $fid = Request :: get(LearningObjectPubFeedback :: PROPERTY_FEEDBACK_ID) ? Request :: get(LearningObjectPubFeedback :: PROPERTY_FEEDBACK_ID) : $_POST[LearningObjectPubFeedback :: PROPERTY_FEEDBACK_ID];

			$datamanager = RepositoryDataManager :: get_instance();
            $condition = new EqualityCondition(LearningObjectPubFeedback :: PROPERTY_FEEDBACK_ID, $fid);
            $feedbacks = $datamanager->retrieve_learning_object_pub_feedback($condition);
            while($feedback = $feedbacks->next_result())
            {
                $feedback_display = $datamanager->retrieve_learning_object($feedback->get_feedback_id());
                $form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_EDIT, $feedback_display, 'edit', 'post', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT_FEEDBACK,LearningObjectPubFeedback :: PROPERTY_FEEDBACK_ID => $fid, Tool :: PARAM_COMPLEX_ID => $cid, Tool :: PARAM_PUBLICATION_ID => $pid, 'details' => Request :: get('details'))));

                if( $form->validate() || Request :: get('validated'))
                {
                    $form->update_learning_object();
                    /*if($form->is_version())
                    {
                        $feedback_display->set_ref($learning_object->get_latest_version()->get_id());
                        $feedback_display->update();
                    }*/
                    $feedback_display->update();
                    $message = htmlentities(Translation :: get('LearningObjectFeedbackUpdated'));

                    $params = array();
                    if(Request :: get('pid')!=null)
                    {
                        $params['pid'] = Request :: get('pid');
                        $params['tool_action'] = 'view';
                    }
                    if(Request :: get('cid')!=null)
                    {
                        $params['cid'] = Request :: get('cid');
                        $params['tool_action'] = 'discuss';
                    }

                    if(Request :: get('fid')!=null)
                    {
                        $params['fid'] = Request :: get('fid');
                    }

                    if(Request :: get('details') == 1)
                    {
                        $params['cid'] = $cid;
                        $params['tool_action'] = 'discuss';
                    }

                    $this->redirect($message, '', $params);

                }
                else
                {
					$trail = new BreadcrumbTrail();
					$trail->add_help('courses general');

                    $this->display_header($trail, true);
                    $form->display();
                    $this->display_footer();
                }
            }
        }
	}
}
?>