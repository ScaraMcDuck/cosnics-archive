<?php
require_once dirname(__FILE__) . '/../../learningobject.class.php';
/**
 * @package learningobject.link
 */
class Link extends LearningObject {
	function get_url () {
		return $this->get_additional_property('url');
	}
	function set_url ($url) {
		return $this->set_additional_property('url', $url);
	}
}
?>