<?php
require_once dirname(__FILE__).'/../../learning_object.class.php';
require_once Path :: get_library_path().'filesystem/filesystem.class.php';
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
		return realpath($this->get_path(SYS_CODE_PATH)).'../files/chatbox/chatbox.'.$this->get_id().'.txt';
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
		$message = '<div><span class="chatuser">'.$user->get_fullname().'</span><span class="chatmessage">'.$message.'</span></div>'."\n";
		$file = $this->get_log_file();
		return Filesystem::write_to_file($file,$message,true);
	}
}
?>