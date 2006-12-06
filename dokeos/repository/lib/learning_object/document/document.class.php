<?php
/**
 * $Id$
 * @package repository.learningobject
 * @subpackage document
 */
require_once dirname(__FILE__).'/../../learningobject.class.php';
require_once dirname(__FILE__).'/../../configuration.class.php';
/**
 * A Document.
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
	function get_url()
	{
		return Configuration::get_instance()->get_parameter('general', 'upload_url').'/'.$this->get_path();
	}
	function get_full_path()
	{
		return realpath(Configuration::get_instance()->get_parameter('general', 'upload_path').'/'.$this->get_path());
	}
	function get_icon_name()
	{
		$filename = $this->get_filename();
		$parts = explode('.',$filename);
		$icon_name = $parts[count($parts)-1];
		if( !file_exists(api_get_path(WEB_CODE_PATH).'/img/'.$icon_name.'.gif'))
		{
			return 'document';
		}
		return $icon_name;
	}
	static function get_disk_space_properties()
	{
		return 'filesize';
	}
}
?>