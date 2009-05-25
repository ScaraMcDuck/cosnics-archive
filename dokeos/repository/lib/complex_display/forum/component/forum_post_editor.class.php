<?php

require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';

class ForumDisplayForumPostEditorComponent extends ForumDisplayComponent
{
	function run()
	{
		if($this->get_parent()->get_parent()->is_allowed(EDIT_RIGHT))
		{
			$cid = Request :: get('cid');
			$pid = Request :: get('pid');
			$post = Request :: get('post');

			if(!$pid || !$cid || !$post)
			{
				$this->display_header(new BreadCrumbTrail());
				$this->display_error_message(Translation :: get('ObjectNotSelected'));
				$this->display_footer();
			}

			$url = $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ForumDisplay::ACTION_EDIT_FORUM_POST,
										'cid' => $cid,
										'pid' => $pid, 'post' => $post));

			$datamanager = RepositoryDataManager :: get_instance();
			$cloi = $datamanager->retrieve_complex_learning_object_item($post);
			$learning_object = $datamanager->retrieve_learning_object($cloi->get_ref());

			$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_EDIT, $learning_object, 'edit', 'post', $url);

			if( $form->validate() || $_GET['validated'])
			{
				$form->update_learning_object();
				if($form->is_version())
				{
					$cloi->set_ref($learning_object->get_latest_version()->get_id());
					$cloi->update();
				}

				if($cloi->get_display_order() == 1)
				{
					$parent = $datamanager->retrieve_learning_object($cloi->get_parent());
					$parent->set_title($learning_object->get_title());
					$parent->update();
				}

				$message = htmlentities(Translation :: get('ForumPostUpdated'));

				$params = array();
				$params['pid'] = $pid;
				$params['cid'] = $cid;
                $params[ComplexDisplay::PARAM_DISPLAY_ACTION] = ForumDisplay::ACTION_VIEW_TOPIC;

				$this->redirect($message, '', $params);

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