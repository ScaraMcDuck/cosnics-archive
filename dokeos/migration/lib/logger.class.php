<?php
require_once(dirname(__FILE__) . '/../../common/filesystem/filesystem.class.php');
/**
 * package migration.lib
 * 
 * @author Van Wayenbergh David
 */
class Logger{

	private $filename;
	private $filesystem;
	
	/**
	 * Constructor for creating a logfile
	 */
    function logger($filename)
    {
    	this->$filename = '/../migration/logfiles/' . $filename;
    	$filesystem = new Filesystem();
    }
    
    /**
     * add a message to a logfile
     * @param String $message add a message to a logfile
     */
    function add_message($message)
    {
    	$filesystem->write_to_file($filename, $message,true);
    }
    
    /**
     * returns the path of a logfile
     * @return $filename the directory and filename of the logfile
     */
    function get_log_path()
    {
    	return $filename;
    }
}
?>