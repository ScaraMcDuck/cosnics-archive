<?php

require_once(Path :: get_library_path().'filesystem/filesystem.class.php');
require_once(Path :: get_library_path().'filesystem/path.class.php');

/**
 * package migration.lib
 * 
 * @author Van Wayenbergh David
 */
class Logger
{
	private $filename;
	private $file;
	private $begin;
	
	/**
	 * Constructor for creating a logfile
	 */
    function Logger($filename, $append = false)
    {
    	$this->filename = Path :: get_path('SYS_PATH') . '/migration/logfiles/' . $filename;
    	Filesystem::create_dir(dirname($filename));
    	$this->file = fopen($this->filename, $append?'a+':'w+');
    }
    
    /**
     * add a message to a logfile
     * @param String $message add a message to a logfile
     */
    function add_message($message)
    {
    	fwrite($this->file, $this->get_timestamp() . $message . "\n");
    }
    
    function write_text($text)
    {
    	fwrite($this->file, $text . "\n");
    }
    
    function is_text_in_file($text)
    {
    	while (!feof($this->file))
    	{
    		$line = fgets($this->file);
    		if(strcmp($line,$text))
    			return true;
    	}

    	return false;
    }
    
    /**
     * close the log file
     */
    function close_file()
    {
    	fclose($this->file);
		chmod($this->file, 0777);
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
    	return ((float)$usec + (float)$sec); 
	}
	
    /**
     * function to set the start time
     */
     function set_start_time()
     {
     	$this->begin = $this->get_microtime();
     }
     
     /**
      * function to write the used time
      */
     function write_passed_time()
     {
     	$this->add_message('Passed Time: ' . (int)($this->get_microtime() - $this->begin) . ' s');
     }
     
     /**
      * function to get the timestamps
      * @return String returns the timestamps used in a log file
      */
     function get_timestamp()
     {
     	setlocale ( LC_TIME, 0);
     	$timestamp = strftime("[%H:%M:%S] ", time());
     	return  $timestamp;
     }
}
?>