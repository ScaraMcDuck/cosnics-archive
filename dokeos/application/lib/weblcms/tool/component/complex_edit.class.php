<?php

require_once dirname(__FILE__) . '/../../learning_object_publication_form.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';

class ToolComplexEditComponent extends ToolComponent
{
	function run()
	{
        if($this->is_allowed(EDIT_RIGHT))
		{
			$cid = isset($_GET[Tool :: PARAM_COMPLEX_ID]) ? $_GET[Tool :: PARAM_COMPLEX_ID] : $_POST[Tool :: PARAM_COMPLEX_ID];
			
			$datamanager = RepositoryDataManager :: get_instance();
			$cloi = $datamanager->retrieve_complex_learning_object_item($cid);
            $cloi->set_default_property('user_id',$this->get_user_id());
			$learning_object = $datamanager->retrieve_learning_object($cloi->get_ref());
			$learning_object->set_default_property('owner',$this->get_user_id());
			$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_EDIT, $learning_object, 'edit', 'post', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT_CLOI, Tool :: PARAM_COMPLEX_ID => $cid, Tool :: PARAM_PUBLICATION_ID => $_GET['pid'], 'details' => $_GET['details'])));
            
			if( $form->validate() || $_GET['validated'])
			{
				$form->update_learning_object();
				if($form->is_version())
				{	
					$cloi->set_ref($learning_object->get_latest_version()->get_id());
					$cloi->update();
				}
				
				$message = htmlentities(Translation :: get('LearningObjectUpdated'));
				
				$params = array();
				$params['pid'] = $_GET['pid'];
				$params['tool_action'] = 'view'; 
				
				if($_GET['details'] == 1)
				{
					$params['cid'] = $cid;
					$params['tool_action'] = 'view_item'; 
				}
				
				$this->redirect(null, $message, '', $params);

			}
			else
			{
				$this->display_header(new BreadCrumbTrail());
				$form->display();
				$this->display_footer();
			}

		}
	}

}
?>