<?php  
/**
============================================================================== 
*	@package dokeos.include
============================================================================== 
*/

/*
                            mailnotification.inc.php  -  description
                             -------------------
    begin                : Friday June 27 2003
    copyright            : (C) 2003 Patrick Cool
    email                : patrick.cool@ugent.be
  
***************************************************************************/

/*
This is a feature add-on for phpBB used in Claroline 1.4. A lot of feature 
of the original phpBB have been stripped out of Claroline 1.4. One of these
features is the ability to subscribe to a thread and to receive an e-mail 
message when someone replies to a thread you subscribed to. 
phpBB in Claroline has this feature disabled.
I added my own code so everyone who posts a message or replies to a message
can get an e-mail notification. (fyi: in the original phpBB, only the thread
starter could get such an e-mail. 
***************************************************************************/

/*
 *                                         				                                
 *   This program is free software; you can redistribute it and/or modify  	
 *   it under the terms of the GNU General Public License as published by  
 *   the Free Software Foundation; either version 2 of the License, or	    	
 *   (at your option) any later version.
 *
 ***************************************************************************/
// first of all we try to get the topic title. This will be used in the e-mail message
$selecttopictitle = "SELECT topic_title FROM `$tbl_topics` WHERE topic_id = '$topic'";  
$resulttopictitle=mysql_query($selecttopictitle);
		while ($rowtopictitle=mysql_fetch_array($resulttopictitle))
		{
		$topictitle=$rowtopictitle["topic_title"];
		}
		
// we want to find all the user who have subscribed to this thread. We use DISTINCT so that each e-mail adress
// will only occur once. We select only those who have subscribed (topic_notify='1'). For each unique e-mail address
// we send a mail using the mail function: mail ($to, $subject, $message) 
// emailHeaders and emailAddPars added by Toon 24/11/2003
$table_user = Database::get_main_table(MAIN_USER_TABLE);
$selectnotify="SELECT DISTINCT t1.nom, t1.prenom, t2.email FROM `$tbl_posts` t1, $table_user t2 WHERE topic_id='$topic' AND topic_notify='1' AND t1.nom=t2.lastname AND t1.prenom=t2.firstname AND t2.lastname <> ''"; 

$resultnotify=mysql_query($selectnotify) or die(mysql_error());
while ($row=mysql_fetch_array($resultnotify))
	{
	$to_email=$row["email"];
	$mailtitle=$lang_mail_notification_title."'".$topictitle."' (".$_course["name"]."). \n";
	$mailmessage=$lang_mail_notification_captatio.$row["prenom"]." \n\n";
	$mailmessage=$mailmessage.$prenom." ".$nom.$lang_mail_notification_hasreplied."(".$topictitle.")";
	$mailmessage=$mailmessage.$lang_mail_notification_ofcourse.$_course["name"];
	$mailmessage=$mailmessage.$lang_mail_notification_informed;

	$emailFrom=get_setting('emailAdministrator');
	$emailHeaders = "From: ".get_setting('administratorName')." <$emailFrom>\n";
	$emailHeaders .= "Reply-To: $emailFrom";

	$emailAddPars = "-f".$emailFrom;

	@api_send_mail($to_email, $mailtitle, $mailmessage, $emailHeaders, $emailAddPars);
	}

// The mail the user will receive will look like this. 
/*
from: admin@servername.net
to: yourmail@yourserver.com
subject: reply op topic: this_is_the_topic_title (this_is_the_course_title)
Message:
	Beste yourname
	
	repliersname heeft gereageerd op een forumbericht this_is_the_topic_title van het vak 
	this_is_the_course_title waar u aan deelgenomen hebt. U had aangegeven dat u op de hoogte gehouden 
	wilde worden van eventuele antwoorden op dit forumbericht

*/

?>
