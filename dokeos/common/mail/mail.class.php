<?php
/**
 * $Id: authentication.class.php 13418 2007-10-09 08:05:07Z bmol $
 * @package mail
 */
/**
 * An abstract class for sending emails. Impement new mail methods by creating a
 * class which extends this abstract class.
 */
abstract class Mail
{
	/**
	 * The sender of the mail
	 */
	private $from;
	/**
	 * Array of receivers in the TO field of the mail
	 */
	private $to;
	/**
	 * Array of receivers in the CC field of the mail
	 */
	private $cc;
	/**
	 * Array of receivers in the BCC field of the mail
	 */
	private $bcc;
	/**
	 * The subject of the mail
	 */
	private $subject;
	/**
	 * The message of the mail
	 */
	private $message;
	/**
	 * Constructor
	 */
	function Mail($subject, $message, $to, $from = null, $cc = array (), $bcc = array ())
	{
		$this->subject = $subject;
		$this->message = $message;
		if(!is_array($to))
		{
			$to = array($to);
		}
		$this->to = $to;
		$this->cc = $cc;
		$this->bcc = $bcc;
		$this->from = $from;
	}
	/**
	 * Create a new mail instance.
	 * @todo This function now uses the DefaultMail-class. The class to use
	 * should be configurable.
	 */
	public static function factory($subject,$message,$to,$from = null, $cc = array(),$bcc = array())
	{
		require_once dirname(__FILE__).'/default/defaultmail.class.php';
		return new DefaultMail($subject,$message,$to,$from,$cc,$bcc);
	}
	function get_subject()
	{
		return $this->subject;
	}
	function get_message()
	{
		return $this->message;
	}
	function get_to()
	{
		return $this->to;
	}
	function get_cc()
	{
		return $this->cc;
	}
	function get_bcc()
	{
		return $this->bcc;
	}
	function get_from()
	{
		return $this->from;
	}
	/**
	 * Send the email
	 */
	abstract function send();
}
?>