<?php
/**
 * $Id$
 * @package filecompression
 */
require_once api_get_path(SYS_PATH).'plugin/pclzip-2-6/pclzip.lib.php';
/**
 * This class implements file compression and extraction using the PclZip
 * library
 */
class PclzipFilecompression extends Filecompression
{
	function get_supported_mimetypes()
	{
		return array('application/x-zip-compressed','application/zip','multipart/x-zip','application/x-gzip','multipart/x-gzip');
	}
	function is_supported_mimetype($mimetype)
	{
		return in_array($mimetype,$this->get_supported_mimetypes());
	}
	function extract_file($file)
	{
		$dir = $this->create_temporary_directory();
		$pclzip = new PclZip($file);
		if($pclzip->extract(PCLZIP_OPT_PATH, $dir) == 0)
		{
			return false;
		}
		Filesystem::create_safe_names($dir);
		return $dir;
	}
	function create_archive($path)
	{
		$archive_file = Filesystem::create_unique_name(api_get_path(SYS_PATH).'files/temp/',uniqid().'.zip');
		$archive_file = realpath(api_get_path(SYS_PATH).'files/temp/').$archive_file;
		$content = Filesystem::get_directory_content($path);
		$pclzip = new PclZip($archive_file);
		// Looks like the PCLZIP_OPT_REMOVE_PATH parameter can't deal with the drive-letter in Windows-paths, so we remove it here.
		$path_to_remove = ereg_replace('^[A-Z]:','',realpath(dirname($path)));
		$pclzip->add($content,PCLZIP_OPT_REMOVE_PATH,$path_to_remove);
		return $archive_file;
	}
}
?>