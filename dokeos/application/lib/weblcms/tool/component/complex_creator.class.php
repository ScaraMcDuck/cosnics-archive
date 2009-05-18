<?php

require_once dirname(__FILE__) . '/../../learning_object_publication_form.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';
require_once Path :: get_repository_path() . 'lib/complex_learning_object_item_form.class.php';
require_once dirname(__FILE__).'/../../learning_object_repo_viewer.class.php';

class ToolComplexCreatorComponent extends ToolComponent
{
	function run()
	{
		if($this->is_allowed(ADD_RIGHT))
		{
			$pid = Request :: get('pid');
			if(!$pid)
			{
				$this->display_header(new BreadCrumbTrail());
				$this->display_error_message(Translation :: get('NoParentSelected'));
				$this->display_footer();
			}

			$type = Request :: get('type');

			$pub = new LearningObjectRepoViewer($this, $type, true);
			$pub->set_parameter(Tool :: PARAM_ACTION, Tool :: ACTION_CREATE_CLOI);
			$pub->set_parameter('pid', $pid);
			$pub->set_parameter('type', $type);

			$object_id = Request :: get('object');

			if(!isset($object_id))
			{
				$html[] = '<p><a href="' . $this->get_url(array('type' => $type, 'pid' => $pid)) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
				$html[] =  $pub->as_html();
				$this->display_header(new BreadCrumbTrail());
				echo implode("\n",$html);
				$this->display_footer();
			}
			else
			{
				$cloi = ComplexLearningObjectItem :: factory($type);

				$cloi->set_ref($object_id);
				$cloi->set_user_id($this->get_user_id());
				$cloi->set_parent($pid);
				$cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($pid));

				$cloi_form = ComplexLearningObjectItemForm :: factory(ComplexLearningObjectItemForm :: TYPE_CREATE, $cloi, 'create_complex', 'post', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_CREATE_CLOI, 'object' => $object_id)));

				if($cloi_form)
				{
					if ($cloi_form->validate() || !$cloi->is_extended())
					{
						$cloi_form->create_complex_learning_object_item();
						$this->my_redirect($pid);
					}
					else
					{
						$this->display_header(new BreadCrumbTrail());
						$cloi_form->display();
						$this->display_footer();
					}
				}
				else
				{
					$cloi->create();
					$this->my_redirect($pid);
				}
			}

		}
	}

	private function my_redirect($pid)
	{
		$message = htmlentities(Translation :: get('LearningObjectCreated'));

		$params = array();
		$params['pid'] = $pid;
		$params['tool_action'] = 'view';

		$this->redirect($message, '', $params);
	}

}
?>