<?php

require_once(dirname(__FILE__) . '/../../common/filesystem/filesystem.class.php');
require_once(dirname(__FILE__) . '/../../common/filesystem/path.class.php');

/**
 * package migration.lib
 * 
 * @author Van Wayenbergh David
 */
class Logger{

	private $filename;
	private $begin;
	
	/**
	 * Constructor for creating a logfile
	 */
    function Logger($filename)
    {
    	$this->filename = Path :: get_path('SYS_PATH') . '/migration/logfiles/' . $filename;
    }
    
    /**
     * add a message to a logfile
     * @param String $message add a message to a logfile
     */
    function add_message($message)
    {
    	FileSystem :: write_to_file($this->filename, $this->get_timestamp() . $message . "\n",true);
    }
    
    /**
     * returns the path of a logfile
     * @return $filename the directory and filename of the logfile
     */
    function get_log_path()
    {
    	return $this->filename;
    }
    
    /**
     * function to get the microtime
     */
    function get_microtime() 
    { 
    	list($usec, $sec) = explode(" ",microtime()); 
    	return ((float)$usec + (float)$sec)*1000; 
	}
	
    /**
     * 
     */
     function set_start_time()
     {
     	$this->begin = $this->get_microtime();
     }
     
     function write_passed_time()
     {
     	$this->add_message('Passed Time: ' . (int)($this->get_microtime() - $this->begin) . ' ms');
     }
     
     function get_timestamp()
     {
     	setlocale ( LC_TIME, 0);
     	$timestamp = strftime("[%H:%M:%S] ", time());
     	return  $timestamp;
     }
}
?>