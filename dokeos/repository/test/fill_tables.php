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

$max_categories = array (5, 3, 0, 0);

$announcements = rand(2,10);
$calendar_events = rand(2,10);
$documents = rand(2,10);
$links = rand(2,10);
$student_publications = rand(2,10);

// TODO
/*
$forums = rand(100,500);
$forum_posts = rand(100,500);
$forum_topics = rand(100,500);
$learnpaths = rand(100,500);
*/

$dataManager = RepositoryDataManager :: get_instance();

$dataManager->delete_all_learning_objects();

for ($u = 1; $u <= $users; $u ++)
{
	create_category($u);
}

for ($i = 0; $i < $announcements; $i ++)
{
	$user = random_user();
	$announcement = new Announcement();
	$announcement->set_owner_id($user);
	$announcement->set_title(random_string(2));
	$announcement->set_description(random_string(8));
	$announcement->set_category_id(random_category($user));
	$announcement->create();
}

for ($i = 0; $i < $calendar_events; $i ++)
{
	$user = random_user();
	$event = new CalendarEvent();
	$event->set_owner_id($user);
	$event->set_title(random_string(2));
	$event->set_description(random_string(8));
	$event->set_category_id(random_category($user));
	$event->create();
}

for ($i = 0; $i < $documents; $i ++)
{
	$user = random_user();
	$document = new Document();
	$document->set_owner_id($user);
	$document->set_title(random_string(2));
	$document->set_description(random_string(8));
	$document->set_path('/'.random_word().'/'.random_word());
	$document->set_filename(random_word());
	$document->set_filesize(rand(1000, 10000));
	$document->set_category_id(random_category($user));
	$document->create();
}

for ($i = 0; $i < $forums; $i ++)
{
	$user = random_user();
	$document = new Forum();
	$document->set_owner_id($user);
	$document->set_title(random_string(2));
	$document->set_description(random_string(8));
	$document->set_path('/'.random_word().'/'.random_word());
	$document->set_filename(random_word());
	$document->set_filesize(rand(1000, 10000));
	$document->set_category_id(random_category($user));
	$document->create();
}

for ($i = 0; $i < $links; $i ++)
{
	$user = random_user();
	$link = new Link();
	$link->set_owner_id($user);
	$link->set_title(random_string(2));
	$link->set_description(random_string(8));
	$link->set_url(random_url());
	$link->set_category_id(random_category($user));
	$link->create();
}

for ($i = 0; $i < $student_publications; $i ++)
{
	$user = random_user();
	$student_publication = new StudentPublication();
	$student_publication->set_owner_id($user);
	$student_publication->set_title(random_string(2));
	$student_publication->set_description(random_string(8));
	$student_publication->set_author(random_user());
	$student_publication->set_url(random_url());
	$student_publication->set_active(true);
	$student_publication->set_accepted(true);
	$student_publication->set_category_id(random_category($user));
	$student_publication->create();
}

function random_url()
{
	return 'http://webs.hogent.be/~'.random_word(8).'/'.str_replace(' ', '%20', random_string(2)).'.'.random_word(3);
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

function random_word($length = 0)
{
	if ($length <= 0)
	{
		$length = rand(4, 16);
	}
	$str = '';
	for ($i = 0; $i < $length; $i ++)
	{
		$str .= chr(rand(97, 122));
	}
	return $str;
}

function random_category($owner)
{
	global  $created_categories;
	return  $created_categories[$owner][rand(0,count($created_categories[$owner])-1)];
}

function create_category($owner, $parent = 0, $level = 0)
{
	global $max_categories, $created_categories;
	$cat = new Category();
	$cat->set_owner_id($owner);
	$cat->set_category_id($parent);
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
		create_category($owner, $id, $level +1);
	}
	$created_categories[$owner][] = $id;
}
?>
<html>
<body>
<p>Tables filled.</p>
</body>
</html>