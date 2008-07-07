<?php
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * @package repository.object
 * @subpackage rssfeed
 */
class RssFeed extends LearningObject {
	const PROPERTY_URL = 'url';

	function get_url () {
		return $this->get_additional_property(self :: PROPERTY_URL);
	}
	function set_url ($url) {
		return $this->set_additional_property(self :: PROPERTY_URL, $url);
	}
}
?>