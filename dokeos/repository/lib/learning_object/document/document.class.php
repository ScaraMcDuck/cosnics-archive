<?php
require_once dirname(__FILE__) . '/../../learningobject.class.php';
/**
 * @package learningobject.document
 */
class Document extends LearningObject {
	function get_path ()
	{
		return $this->get_additional_property('path');
	}
	function set_path ($path)
	{
		return $this->set_additional_property('path', $path);
	}
	function get_filename ()
	{
		return $this->get_additional_property('filename');
	}
	function set_filename ($filename)
	{
		return $this->set_additional_property('filename', $filename);
	}
	function get_filesize ()
	{
		return $this->get_additional_property('filesize');
	}
	function set_filesize ($filesize)
	{
		return $this->set_additional_property('filesize', $filesize);
	}
	function delete()
	{
		$path = Configuration::get_instance()->get_parameter('general', 'upload_path');
		$path = $path.'/'.$this->get_path();
		unlink($path);
		parent::delete();
	}
	static function get_disk_space_properties()
	{
		return 'filesize';
	}
}
?>