<?php

require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';
require_once Path :: get_repository_path() . 'lib/complex_learning_object_item_form.class.php';
require_once dirname(__FILE__).'/../../../learning_object_repo_viewer.class.php';

class ForumToolPostCreatorComponent extends ForumToolComponent
{
	function run()
	{
		if($this->is_allowed(ADD_RIGHT))
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
			
			$pub = new LearningObjectRepoViewer($this, 'forum_post', true);
			$pub->set_parameter(Tool :: PARAM_ACTION, ForumTool :: ACTION_CREATE_FORUM_POST);
			$pub->set_parameter('pid', $pid);
			$pub->set_parameter('cid', $cid);
			$pub->set_parameter('type', $type);
			$pub->set_parameter('reply', $reply);
			
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
				
				$item = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_item($cid);
				
				$cloi->set_ref($object_id);
				$cloi->set_user_id($this->get_user_id());
				$cloi->set_parent($item->get_ref());
				$cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($pid));
				
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
		$params['tool_action'] = 'view_topic'; 
	
		$this->redirect(null, $message, '', $params);
	}

}
?>