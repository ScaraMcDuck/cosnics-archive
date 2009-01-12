<?php

require_once dirname(__FILE__) . '/../tool.class.php';
require_once dirname(__FILE__) . '/../tool_component.class.php';
require_once dirname(__FILE__).'/../../learning_object_repo_viewer.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object/feedback/feedback.class.php';

class ToolFeedbackPublisherComponent extends ToolComponent
{
	function run()
	{
		if(!$this->is_allowed(ADD_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		
		$trail = new BreadcrumbTrail();
		
		$object = $_GET['object'];
		
		$pub = new LearningObjectRepoViewer($this, 'feedback', true);
		$pub->set_parameter(Tool :: PARAM_ACTION, Tool :: ACTION_PUBLISH_FEEDBACK);
		
		if(isset($_GET['pid']))
			$pub->set_parameter('pid', $_GET['pid']);
		else
			$pub->set_parameter('cid', $_GET['cid']);
		
		if(!isset($object))
		{	
			$html[] = '<p><a href="' . $this->get_url() . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			$html[] =  $pub->as_html();
			$this->display_header($trail);
			echo implode("\n",$html);
			$this->display_footer();
		}
		else
		{
			$feedback = new Feedback();
			$feedback->set_id($object);
			$id = isset($_GET['pid'])?$_GET['pid']:$_GET['cid'];
			
			$publication_feedback= new LearningObjectPublicationFeedback(null, $feedback, $this->get_course_id(), $this->get_tool_id().'_feedback', $id,$this->get_user_id(), time(), 0, 0);
			$publication_feedback->set_show_on_homepage(0); 
			$publication_feedback->create(); 
			$this->redirect(null, Translation :: get('FeedbackAdded'), '', array(Tool :: PARAM_ACTION => isset($_GET['pid'])?'view':'view_item', isset($_GET['pid'])?'pid':'cid' => $id));	
		}
		
	}
}
?>