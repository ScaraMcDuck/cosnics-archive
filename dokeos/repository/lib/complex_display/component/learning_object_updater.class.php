<?php
/**
 */

require_once Path :: get_repository_path() . 'lib/complex_learning_object_item_form.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';

class ComplexDisplayLearningObjectUpdaterComponent extends ComplexDisplayComponent
{
	function run()
	{
        if($this->is_allowed(EDIT_RIGHT))
		{
			$pid = Request :: get('pid') ? Request :: get('pid') : $_POST['pid'];

			$datamanager = RepositoryDataManager :: get_instance();
			$learning_object = $datamanager->retrieve_learning_object($pid);

 			$learning_object->set_default_property('owner',$this->get_user_id());
            $form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_EDIT, $learning_object, 'edit', 'post', $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_UPDATE_LO, 'selected_cloi' => $selected_cloi, 'selected_cloi' => $cid, 'pid' => $pid)));


            if( $form->validate() || Request :: get('validated'))
            {
                $form->update_learning_object();
                

                $message = htmlentities(Translation :: get('LearningObjectUpdated'));

                $params = array();
                $params['pid'] = Request :: get('pid');
                $params['tool_action'] = Request :: get('tool_action');
                $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ComplexDisplay :: ACTION_VIEW_CLO;

                $this->redirect($message, '', $params);

            }
            else
            {
                $form->display();
            }
        }
	}
}
?>
