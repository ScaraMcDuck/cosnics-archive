<?php
/**
 * $Id$
 * @package mail
 */
/**
 * Change these values if you want to use phpmailer to send emails
 */
$phpmailer_config['SMTP_FROM_EMAIL'] = $administrator["email"];
$phpmailer_config['SMTP_FROM_NAME'] = $administrator["name"];
$phpmailer_config['SMTP_HOST'] = 'localhost';
$phpmailer_config['SMTP_PORT'] = 25;
$phpmailer_config['SMTP_MAILER'] = 'smtp'; //mail, sendmail or smtp
$phpmailer_config['SMTP_AUTH'] = 0;
$phpmailer_config['SMTP_USER'] = '';
$phpmailer_config['SMTP_PASS'] = '';
?>