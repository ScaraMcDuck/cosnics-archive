<?php
/**
 * @package application.weblcms
 * @subpackage browser.detailrenderer
 */
require_once dirname(__FILE__).'/../learning_object_publication_list_renderer.class.php';
require_once dirname(__FILE__).'/list_publication_feedback_list_renderer.class.php';
require_once dirname(__FILE__).'../../../learning_object_publisher.class.php';
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
		$form = LearningObjectForm::factory(LearningObjectForm :: TYPE_CREATE,new AbstractLearningObject('feedback',Session :: get_user_id()),'new_feedback','post',$this->browser->get_url(array('pid'=>$this->browser->get_publication_id())));
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
			$html[] = Display::display_normal_message(Translation :: get('FeedbackAdded'),true);
		}
		
		$html[] = '<h3>' . Translation :: get('LearningObjectPublicationDetails') . '</h3>';
		$html[] = $this->render_publication($publication);
		$html[] = '<h3>' . '<div class="title">'.Translation :: get('LearningObjectPublicationAddFeedback').'</div></h3>';
		$html[] = $form->toHtml();
		$html[] = $this->render_publication_feedback($publication);
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
			$html[] = '<h3>' . Translation :: get('LearningObjectPublicationListFeedback') . '</h3>';
			$renderer = new ListPublicationFeedbackListRenderer($this->browser,$publication_feedback_array);
			$html[] = $renderer->as_html();
		}
		return implode("\n", $html);
	}
}
?>