<?php
/**
 * $Id: announcement.class.php 15410 2008-05-26 13:41:21Z Scara84 $
 * @package repository.learningobject
 * @subpackage system_announcement
 */
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * This class represents a system announcement
 */
class SystemAnnouncement extends LearningObject
{
	const PROPERTY_ICON = 'icon';

	function get_icon () {
		return $this->get_additional_property(self :: PROPERTY_ICON);
	}
	function set_icon ($icon) {
		return $this->set_additional_property(self :: PROPERTY_ICON, $icon);
	}
	function supports_attachments()
	{
		return false;
	}
	function get_icon_name()
	{
		return 'system_announcement_' . $this->get_icon();
	}
	static function get_possible_icons()
	{
		$icons = array();
		
		$icons['confirmation'] = Translation :: get('Confirmation');
		$icons['error'] = Translation :: get('Error');
		$icons['warning'] = Translation :: get('Warning');
		$icons['stop'] = Translation :: get('Stop');
		$icons['question'] = Translation :: get('Question');
		$icons['config'] = Translation :: get('Config');
		
		return $icons;
	}
	
	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_ICON);
	}
}
?>