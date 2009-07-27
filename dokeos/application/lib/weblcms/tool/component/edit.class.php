<?php

require_once dirname(__FILE__) . '/../../learning_object_publication_form.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';

class ToolEditComponent extends ToolComponent
{
	function run()
	{
		if($this->is_allowed(EDIT_RIGHT))
		{
			$pid = Request :: get(Tool :: PARAM_PUBLICATION_ID) ? Request :: get(Tool :: PARAM_PUBLICATION_ID) : $_POST[Tool :: PARAM_PUBLICATION_ID];

			$datamanager = WeblcmsDataManager :: get_instance();
			if(Request :: get('tool') == 'learning_path')
			{
				$learning_object = RepositoryDataManager :: get_instance()->retrieve_learning_object($pid);
			}
			else
			{
				$publication = $datamanager->retrieve_learning_object_publication($pid);
				$learning_object = $publication->get_learning_object(); //RepositoryDataManager :: get_instance()->retrieve_learning_object($publication->get_learning_object()->get_id());
			}

			$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_EDIT, $learning_object, 'edit', 'post', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, Tool :: PARAM_PUBLICATION_ID => $pid)));

			$trail = new BreadcrumbTrail();

			if(Request :: get('pcattree') > 0)
			{
				foreach(Tool ::get_pcattree_parents(Request :: get('pcattree')) as $breadcrumb)
				{
					$trail->add(new BreadCrumb($this->get_url(), $breadcrumb->get_name()));
				}
			}
			if(Request :: get('tool') == 'wiki')
			{
				$trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => 'view', 'display_action' => 'view', Tool :: PARAM_PUBLICATION_ID => $pid)), $learning_object->get_title()));
			}
			else
			{
				$trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => 'view', Tool :: PARAM_PUBLICATION_ID => $pid)), $learning_object->get_title()));
			}
			$trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => 'edit', Tool :: PARAM_PUBLICATION_ID => $pid)), Translation :: get('Edit')));
			$trail->add_help('courses general');

			if( $form->validate() || Request :: get('validated'))
			{
				if(!Request :: get('validated'))
				{
					$form->update_learning_object();
				}

				if($form->is_version())
				{
					$publication->set_learning_object($learning_object->get_latest_version());
					$publication->update();
				}

				$publication_form = new LearningObjectPublicationForm(LearningObjectPublicationForm :: TYPE_SINGLE, $learning_object,$this, false, $this->get_course(), false, array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, Tool :: PARAM_PUBLICATION_ID => $pid, 'validated' => 1));
				$publication_form->set_publication($publication);

				if( $publication_form->validate())
				{
					$publication_form->update_learning_object_publication();
					$message = htmlentities(Translation :: get('LearningObjectUpdated'));
					
					$show_details = Request :: get('details');
					$tool = Request :: get('tool');

					$params = array();
					if($show_details == 1)
					{
						$params['pid'] = $pid;
						$params['tool_action'] = 'view';
					}

					if($tool == 'learning_path')
					{
						$params['tool_action'] = 'view_clo';
						$params['display_action'] = 'view';
						$params['pid'] = Request :: get('pid');
					}
					
					if (!isset($show_details) && $tool != 'learning_path')
					{
						$filter = array('tool_action');
					}
					else
					{
						$filter = array();
					}

					$this->redirect($message, false, $params, $filter);
				}
				else
				{
					$this->display_header($trail, true);
					$publication_form->display();
					$this->display_footer();
				}
			}
			else
			{
				$this->display_header($trail, true);
				$form->display();
				$this->display_footer();
			}
		}
	}
}
?>