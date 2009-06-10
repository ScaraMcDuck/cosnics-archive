<?php
/**
 */

require_once Path :: get_repository_path() . 'lib/complex_learning_object_item_form.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';

class ComplexDisplayUpdaterComponent extends ComplexDisplayComponent
{
	function run()
	{
        if($this->is_allowed(EDIT_RIGHT))
		{
			$cid = Request :: get('cid') ? Request :: get('cid') : $_POST['cid'];
            $pid = Request :: get('pid') ? Request :: get('pid') : $_POST['pid'];
			$selected_cloi = Request :: get('selected_cloi') ? Request :: get('selected_cloi') : $_POST['selected_cloi'];
            
			$datamanager = RepositoryDataManager :: get_instance();
			$cloi = $datamanager->retrieve_complex_learning_object_item($selected_cloi);

 			$cloi->set_default_property('user_id',$this->get_user_id());
            $learning_object = $datamanager->retrieve_learning_object($cloi->get_ref());
            $learning_object->set_default_property('owner',$this->get_user_id());
            $form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_EDIT, $learning_object, 'edit', 'post', $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_UPDATE, 'selected_cloi' => $selected_cloi, 'cid' => $cid, 'pid' => $pid)));


            if( $form->validate() || Request :: get('validated'))
            {
                $form->update_learning_object();
                if($form->is_version())
                {
                	$old_id = $cloi->get_ref();
					$new_id = $learning_object->get_latest_version()->get_id();
					$cloi->set_ref($new_id);
					$cloi->update();
					
					$children = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items(new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $old_id));
					while($child = $children->next_result())
					{
						$child->set_parent($new_id);
						$child->update();
					}
                }

                $message = htmlentities(Translation :: get('LearningObjectUpdated'));

                $params = array();
                $params['pid'] = Request :: get('pid');
                $params['cid'] = Request :: get('cid');
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
