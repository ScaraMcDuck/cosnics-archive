<?php
require_once dirname(__FILE__).'/../../learningobject.class.php';
/**
 * @package repository.learningobject
 * @subpackage document
 */
class Document extends LearningObject
{
	const PROPERTY_PATH = 'path';
	const PROPERTY_FILENAME = 'filename';
	const PROPERTY_FILESIZE = 'filesize';

	function get_path()
	{
		return $this->get_additional_property(self :: PROPERTY_PATH);
	}
	function set_path($path)
	{
		return $this->set_additional_property(self :: PROPERTY_PATH, $path);
	}
	function get_filename()
	{
		return $this->get_additional_property(self :: PROPERTY_FILENAME);
	}
	function set_filename($filename)
	{
		return $this->set_additional_property(self :: PROPERTY_FILENAME, $filename);
	}
	function get_filesize()
	{
		return $this->get_additional_property(self :: PROPERTY_FILESIZE);
	}
	function set_filesize($filesize)
	{
		return $this->set_additional_property(self :: PROPERTY_FILESIZE, $filesize);
	}
	function delete()
	{
		$path = Configuration :: get_instance()->get_parameter('general', 'upload_path');
		$path = $path.'/'.$this->get_path();
		unlink($path);
		parent :: delete();
	}
	static function get_disk_space_properties()
	{
		return 'filesize';
	}
}
?>