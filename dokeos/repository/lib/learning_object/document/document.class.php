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

	function delete_version()
	{
		$path = Configuration :: get_instance()->get_parameter('general', 'upload_path');
		$path = $path.'/'.$this->get_path();
		if (RepositoryDataManager :: get_instance()->is_only_document_occurence($this->get_path()))
		{
			unlink($path);
		}
		parent :: delete_version();
	}

	function get_url()
	{
		return Configuration :: get_instance()->get_parameter('general', 'upload_url').'/'.$this->get_path();
	}
	function get_full_path()
	{
		return realpath(Configuration :: get_instance()->get_parameter('general', 'upload_path').'/'.$this->get_path());
	}
	function get_icon_name()
	{
		$filename = $this->get_filename();
		$parts = explode('.', $filename);
		$icon_name = $parts[count($parts) - 1];
		if (!file_exists(api_get_path(SYS_CODE_PATH).'/img/'.$icon_name.'.gif'))
		{
			return 'document';
		}
		return $icon_name;
	}
	static function get_disk_space_properties()
	{
		return 'filesize';
	}
	function send_as_download()
	{
		header('Expires: Wed, 01 Jan 1990 00:00:00 GMT');
		header('Cache-Control: public');
		header('Pragma: no-cache');
		header('Content-type: application/octet-stream');
		//header('Content-Type: application/force-download');
		header('Content-length: '.$this->get_filesize());
		if (preg_match("/MSIE 5.5/", $_SERVER['HTTP_USER_AGENT']))
		{
			header('Content-Disposition: filename= '.$this->get_filename());
		}
		else
		{
			header('Content-Disposition: attachment; filename= '.$this->get_filename());
		}
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
		{
			header('Pragma: ');
			header('Cache-Control: ');
			header('Cache-Control: public'); // IE cannot download from sessions without a cache
		}
		header('Content-Description: '.$this->get_filename());
		header('Content-transfer-encoding: binary');
		$fp = fopen($this->get_full_path(), 'r');
		fpassthru($fp);
		return true;
	}
}
?>