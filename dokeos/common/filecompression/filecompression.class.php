<?php
/**
 * $Id$
 * @package filecompression
 */
/**
 * An abstract class for handling file compression. Impement new compression
 * methods by creating a class which extends this abstract class.
 */
abstract class Filecompression
{
    function Filecompression()
    {
    }
    protected function create_temporary_directory()
    {
		$path = api_get_path(SYS_PATH).'archive/'.uniqid();
		mkdir($path);
		return $path;
    }
    /**
     * Retrieves an array of all supported mimetypes for this file compression
     * implementation.
     * @return array
     */
    abstract function get_supported_mimetypes();
    /**
     * Determines if a given mimetype is supported by the file compression
     * implementation.
     * @return boolean True if the given mimetype is supported.
     */
    abstract function is_supported_mimetype($mimetype);
    /**
     * Extracts a compressed file to a given directory. This function will also
     * make sure that all resulting directory- and filenames are safe using the
     * Filesystem::get_safe_filename function.
     * @see Filesystem::get_safe_filename
     * @param string $file The full path to the file which should be extracted
     * @return string|boolean The full path to the directory where the file was
     * extracted or boolean false if extraction wasn't successfull
     */
    abstract function extract_file($file);
    /**
     * Create a filecompression instance
     * @todo At the moment this returns the class using pclzip. The class to
     * return should be configurable
     */
    public static function factory()
    {
		require_once dirname(__FILE__).'/pclzip/pclzipfilecompression.class.php';
		return new PclzipFilecompression();
    }
}
?>