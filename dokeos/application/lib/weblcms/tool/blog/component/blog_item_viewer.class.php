<?php
require_once dirname(__FILE__).'/../../../learning_object_repo_viewer.class.php';
require_once dirname(__FILE__).'/../../../browser/list_renderer/list_publication_feedback_list_renderer.class.php';

class BlogToolItemViewerComponent extends BlogToolComponent
{
	function run() 
	{
		if (!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		$trail = new BreadcrumbTrail();
		$trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => BlogTool :: ACTION_VIEW_BLOG, Tool :: PARAM_PUBLICATION_ID => Request :: Get(Tool :: PARAM_PUBLICATION_ID))), Translation :: get('ViewBlog')));
		
		$cloi_id = Request :: get(Tool :: PARAM_COMPLEX_ID);
		if(!$cloi_id)
		{
			$this->display_header($trail);
			$this->display_error_message(Translation :: get('NoObjectSelected'));
			$this->display_footer();
		}
		
		$trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_COMPLEX_ID => $cloi_id, Tool :: PARAM_PUBLICATION_ID => Request :: Get('pid'))), Translation :: get('ViewBlogItem')));
		
		$dm = RepositoryDataManager :: get_instance();
		$cloi = $dm->retrieve_complex_learning_object_item($cloi_id);
		$object = $dm->retrieve_learning_object($cloi->get_ref());

		$this->display_header($trail);
		echo '<br />' . $this->display_learning_object($object, $cloi_id);

		if($this->get_course()->get_allow_feedback())
		{
			echo '<a href="' . $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_FEEDBACK, Tool :: PARAM_COMPLEX_ID => $cloi_id)) . '">' . Translation :: get('AddFeedback') . '</a><br />';
			echo $this->display_feedback($cloi_id);
			echo '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/feedback_list.js' .'"></script>';
		}

		$this->display_footer();
	}
	
	function display_feedback($cloi_id)
	{
		$html = array();
		$feedback_array = $this->retrieve_feedback($cloi_id);
		
		if(count($feedback_array) > 0)
		{
			$html[] = '<a href="#" id="showfeedback" style="display:none">' . Translation :: get('ShowFeedback') . '</a>';
			$html[] = '<div id="feedbacklist">';
			$html[] = '<h3>' . Translation :: get('LearningObjectPublicationListFeedback') .  ' <a href="#" id="hidefeedback" style="display:none; font-size: 80%; font-weight: normal;">(' . Translation :: get('Hide') . ')</a></h3>';
			$renderer = new ListPublicationFeedbackListRenderer($this,$feedback_array);
			$html[] = $renderer->as_html();
			$html[] = '</div>';
		}
		return implode("\n", $html);
	}

}
?>