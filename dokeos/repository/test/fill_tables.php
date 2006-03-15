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

$announcements = rand(2, 10);
$calendar_events = rand(2, 10);
$documents = rand(2, 10);
$links = rand(2, 10);
$student_publications = rand(2, 10);

// TODO
/*
$forums = rand(100,500);
$forum_posts = rand(100,500);
$forum_topics = rand(100,500);
$learnpaths = rand(100,500);
*/

$randomText =<<<END
Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Cras vel erat.
Phasellus est. Curabitur nunc leo, laoreet eu, varius sit amet, faucibus
faucibus, magna. Quisque venenatis, ante quis dictum sodales, orci velit
molestie nisl, mollis adipiscing ante erat eget tellus. Etiam nec tellus
ut lacus sollicitudin commodo. Duis sed massa in ipsum pharetra scelerisque.
Cum sociis natoque penatibus et magnis dis parturient montes, nascetur
ridiculus mus. Donec quis elit. Maecenas suscipit pretium tortor. Mauris augue
ligula, molestie id, laoreet quis, sollicitudin eget, orci. Fusce luctus
auctor sem. Integer turpis. Curabitur id lectus. Aenean eget lectus. Donec
vitae nisl. Duis et tellus sed nunc congue sollicitudin. Vivamus sed ipsum a
magna accumsan convallis. Donec egestas tincidunt dolor. Donec ornare nonummy
lacus. Maecenas posuere. Vestibulum urna. Curabitur bibendum gravida pede.
Duis volutpat, sapien eu sagittis interdum, lectus risus tempus lacus, eu
consequat massa tortor et ligula. Nullam gravida fermentum elit. Nam venenatis
quam vel pede. Praesent sed metus. Proin at pede in odio fringilla euismod.
Quisque mattis, ante suscipit bibendum porttitor, risus pede hendrerit erat,
quis pellentesque turpis lacus et pede. Proin condimentum est at nulla. Nam
gravida. Suspendisse dapibus, enim non congue euismod, nulla ipsum mattis
felis, pulvinar commodo metus neque nec tortor. Donec luctus. Vestibulum
rhoncus. Pellentesque iaculis suscipit arcu.
END;

$words = preg_split('/\W+/', preg_replace(array ('/^\W+/', '/\W+$/'), array ('', ''), strtolower($randomText)));

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

//for ($i = 0; $i < $student_publications; $i ++)
//{
//	$user = random_user();
//	$student_publication = new StudentPublication();
//	$student_publication->set_owner_id($user);
//	$student_publication->set_title(random_string(2));
//	$student_publication->set_description(random_string(8));
//	$student_publication->set_author(random_user());
//	$student_publication->set_url(random_url());
//	$student_publication->set_active(true);
//	$student_publication->set_accepted(true);
//	$student_publication->set_category_id(random_category($user));
//	$student_publication->create();
//}

function random_url()
{
	return 'http://webs.hogent.be/~'.totally_random_word(8).'/'.str_replace(' ', '%20', random_string(2)).'.'.totally_random_word(3);
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

function totally_random_word($length)
{
	$str = '';
	for ($i = 0; $i < $length; $i ++)
	{
		$str .= chr(rand(97, 122));
	}
	return $str;
}

function random_word()
{
	global $words;
	return $words[rand(0, count($words) - 1)];
}

function random_category($owner)
{
	global $created_categories;
	return $created_categories[$owner][rand(0, count($created_categories[$owner]) - 1)];
}

function create_category($owner, $parent = 0, $level = 0)
{
	global $max_categories, $created_categories;
	$cat = new Category();
	$cat->set_owner_id($owner);
	$cat->set_category_id($parent);
	$cat->set_title($parent == 0 ? 'My Repository' : random_string(2));
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