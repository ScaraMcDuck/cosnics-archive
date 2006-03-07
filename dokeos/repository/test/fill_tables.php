<?php


/**
==============================================================================
 *	This is a simple test script that fills the LCMS data source with garbage
 *	data. For testing purposes only. Does not empty tables prior to adding
 *	new learning objects.
 * 
 *	@author Tim De Pauw
==============================================================================
 */

require_once dirname(__FILE__).'/../lib/datamanager.class.php';

$users = 1;

$announcements = rand(100, 500);
$calendar_events = rand(100, 500);
$documents = rand(100, 500);
$links = rand(100, 500);
$student_publications = rand(100, 500);

// TODO
/*
$forums = rand(100,500);
$forum_posts = rand(100,500);
$forum_topics = rand(100,500);
$learnpaths = rand(100,500);
*/

$dataManager = DataManager :: get_instance();

for ($i = 0; $i < $announcements; $i ++)
{
	$announcement = new Announcement();
	$announcement->set_owner_id(random_user());
	$announcement->set_title(random_string(2));
	$announcement->set_description(random_string(8));
	$announcement->create();
}

for ($i = 0; $i < $calendar_events; $i ++)
{
	$event = new CalendarEvent();
	$event->set_owner_id(random_user());
	$event->set_title(random_string(2));
	$event->set_description(random_string(8));
	$event->create();
}

for ($i = 0; $i < $documents; $i ++)
{
	$document = new Document();
	$document->set_owner_id(random_user());
	$document->set_title(random_string(2));
	$document->set_description(random_string(8));
	$document->set_path('/'.random_word().'/'.random_word());
	$document->set_filename(random_word());
	$document->set_filesize(rand(1000, 10000));
	$document->create();
}

for ($i = 0; $i < $forums; $i ++)
{
	$document = new Forum();
	$document->set_owner_id(random_user());
	$document->set_title(random_string(2));
	$document->set_description(random_string(8));
	$document->set_path('/'.random_word().'/'.random_word());
	$document->set_filename(random_word());
	$document->set_filesize(rand(1000, 10000));
	$document->create();
}

for ($i = 0; $i < $links; $i ++)
{
	$link = new Link();
	$link->set_owner_id(random_user());
	$link->set_title(random_string(2));
	$link->set_description(random_string(8));
	$link->set_url('http://www.google.com/');
	$link->create();
}

for ($i = 0; $i < $student_publications; $i ++)
{
	$student_publication = new StudentPublication();
	$student_publication->set_owner_id(random_user());
	$student_publication->set_title(random_string(2));
	$student_publication->set_description(random_string(8));
	$student_publication->set_author(random_user());
	$student_publication->set_url('http://webs.hogent.be/~'.random_string(8).'/'.random_string(8).'.'.random_string(3));
	$student_publication->set_active(true);
	$student_publication->set_accepted(true);
	$student_publication->create();
}

function random_user()
{
	global $users;
	return rand(1, $users);
}

function random_string($length)
{
	$words = array ();
	for ($i = 0; $i < $length; $i ++)
	{
		$words[] = random_word();
	}
	return implode(' ', $words);
}

function random_word()
{
	$length = rand(4, 16);
	$str = '';
	for ($i = 0; $i < $length; $i ++)
	{
		$str .= chr(rand(97, 122));
	}
	return $str;
}
?>
<html>
<body>
<p>Tables filled.</p>
</body>
</html>