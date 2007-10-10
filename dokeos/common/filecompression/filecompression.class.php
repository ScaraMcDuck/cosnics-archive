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