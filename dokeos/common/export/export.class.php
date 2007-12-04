<?php
/**
 * $Id: filecompression.class.php 13555 2007-10-24 14:15:23Z bmol $
 * @package export
 */
require_once (api_get_library_path().'/document.lib.php');
/**
 * Abstract class to export tabular data.
 * Create a new type of export by extending this class.
 */
abstract class Export
{
	private $filename;
	public function Export($filename)
	{
		$this->filename = $filename;
		Export::get_supported_filetypes();
	}
	protected function get_filename()
	{
		return $this->filename;
	}
	abstract function write_to_file($data);
	public static function get_supported_filetypes()
	{
		$directories = FileSystem::get_directory_content(dirname(__FILE__),FileSystem::LIST_DIRECTORIES,false);
		foreach($directories as $index => $directory)
		{
			$type = basename($directory);
			if($type != '.svn')
			{
				$types[$type] = $type;
			}
		}
		return $types;
	}
	public static function factory($type, $filename = 'export')
	{
		$file = dirname(__FILE__).'/'.$type.'/'.$type.'export.class.php';
		$class = ucfirst($type).'Export';
		if(file_exists($file))
		{
			require_once($file);
			return new $class($filename.'.'.$type);
		}
	}
	/**
	 * Export tabular data to XML-file
	 * @param array $data
	 * @param string $filename
	 */
	function export_table_xml($data, $filename = 'export', $item_tagname = 'item', $wrapper_tagname = null)
	{
		$file = Filesystem::create_unique_name(api_get_path(SYS_ARCHIVE_PATH),'export.xml');
		$handle = fopen($file, 'a+');
		fwrite($handle, '<?xml version="1.0" encoding="ISO-8859-1"?>'."\n");
		if (!is_null($wrapper_tagname))
		{
			fwrite($handle, "\t".'<'.$wrapper_tagname.'>'."\n");
		}
		foreach ($data as $index => $row)
		{
			fwrite($handle, '<'.$item_tagname.'>'."\n");
			foreach ($row as $key => $value)
			{
				fwrite($handle, "\t\t".'<'.$key.'>'.$value.'</'.$key.'>'."\n");
			}
			fwrite($handle, "\t".'</'.$item_tagname.'>'."\n");
		}
		if (!is_null($wrapper_tagname))
		{
			fwrite($handle, '</'.$wrapper_tagname.'>'."\n");
		}
		fclose($handle);
		DocumentManager :: file_send_for_download($file, true, $filename.'.xml');
		exit;
	}
}
?>