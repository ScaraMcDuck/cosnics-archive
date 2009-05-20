<?php

require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';
require_once Path :: get_repository_path() . 'lib/complex_learning_object_item_form.class.php';
require_once dirname(__FILE__).'/../../../learning_object_repo_viewer.class.php';

class ForumToolSubforumEditorComponent extends ForumToolComponent
{
	function run()
	{
		if($this->is_allowed(EDIT_RIGHT))
		{
			$pid = Request :: get('pid');
			$subforum = Request :: get('subforum');
			$forum = Request :: get('forum');
			$is_subforum = Request :: get('is_subforum');

			if(!$pid || !$subforum)
			{
				$this->display_header(new BreadCrumbTrail(), true, 'courses forum tool');
				$this->display_error_message(Translation :: get('NoParentSelected'));
				$this->display_footer();
			}

			$url = $this->get_url(array(Tool :: PARAM_ACTION => ForumTool :: ACTION_EDIT_SUBFORUM,
										Tool :: PARAM_PUBLICATION_ID => $pid, 'subforum' => $subforum, 'is_subforum' => $is_subforum, 'forum' => $forum));

			$datamanager = RepositoryDataManager :: get_instance();
			$cloi = $datamanager->retrieve_complex_learning_object_item($subforum);
			$learning_object = $datamanager->retrieve_learning_object($cloi->get_ref());

			$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_EDIT, $learning_object, 'edit', 'post', $url);

			if( $form->validate())
			{
				$form->update_learning_object();
				if($form->is_version())
				{
					$cloi->set_ref($learning_object->get_latest_version()->get_id());
					$cloi->update();
				}

				$this->my_redirect($pid, $is_subforum, $forum);
			}
			else
			{
				$this->display_header(new BreadCrumbTrail(), true, 'courses forum tool');
				$form->display();
				$this->display_footer();
			}
		}
	}

	private function my_redirect($pid, $is_subforum, $forum)
	{
		$message = htmlentities(Translation :: get('SubforumCreated'));

		$params = array();
		$params['pid'] = $pid;
		$params['tool_action'] = 'view';
		if($is_subforum)
			$params['forum'] = $forum;

		$this->redirect($message, '', $params);
	}

}
?>