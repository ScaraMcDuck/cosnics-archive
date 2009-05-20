<?php

require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';
require_once Path :: get_repository_path() . 'lib/complex_learning_object_item_form.class.php';
require_once dirname(__FILE__).'/../../../learning_object_repo_viewer.class.php';

class ForumToolTopicCreatorComponent extends ForumToolComponent
{
	function run()
	{
		if($this->is_allowed(ADD_RIGHT))
		{
			$pid = Request :: get('pid');
			$forum = Request :: get('forum');
			$is_subforum = Request :: get('is_subforum');

			if(!$pid || !$forum)
			{
				$this->display_header(new BreadCrumbTrail(), true, 'courses forum tool');
				$this->display_error_message(Translation :: get('NoParentSelected'));
				$this->display_footer();
			}

			$pub = new LearningObjectRepoViewer($this, 'forum_topic', true);
			$pub->set_parameter(Tool :: PARAM_ACTION, ForumTool :: ACTION_CREATE_TOPIC);
			$pub->set_parameter('pid', $pid);
			$pub->set_parameter('forum', $forum);
			$pub->set_parameter('is_subforum', $is_subforum);

			$object_id = Request :: get('object');

			if(!isset($object_id))
			{
				$html[] = '<p><a href="' . $this->get_url(array('forum' => $forum, 'pid' => $pid)) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
				$html[] =  $pub->as_html();
				$this->display_header(new BreadCrumbTrail(), true, 'courses forum tool');
				echo implode("\n",$html);
				$this->display_footer();
			}
			else
			{
				$cloi = ComplexLearningObjectItem :: factory('forum_topic');

				if($is_subforum)
				{
					$subforum = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_item($forum)->get_ref();
					$cloi->set_parent($subforum);
				}
				else
				{
					$cloi->set_parent($forum);
				}

				$cloi->set_ref($object_id);
				$cloi->set_user_id($this->get_user_id());
				$cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($forum));

				$cloi->create();

				/*$children = RepositoryDataManager :: get_instance()->count_complex_learning_object_items(new EqualityCondition('parent', $object_id));

				if($children == 0)
				{
					$object = RepositoryDataManager :: get_instance()->retrieve_learning_object($object_id);

					$learning_object = new AbstractLearningObject('forum_post', $this->get_user_id());
					$learning_object->set_title($object->get_title());
					$learning_object->set_description($object->get_description());
					$learning_object->set_owner_id($this->get_user_id());

					$learning_object->create();

					$attachments = $object->get_attached_learning_objects();
					foreach($attachments as $attachment)
						$learning_object->attach_learning_object($attachment->get_id());

					$cloi = ComplexLearningObjectItem :: factory('forum_post');

					$cloi->set_ref($learning_object->get_id());
					$cloi->set_user_id($this->get_user_id());
					$cloi->set_parent($object_id);
					$cloi->set_display_order(1);

					$cloi->create();
				}*/

				$this->my_redirect($pid, $forum, $is_subforum);
			}

		}
	}

	private function my_redirect($pid, $forum, $is_subforum)
	{
		$message = htmlentities(Translation :: get('ForumTopicCreated'));

		$params = array();
		$params['pid'] = $pid;
		$params['tool_action'] = 'view';

		if($is_subforum)
			$params['forum'] = $forum;

		$this->redirect($message, '', $params);
	}

}
?>