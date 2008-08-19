<?php
/**
 * $Id: filecompression.class.php 13555 2007-10-24 14:15:23Z bmol $
 * @package export
 */
/**
 * Abstract class to export tabular data.
 * Create a new type of export by extending this class.
 */
abstract class LearningObjectImport
{
	/**
	 * The filename which will be used for the export file.
	 */
	private $filename;
	/**
	 * Constructor
	 * @param string $filename
	 */
	public function LearningObjectImport($filename)
	{
		$this->filename = $filename;
		Export::get_supported_filetypes();
	}
	/**
	 * Gets the filename
	 * @return string
	 */
	protected function get_filename()
	{
		return $this->filename;
	}

	/**
	 * Gets the supported filetypes for export
	 * @return array Array containig all supported filetypes (keys and values
	 * are the same)
	 */
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
	
	public static function type_supported($type)
	{
		$supported_types = self :: get_supported_filetypes();
			
		foreach($supported_types as $supported_type)
			if($supported_type == $type)
				return true;
		
		return false;
	}
	
	/**
	 * Factory function to create an instance of an export class
	 * @param string $type One of the supported file types returned by the
	 * get_supported_filetypes function.
	 * @param string $filename The desired filename for the export file
	 * (extension will be automatically added depending on the given $type)
	 */
	public static function factory($type)
	{
		$file = dirname(__FILE__).'/'.$type.'/'.$type.'_import.class.php';
		$class = DokeosUtilities :: underscores_to_camelcase($type).'Import';
		if(file_exists($file))
		{
			require_once($file);
			return new $class();
		}
	}
	
	protected function get_path($path_type)
	{
		return Path :: get($path_type);
	}
	
	abstract function import_learning_object($file, $repository_manager, $user, $original_name);
}
?>