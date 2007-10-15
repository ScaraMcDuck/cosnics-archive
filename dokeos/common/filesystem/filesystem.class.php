<?php
/**
 * $Id$
 * @package filesystem
 */
/**
 * This class implements some usefull functions to hanlde the filesystem.
 * @todo Implement other usefull functions which are now in files like
 * fileManage.lib.php, document.lib.php, fileUpload.lib.php But keep the
 * functions to filesystem-related stuff. So this isn't the place for code for
 * getting an icon to match a documents filetype for example.
 * @todo Make sure all functions in this class remove special chars before doing
 * stuff. So other modules shouldn't take care of the special chars problems.
 * This also means some functions which now return boolean should return the
 * changed pathname or filename after they successfully finished their
 * work.
 */
class Filesystem
{
	/**
	 * Constant representing "Files and directories"
	 */
	const LIST_FILES_AND_DIRECTORIES = 1;
	/**
	 * Constant representing "Files"
	 */
	const LIST_FILES = 2;
	/**
	 * Constant representing "Directories"
	 */
	const LIST_DIRECTORIES = 3;
	/**
	 * Creates a directory.
	 * This function creates all missing directories in a given path.
	 * @param string $path
	 * @param string $mode
	 * @return boolean True if successfull, false if not.
	 */
	public static function create_dir($path,$mode = '0777')
	{
		// If the given path is a file, return false
		if(is_file($path))
		{
			return false;
		}
		// If the directory doesn't exist yet, create it using php's mkdir function
		if(!is_dir($path))
		{
			return mkdir($path,$mode,true);
		}
		return true;
	}
	/**
	 * Copies a file. If the destination directory doesn't exist, this function
	 * tries to create the directory using the Filesystem::create_dir function.
	 * @param string $source The full path to the source file
	 * @param string $destination The full path to the destination file
	 * @param boolean $overwrite If the destination file allready exists, should
	 * it be overwritten?
	 * @return boolean True if successfull, false if not.
	 */
	public static function copy_file($source,$destination,$overwrite = false)
	{
		if(file_exists($destination) && !$overwrite)
		{
			return false;
		}
		$destination_dir = dirname($destination);
		if(file_exists($source) && Filesystem::create_dir($destination_dir))
		{
			return copy($source,$destination);
		}
	}
	/**
	 * Creates a unique name for a file or a directory. This function will also
	 * use the function Filesystem::create_safe_name to make sure the resulting
	 * name is safe to use.
	 * @param string $desired_path The path
	 * @param string $desired_filename The desired filename
	 * @return string A unique name based on the given desired_name
	 */
	public static function create_unique_name($desired_path,$desired_filename = null)
	{
		$index = 0;
		if(!is_null($desired_filename))
		{
			$filename = Filesystem::create_safe_name($desired_filename);
			$new_filename = $filename;
			while (file_exists($desired_path.'/'.$new_filename))
			{
				$file_parts = explode('.', $filename);
				$new_filename = array_shift($file_parts). ($index ++).'.'.implode('.',$file_parts);
			}
			return $new_filename;
		}
		$desired_path = dirname($desired_path).'/'.Filesystem::create_safe_name(basename($desired_path));
		while(is_dir($desired_path))
		{
			$desired_path = ($index++);
		}
		return $desired_path;
	}
	/**
	 * Creates a safe name for a file or directory
	 * @param string $desired_name The desired name
	 * @return string The safe name
	 */
	public static function create_safe_name($desired_name)
	{
		//Change encoding
		$safe_name = mb_convert_encoding($desired_name,"ISO-8859-1","UTF-8");
		//Replace .php by .phps
		$safe_name = eregi_replace("\.(php.?|phtml)$", ".phps", $safe_name);
		//If first letter is . add something before
		$safe_name = eregi_replace("^\.","0.",$safe_name);
		//Replace accented characters
		$safe_name = strtr($safe_name, 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝàáâãäåçèéêëìíîïðñòóôõöøùúûüýÿ', 'aaaaaaaceeeeiiiidnoooooouuuuyaaaaaaceeeeiiiidnoooooouuuuyy');
		//Replace all except letters, numbers, - and . to underscores
	    $safe_name =  ereg_replace('[^0-9a-zA-Z\-\.]', '_',$safe_name);
	    //Replace set of underscores by a single underscore
		$safe_name = ereg_replace('[_]+','_',$safe_name);
		return $safe_name;
	}
	/**
	 * Scans all files and directories in the given path and subdirectories. If
	 * a file or directory name isn't considered as safe, it will be renamed to
	 * a safe name.
	 * @param string $path The full path to the directory. This directory will
	 * not be renamed, only its content.
	 */
	public static function create_safe_names($path)
	{
		$list = Filesystem::get_directory_content($path);
		// Sort everything, so renaming a file or directory has no impact on next elements in the array
		rsort($list);
		foreach($list as $index => $entry)
		{
			if(basename($entry) != Filesystem::create_safe_name(basename($entry)))
			{
				if( is_file($entry))
				{
					$safe_name = Filesystem::create_unique_name(dirname($entry),basename($entry));
					$destination = dirname($entry).'/'.$safe_name;
					echo $destination."\n";
					Filesystem::copy_file($entry, $destination);
					unlink($entry);
				}
				elseif(is_dir($entry))
				{
					$safe_name = Filesystem::create_unique_name($entry);
					rename($entry,$safe_name);
				}
			}
		}
	}
	/**
	 * Writes content to a file. This function will try to create the path and
	 * the file if they don't exist yet.
	 * @param string $file The full path to the file
	 * @param string $content
	 * @param boolean $append If true the given conten will be appended to the
	 * end of the file
	 */
	public static function write_to_file($file,$content,$append = false)
	{
		if(Filesystem::create_dir(dirname($file)))
		{
			if($create_file = fopen($file, $append ? 'a': 'w'))
			{
				fwrite($create_file, $values['html_content']);
				fclose($create_file);
				chmod($file, 0777);
				return true;
			}
			return false;
		}
		return false;
	}
	/**
	 * Determines the number of bytes taken by a given directory or file
	 * @param string $path The full path to the file or directory of which the
	 * disk space should be determined
	 * @return int The number of bytes taken on disk by the given directory or
	 * file
	 */
	public static function get_disk_space($path)
	{
		if(is_file($path))
		{
			return filesize($path);
		}
		if(is_dir($path))
		{
			$total_disk_space = 0;
			$files = Filesystem::get_directory_content($path,Filesystem::LIST_FILES);
			foreach($files as $index => $file)
			{
				$total_disk_space += @filesize($file);
			}
			return $total_disk_space;
		}
		// If path doesn't exist, return null
		return 0;
	}
	/**
	 * Guesses the disk space used when the given content would be written to a
	 * file
	 * @param string $content
	 * @return int The number of bytes taken on disk by a file containing the
	 * given content
	 */
	public static function guess_disk_space($content)
	{
		$tmpfname =tempnam();
		$handle = fopen($tmpfname, "w");
		fwrite($handle, $content);
		fclose($handle);
		$disk_space = Filesystem::get_disk_space($tmpfname);
		unlink($tmpfname);
		return $disk_space;
	}
	/**
	 * Retrieves all contents (files and/or directories) of a directory
	 * @param string $path The full path of the directory
	 * @param const $type Type to determines which items should be included in
	 * the resulting list
	 * @return array Containing the requested directory contents. All entries
	 * are full paths.
	 */
	public static function get_directory_content($path, $type = Filesystem::LIST_FILES_AND_DIRECTORIES)
	{
		$it = new RecursiveDirectoryIterator($path);
		foreach (new RecursiveIteratorIterator($it, 1) as $entry)
		{
			if(($type == Filesystem::LIST_FILES_AND_DIRECTORIES || $type == Filesystem::LIST_FILES) && is_file($entry))
			{
				$result[] = $entry->getRealPath();
			}
			if(($type == Filesystem::LIST_FILES_AND_DIRECTORIES || $type == Filesystem::LIST_DIRECTORIES) && is_dir($entry))
			{
				$result[] = $entry->getRealPath();
			}
		}
		return $result;
	}
	/**
	 * Removes a file or a directory (and all its contents).
	 * @param string $path To full path to the file or directory to delete
	 * @return boolean True if successfull, false if not. When a directory is
	 * given to delete, this function will delete as much as possible from this
	 * directory. If some subdirectories or files in the given directory can't
	 * be deleted, this function will return false.
	 */
	public static function remove($path)
	{
		if(is_file($path))
		{
			return @unlink($path);
		}
		elseif(is_dir($path))
		{
			$content = Filesystem::get_directory_content($path);
			// Reverse sort the content so deepest entries come first.
			rsort($content);
			$result = true;
			foreach($content as $index => $entry)
			{
				if(is_file($entry))
				{
					$result &= @unlink($entry);
				}
				elseif(is_dir($entry))
				{
					$result &= @rmdir($entry);
				}
			}
			return ($result & @rmdir($path));
		}
	}
}
?>