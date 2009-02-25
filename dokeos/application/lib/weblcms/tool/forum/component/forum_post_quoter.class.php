<?php

require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';
require_once Path :: get_repository_path() . 'lib/complex_learning_object_item_form.class.php';
require_once dirname(__FILE__).'/../../../learning_object_repo_viewer.class.php';

class ForumToolPostQuoterComponent extends ForumToolComponent
{
	function run()
	{
		if($this->is_allowed(ADD_RIGHT))
		{
			$pid = Request :: get('pid');
			$cid = Request :: get('cid');
			
			$quote = Request :: get('quote');
			
			if(!$pid || !$cid || !$quote)
			{
				$this->display_header(new BreadCrumbTrail());
				$this->display_error_message(Translation :: get('NoParentSelected'));
				$this->display_footer();
			}
			
			$quote_lo = RepositoryDataManager :: get_instance()->retrieve_learning_object($quote);
			
			$learning_object = new AbstractLearningObject('forum_post', $this->get_user_id());
			$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_CREATE, $learning_object, 'create', 'post', $this->get_url(array(Tool :: PARAM_ACTION => ForumTool :: ACTION_QUOTE_FORUM_POST, 'pid' => $pid, 'cid' => $cid, 'quote' => $quote)));
			
			$defaults['title'] = 'RE: ' . $quote_lo->get_title();
			$defaults['description'] = '[quote user="' . UserDataManager :: get_instance()->retrieve_user($quote_lo->get_owner_id())->get_fullname() . '"]' . $quote_lo->get_description() . '[/quote]';
			
			$form->setParentDefaults($defaults);
			
			if($form->validate())
			{	
				$object = $form->create_learning_object();
				$cloi = ComplexLearningObjectItem :: factory('forum_post');
				
				$item = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_item($cid);
				
				$cloi->set_ref($object->get_id());
				$cloi->set_user_id($this->get_user_id());
				$cloi->set_parent($item->get_ref());
				$cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($pid));
				
				if($reply)
					$cloi->set_reply_on_post($quote);
				
				$cloi->create();
				$this->my_redirect($pid, $cid);
			}
			else
			{
				$this->display_header(new BreadCrumbTrail());
				$form->display();
				$this->display_footer();
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