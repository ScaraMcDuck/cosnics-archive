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
	function extract_file($file)
	{
		$dir = $this->create_temporary_directory();
		$pclzip = new PclZip($file);
		if($pclzip->extract(PCLZIP_OPT_PATH, $dir) == 0)
		{
			return false;
		}
		return $dir;
	}
}
?>