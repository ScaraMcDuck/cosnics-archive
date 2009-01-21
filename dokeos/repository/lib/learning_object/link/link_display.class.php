<?php
/**
 * @package repository.learningobject
 * @subpackage link
 */
class LinkDisplay extends LearningObjectDisplay
{
	function get_full_html()
	{
		$html = parent :: get_full_html();
		$object = $this->get_learning_object();
		return str_replace(self::DESCRIPTION_MARKER, '<div class="link_url" style="margin-top: 1em;"><a href="'.htmlentities($object->get_url()).'">'.htmlentities($object->get_url()).'</a></div>' . self::DESCRIPTION_MARKER, $html);
	}
	function get_short_html()
	{
		$object = $this->get_learning_object();
		return '<span class="learning_object"><a target="about:blank" href="'.htmlentities($object->get_url()).'">'.htmlentities($object->get_title()).'</a></span>';
	}
}
?>