<?php

require_once dirname(__FILE__) . '/../../learning_object_publication_form.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';

class ToolComplexFeedbackComponent extends ToolComponent
{
	function run()
	{
        $trail = new BreadcrumbTrail();

		$object = $_GET['object'];
		$pub = new LearningObjectRepoViewer($this, 'feedback', true);
		$pub->set_parameter(Tool :: PARAM_ACTION, Tool :: ACTION_PUBLISH_FEEDBACK);
		if(isset($_GET['pid']))
            $pub->set_parameter('pid', $_GET['pid']);

		if(isset($_GET['cid']))
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
			$id = isset($_GET['cid'])?$_GET['cid']:$_GET['pid'];
			$pid = $_GET['pid'];

            /*
             * change in the feedback 
             */
			$complex_feedback= new LearningObjectPublicationFeedback(null, $feedback, $this->get_course_id(), $this->get_tool_id().'_feedback', $id,$this->get_user_id(), time(), 0, 0);
			$complex_feedback->set_show_on_homepage(0);
			$complex_feedback->create();
			$this->redirect(null, Translation :: get('FeedbackAdded'), '', array(Tool :: PARAM_ACTION => isset($_GET['cid'])?'view_item':'view', isset($_GET['cid'])?'cid':'pid' => $id, 'pid' => $pid));
		}
    }
}
?>
