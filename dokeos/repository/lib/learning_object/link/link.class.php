<?php
require_once dirname(__FILE__) . '/../../learningobject.class.php';
/**
 * @package repository.learningobject.link
 */
class Link extends LearningObject {
	const PROPERTY_URL = 'url';
	
	function get_url () {
		return $this->get_additional_property(self :: PROPERTY_URL);
	}
	function set_url ($url) {
		return $this->set_additional_property(self :: PROPERTY_URL, $url);
	}
}
?>