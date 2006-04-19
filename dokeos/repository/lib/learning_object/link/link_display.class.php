<?php
/**
 * @package repository.learningobject.link
 */
class LinkDisplay extends LearningObjectDisplay
{
	function get_full_html()
	{
		$object = $this->get_learning_object();
		$html[] = '<div class="learning_object">';
		$html[] = '<div class="icon"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$object->get_type().'.gif" alt="'.$object->get_type().'"/></div>';
		$html[] = '<div class="title">'.htmlentities($object->get_title()).'</div>';
		$html[] = '<div class="description">'.$object->get_description();
		$html[] = '<br /><br /><a href="'.htmlentities($object->get_url()).'">'.htmlentities($object->get_url()).'</a>';
		$html[] = '</div>';
		$html[] = '</div>';
		return implode("\n",$html);
	}
	function get_short_html()
	{
		$object = $this->get_learning_object();
		return '<span class="learning_object"><a href="'.$object->get_url().'">'.htmlentities($object->get_title()).'</a></span>';
	}
}
?>