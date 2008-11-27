<?php
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * @package repository.learningobject
 * @subpackage link
 */
class Link extends LearningObject
{
	const PROPERTY_URL = 'url';

	function get_url ()
	{
		return $this->get_additional_property(self :: PROPERTY_URL);
	}
	function set_url ($url)
	{
		return $this->set_additional_property(self :: PROPERTY_URL, $url);
	}
	
	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_URL);
	}
}
?>