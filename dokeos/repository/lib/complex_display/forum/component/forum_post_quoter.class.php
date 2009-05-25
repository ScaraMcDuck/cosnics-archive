<?php

require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';
require_once Path :: get_repository_path() . 'lib/complex_learning_object_item_form.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/learning_object_repo_viewer.class.php';

class ForumDisplayForumPostQuoterComponent extends ForumDisplayComponent
{
	function run()
	{
        if($this->get_parent()->get_parent()->is_allowed(ADD_RIGHT))
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

			$rdm = RepositoryDataManager :: get_instance();

			$quote_item = $rdm->retrieve_complex_learning_object_item($quote);
			$quote_lo = $rdm->retrieve_learning_object($quote_item->get_ref());

			$learning_object = new AbstractLearningObject('forum_post', $this->get_user_id());
            $form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_CREATE, $learning_object, 'create', 'post', $this->get_url(array(ComplexDisplay::PARAM_DISPLAY_ACTION => ForumDisplay::ACTION_QUOTE_FORUM_POST,'pid' => $pid, 'cid' => $cid, 'quote' => $quote)));

			if(substr($quote_lo->get_title(), 0, 3) == 'RE:')
				$reply = $quote_lo->get_title();
			else
				$reply = 'RE: ' . $quote_lo->get_title();

			$defaults['title'] = $reply;
			$defaults['description'] = '[quote="' . UserDataManager :: get_instance()->retrieve_user($quote_lo->get_owner_id())->get_fullname() . '"]' . $quote_lo->get_description() . '[/quote]';

			$form->setParentDefaults($defaults);

			if($form->validate())
			{
				$object = $form->create_learning_object();
				$cloi = ComplexLearningObjectItem :: factory('forum_post');

				$item = $rdm->retrieve_complex_learning_object_item($cid);

				$cloi->set_ref($object->get_id());
				$cloi->set_user_id($this->get_user_id());
				$cloi->set_parent($item->get_ref());
				$cloi->set_display_order($rdm->select_next_display_order($item->get_ref()));

				if($quote)
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
        $params[ComplexDisplay::PARAM_DISPLAY_ACTION] = ForumDisplay::ACTION_VIEW_TOPIC;

		$this->redirect($message, false, $params);
	}

}
?>