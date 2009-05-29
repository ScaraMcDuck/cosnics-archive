<?php

require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';
require_once Path :: get_repository_path() . 'lib/complex_learning_object_item_form.class.php';
require_once Path :: get_application_path() . 'common/repo_viewer/repo_viewer.class.php';

class ForumDisplayForumPostCreatorComponent extends ForumDisplayComponent
{
	function run()
	{
		if($this->get_parent()->get_parent()->is_allowed(ADD_RIGHT))
		{
			$pid = Request :: get('pid');
			$cid = Request :: get('cid');
			$reply = Request :: get('reply');

			if(!$pid || !$cid)
			{
				$this->display_header(new BreadCrumbTrail());
				$this->display_error_message(Translation :: get('NoParentSelected'));
				$this->display_footer();
			}

			$rdm = RepositoryDataManager :: get_instance();

			if($reply)
			{
				$reply_item = $rdm->retrieve_complex_learning_object_item($reply);
				$reply_lo = $rdm->retrieve_learning_object($reply_item->get_ref(), 'forum_post');
			}

			$pub = new RepoViewer($this, 'forum_post', true);
            $pub->set_parameter(ComplexDisplay :: PARAM_DISPLAY_ACTION, ForumDisplay :: ACTION_CREATE_FORUM_POST);
			$pub->set_parameter('pid', $pid);
			$pub->set_parameter('cid', $cid);
			$pub->set_parameter('type', $type);
			$pub->set_parameter('reply', $reply);
			if($reply_lo)
			{
				if(substr($reply_lo->get_title(), 0, 3) == 'RE:')
					$reply = $reply_lo->get_title();
				else
					$reply = 'RE: ' . $reply_lo->get_title();

				$pub->set_creation_defaults(array('title' => $reply));
			}

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
				$cloi = ComplexLearningObjectItem :: factory('forum_post');

				$item = $rdm->retrieve_complex_learning_object_item($cid);

				$cloi->set_ref($object_id);
				$cloi->set_user_id($this->get_user_id());
				$cloi->set_parent($item->get_ref());
				$cloi->set_display_order($rdm->select_next_display_order($item->get_ref()));

				if($reply)
					$cloi->set_reply_on_post($reply);

				$cloi->create();
				$this->my_redirect($pid, $cid);
			}

		}
	}

	private function my_redirect($pid, $cid)
	{
		$message = htmlentities(Translation :: get('LearningObjectCreated'));

		$params = array();
		$params['pid'] = $pid;
		$params['cid'] = $cid;
        $params[ComplexDisplay::PARAM_DISPLAY_ACTION] = ForumDisplay::ACTION_VIEW_TOPIC;

		$this->redirect($message, '', $params);
	}

}
?>