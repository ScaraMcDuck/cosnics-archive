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
			
			if(!$pid || !$forum)
			{
				$this->display_header(new BreadCrumbTrail());
				$this->display_error_message(Translation :: get('NoParentSelected'));
				$this->display_footer();
			}
			
			$pub = new LearningObjectRepoViewer($this, 'forum_topic', true);
			$pub->set_parameter(Tool :: PARAM_ACTION, ForumTool :: ACTION_CREATE_TOPIC);
			$pub->set_parameter('pid', $pid);
			$pub->set_parameter('forum', $forum);
			
			$object_id = Request :: get('object');
			
			if(!isset($object_id))
			{	
				$html[] = '<p><a href="' . $this->get_url(array('forum' => $forum, 'pid' => $pid)) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
				$html[] =  $pub->as_html();
				$this->display_header(new BreadCrumbTrail());
				echo implode("\n",$html);
				$this->display_footer();
			}
			else
			{	
				$cloi = ComplexLearningObjectItem :: factory('forum_topic');

				$cloi->set_ref($object_id);
				$cloi->set_user_id($this->get_user_id());
				$cloi->set_parent($forum);
				$cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($forum));
				
				$cloi->create();
				
				$this->my_redirect($pid);
			}

		}
	}
	
	private function my_redirect($pid)
	{
		$message = htmlentities(Translation :: get('ForumTopicCreated'));
				
		$params = array();
		$params['pid'] = $pid;
		$params['tool_action'] = 'view'; 
	
		$this->redirect(null, $message, '', $params);
	}

}
?>