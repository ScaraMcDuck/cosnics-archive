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
	 * The imported lo's file properties.
	 */
	private $learning_object_file;
	
	/**
	 * The user importing the lo.
	 */
	private $user;
	
	/**
	 * The category the lo should be placed in.
	 */
	private $category;
	
	/**
	 * Constructor
	 * @param string $filename
	 */
	public function LearningObjectImport($learning_object_file, $user, $category)
	{
		$this->learning_object_file = $learning_object_file;
		$this->user = $user;
		$this->category = $category;
	}
	/**
	 * Gets the learning object file
	 * @return array
	 */
	function get_learning_object_file()
	{
		return $this->learning_object_file;
	}
	
	function get_user()
	{
		return $this->user;
	}
	
	function get_category()
	{
		return $this->category;
	}	
	
	/**
	 * Gets a learning object file property
	 * @return array
	 */
	function get_learning_object_file_property($name)
	{
		return $this->learning_object_file[$name];
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
		return in_array($type, self :: get_supported_filetypes());
	}
	
	/**
	 * Factory function to create an instance of an export class
	 * @param string $type One of the supported file types returned by the
	 * get_supported_filetypes function.
	 * @param string $filename The desired filename for the export file
	 * (extension will be automatically added depending on the given $type)
	 */
	public static function factory($type, $learning_object_file, $user, $category)
	{
		$file = dirname(__FILE__).'/'.$type.'/'.$type.'_import.class.php';
		$class = DokeosUtilities :: underscores_to_camelcase($type).'Import';
		if(file_exists($file))
		{
			require_once($file);
			return new $class($learning_object_file, $user, $category);
		}
	}
	
	protected function get_path($path_type)
	{
		return Path :: get($path_type);
	}
	
	abstract function import_learning_object();
}
?>