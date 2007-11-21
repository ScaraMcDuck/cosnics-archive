<?php
/**
 * @package repository.learningobject
 * @subpackage feedback
 */
require_once dirname(__FILE__) . '/../../learningobject.class.php';
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
		$icons['thumbs_up'] = get_lang('thumbs_up');
		$icons['thumbs_down'] = get_lang('thumbs_down');
		$icons['wrong'] = get_lang('wrong');
		$icons['right'] = get_lang('right');
		$icons['description'] = get_lang('informative');
		return $icons;
	}
}
?>