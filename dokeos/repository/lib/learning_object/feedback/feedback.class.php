<?php
/**
 * @package repository.learningobject
 * @subpackage feedback
 */
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * A feedback
 */
class Feedback extends LearningObject {
	const PROPERTY_ICON = 'icon';

	function get_icon () {
		return $this->get_additional_property(self :: PROPERTY_ICON);
	}
	function set_icon ($icon) {
		return $this->set_additional_property(self :: PROPERTY_ICON, $icon);
	}
	function supports_attachments()
	{
		return true;
	}
	function get_icon_name()
	{
		return $this->get_icon();
	}
	static function get_possible_icons()
	{
		$icons['thumbs_up'] = Translation :: get('thumbs_up');
		$icons['thumbs_down'] = Translation :: get('thumbs_down');
		$icons['wrong'] = Translation :: get('wrong');
		$icons['right'] = Translation :: get('right');
		$icons['informative'] = Translation :: get('informative');
		return $icons;
	}
	
	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_ICON);
	}
}
?>