<?php
require_once dirname(__FILE__).'/../../learningobject.class.php';
/**
 * @package repository.learningobject
 * @subpackage chatbox
 */
class Chatbox extends LearningObject
{
	/**
	 * Get the path to the logfile of this chatbox
	 * @return string
	 * @todo Start new logfile on .../after .../...
	 * @todo Find better location to store chatlogs
	 */
	private function get_log_file()
	{
		$path = realpath(api_get_path(SYS_CODE_PATH)).'upload/chatbox/';
		mkdir($path);
		$file = $path.'chatbox.'.$this->get_id().'.txt';
		touch($file);
		return $file;
	}
	/**
	 * Get all messages in this chatbox
	 * @param string HTML-formatted content of the chatbox
	 */
	public function get_messages()
	{
		$file = $this->get_log_file();
		return file_get_contents($file);
	}
	/**
	 * Adds a message to this chatbox
	 * @param User $user The user posting the message
	 * @param string $message The message
	 * @return boolean True on success, false if not
	 */
	public function add_message($user, $message)
	{
		$message = '<div><span class="chatuser">'.$user->get_fullname().'</span><span class="chatmessage">'.$message.'</span></div>';
		$file = $this->get_log_file();
		if (is_writable($file))
		{
			if (!$handle = fopen($file, 'a'))
			{
				return false;
			}
			if (!fwrite($handle, $message))
			{
				return false;
			}
			fclose($handle);
			return true;
		}
		else
		{
			return false;
		}
	}
}
?>