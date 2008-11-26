<?php
/**
 * @package application.weblcms
 * @subpackage browser.detailrenderer
 */
require_once dirname(__FILE__).'/../learning_object_publication_list_renderer.class.php';
require_once dirname(__FILE__).'/list_publication_feedback_list_renderer.class.php';
require_once dirname(__FILE__).'../../../learning_object_publisher.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';
/**
 * Renderer to display all details of learning object publication
 */
class LearningObjectPublicationDetailsRenderer extends LearningObjectPublicationListRenderer
{
	/**
	 * Returns the HTML output of this renderer.
	 * @return string The HTML output
	 */
	function as_html()
	{
		$publication_id = $this->browser->get_publication_id();
		$dm = WeblcmsDataManager :: get_instance();
		$publication = $dm->retrieve_learning_object_publication($publication_id);
		$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_CREATE,new AbstractLearningObject('feedback',Session :: get_user_id()),'new_feedback','post',$this->browser->get_url(array('pid'=>$this->browser->get_publication_id())));
		$this->browser->get_parent()->set_parameter('pid',$publication_id);
		//$pub = new LearningObjectPublisher($this->browser->get_parent(), 'feedback', true);
				
		if($form->validate())
		{
			//creation feedback object
			$feedback = $form->create_learning_object();
			//creation publication feedback object
			$publication_feedback= new LearningObjectPublicationFeedback(null, $feedback, $this->browser->get_course_id(), $publication->get_tool().'_feedback', $this->browser->get_publication_id(),$this->browser->get_user_id(), time(), 0, 0);
			$publication_feedback->set_show_on_homepage(0);
			$publication_feedback->create();
			//$this->browser->get_parent()->redirect(null, '', false, array());
			$html[] = Display::display_normal_message(Translation :: get('FeedbackAdded'),true);
		}
		
		$html[] = '<h3>' . Translation :: get('LearningObjectPublicationDetails') . '</h3>';
		$html[] = $this->render_publication($publication);
		//dump($this->browser->get_parent()->get_course());
		if($this->browser->get_parent()->get_course()->get_allow_feedback())
		{
			$html[] = '<a href="javascript:void(0)" id="showfeedbackform" style="display:none">' . Translation :: get('ShowHideFeedbackForm') . '</a>';
			$html[] = '<div id="feedbackform">';
			$html[] = '<h3>' . '<div class="title">'.Translation :: get('LearningObjectPublicationAddFeedback').'</div></h3>';
			$html[] = '<div class="feedback_block">';
			$html[] = $form->toHtml();
			$html[] = '</div></div><br />';
			$html[] = $this->render_publication_feedback($publication);
			$html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/feedback_list.js' .'"></script>';
		}
		//$html[] = $pub->as_html();
		return implode("\n", $html);
	}

	/**
	 * Renders a single publication.
	 * @param LearningObjectPublication $publication The publication.
	 * @return string The rendered HTML.
	 */
	function render_publication($publication,$first= false, $last= false)
	{
		$html = array ();
		$last_visit_date = $this->browser->get_last_visit_date();
		$icon_suffix = '';
		if($publication->is_hidden())
		{
			$icon_suffix = '_na';
		}
		elseif( $publication->get_publication_date() >= $last_visit_date)
		{
			$icon_suffix = '_new';
		}
		$html[] = '<div class="learning_object" style="background-image: url(' . Theme :: get_common_img_path().'learning_object/'.$publication->get_learning_object()->get_icon_name().$icon_suffix.'.png);">';
		$html[] = '<div class="title'. ($publication->is_visible_for_target_users() ? '' : ' invisible').'">';
		$html[] = $this->render_title($publication);
		$html[] = '</div>';
		$html[] = '<div class="description'. ($publication->is_visible_for_target_users() ? '' : ' invisible').'">';
		$html[] = $this->render_description($publication);
		$html[] = $this->render_attachments($publication);
		$html[] = '</div>';
		$html[] = '<div class="publication_info'. ($publication->is_visible_for_target_users() ? '' : ' invisible').'">';
		$html[] = $this->render_publication_information($publication);
		$html[] = '</div>';
		$html[] = '<div class="publication_actions">';
		$html[] = $this->render_publication_actions($publication,$first,$last);
		$html[] = '</div>';
		$html[] = '</div>';
		return implode("\n", $html);
	}

	/**
	 * Renders a list of all LearningObjects of the type 'feedback' attached to this LearningObject
	 * @param  LearningObjectPublication $publication The publication.
	 * @return string The rendered HTLM.
	 */
	function render_publication_feedback($publication){
		$html = array();
		$publication_feedback_array = array();
		$publication_feedback_array = $publication->retrieve_feedback();
		
		if(count($publication_feedback_array) > 0)
		{
			$html[] = '<a href="javascript:void(0)" id="showfeedback" style="display:none">' . Translation :: get('ShowHideFeedback') . '</a>';
			$html[] = '<div id="feedbacklist">';
			$html[] = '<h3>' . Translation :: get('LearningObjectPublicationListFeedback') . '</h3>';
			$renderer = new ListPublicationFeedbackListRenderer($this->browser,$publication_feedback_array);
			$html[] = $renderer->as_html();
			$html[] = '</div>';
		}
		return implode("\n", $html);
	}
	
	function render_publication_actions($publication,$first,$last)
	{
		$html = array();
		$icons = array();
		
		$html[] = '<span style="white-space: nowrap;">';
		if ($this->is_allowed(DELETE_RIGHT))
		{
			$icons[] = $this->render_delete_action($publication);
		}
		if ($this->is_allowed(EDIT_RIGHT))
		{
			$icons[] = $this->render_edit_action($publication);
			$icons[] = $this->render_visibility_action($publication);
		}
		$html[] = implode('&nbsp;', $icons);
		$html[] = '</span>';
		return implode($html);
	}
	
	/**
	 * Renders the means to toggle visibility for the given publication.
	 * @param LearningObjectPublication $publication The publication.
	 * @return string The HTML rendering.
	 */
	function render_visibility_action($publication)
	{
		$visibility_url = $this->get_url(array (Tool :: PARAM_ACTION => Tool :: ACTION_TOGGLE_VISIBILITY, Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), 'details' => '1'), true);
		if($publication->is_hidden())
		{
			$visibility_img = 'action_invisible.png';
		}
		elseif($publication->is_forever())
		{
			$visibility_img = 'action_visible.png';
		}
		else
		{
			$visibility_img = 'action_period.png';
			$visibility_url = 'javascript:void(0)';
		}
		$visibility_link = '<a href="'.$visibility_url.'"><img src="'.Theme :: get_common_img_path().$visibility_img.'"  alt=""/></a>';
		return $visibility_link;
	}

	/**
	 * Renders the means to edit the given publication.
	 * @param LearningObjectPublication $publication The publication.
	 * @return string The HTML rendering.
	 */
	function render_edit_action($publication)
	{
		$edit_url = $this->get_url(array (Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), 'details' => '1'), true);
		$edit_link = '<a href="'.$edit_url.'"><img src="'.Theme :: get_common_img_path().'action_edit.png"  alt=""/></a>';
		return $edit_link;
	}
}
?>