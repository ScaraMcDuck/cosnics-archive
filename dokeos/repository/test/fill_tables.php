<?php


/**
==============================================================================
 *	This is a simple test script that removes all learning objects from the
 *	LCMS data source and fills it with garbage data. For testing purposes
 *	only.
 * 
 *	@author Tim De Pauw
==============================================================================
 */

require_once dirname(__FILE__).'/../lib/repositorydatamanager.class.php';

$users = 1;

$max_categories = array (10, 10, 10, 10);

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

$dataManager = RepositoryDataManager :: get_instance();

$dataManager->delete_all_learning_objects();

for ($u = 1; $i <= $users; $i ++)
{
	create_category($u);
}

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

function create_category($owner, $parent = 0, $level = 0)
{
	global $max_categories;
	$cat = new Category();
	$cat->set_owner_id($owner);
	$cat->set_parent_category_id($parent);
	$cat->set_title(random_string(2));
	$cat->set_description(random_string(8));
	$id = $cat->create();
	if (!$max_categories[$level])
	{
		return;
	}
	$count = rand(1, $max_categories[$level]);
	for ($i = 0; $i < $count; $i ++)
	{
		create_category($owner, $id, $level + 1);
	}
	return $id;
}
?>
<html>
<body>
<p>Tables filled.</p>
</body>
</html>