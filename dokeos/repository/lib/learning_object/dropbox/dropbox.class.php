<?php
require_once dirname(__FILE__) . '/../../learningobject.class.php';

class Dropbox extends LearningObject {
	function get_filename () {
		return $this->get_additional_property('filename');
	}
	function set_filename ($filename) {
		return $this->set_additional_property('filename', $filename);
	}
	function get_filesize () {
		return $this->get_additional_property('filesize');
	}
	function set_filesize ($filesize) {
		return $this->set_additional_property('filesize', $filesize);
	}
}
?>