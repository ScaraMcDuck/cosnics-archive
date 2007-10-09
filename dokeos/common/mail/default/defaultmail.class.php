<?php
/**
 * $Id: authentication.class.php 13418 2007-10-09 08:05:07Z bmol $
 * @package mail
 */
require_once dirname(__FILE__).'/../mail.class.php';
/**
 * This class implements the abstract Mail class and uses the php mail()
 * function to send the emails.
 */
class DefaultMail extends Mail
{
	function send()
	{
		$headers = array();
		foreach($this->get_cc() as $index => $cc)
		{
			$headers[] = 'Cc: '.$cc;
		}
		foreach($this->get_bcc() as $index => $bcc)
		{
			$headers[] = 'Bcc: '.$bcc;
		}
		if(!is_null($this->get_from()))
		{
			$headers[] = 'From: '.$this->get_from();
			$headers[] = 'Reply-To: '.$this->get_from();
		}
		$headers = implode("\n",$headers);
		mail(implode(',',$this->get_to()),$this->get_subject(),$this->get_message(),$headers);
	}
}
?>