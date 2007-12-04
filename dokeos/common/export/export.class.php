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
}
?>