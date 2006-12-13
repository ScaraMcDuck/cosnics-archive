<?php
/**
 * @package repository.learningobject
 * @subpackage chatbox
 */
class ChatboxDisplay extends LearningObjectDisplay
{
	// Inherited
	function get_full_html()
	{
		$object = $this->get_learning_object();
		$html = array();
		$html[] = '<div class="learning_object" style="background-image: url('.api_get_path(WEB_CODE_PATH).'img/'.$object->get_icon_name().'.gif);">';
		$html[] = '<div class="title">'.htmlspecialchars($object->get_title()).'</div>';
		$html[] = '<div class="description">'.$object->get_description().'</div>';
		$html[] = '<textarea cols="80" rows="20">Todo: replace this with a real chat window</textarea>';
		$html[] = '</div>';
		$html[] = $this->get_attached_learning_objects_as_html();
		return implode("\n",$html);
	}
}
?>