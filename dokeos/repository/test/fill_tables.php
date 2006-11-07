<?php
/**
==============================================================================
 *	This is a simple test script that removes all learning objects from the
 *	LCMS data source and fills it with garbage data. For testing purposes
 *	only.
 *
 *	@author Tim De Pauw
 * @package repository
==============================================================================
 */

$langFile = 'repository';
require_once dirname(__FILE__).'/../../main/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/../lib/repositorydatamanager.class.php';

$users = 1;

$max_categories = array (5, 3, 3, 0);

$announcements = rand(2, 10);
$calendar_events = rand(2, 10);
$documents = rand(2, 10);
$links = rand(50, 100);
//$student_publications = rand(2, 10);

$forums = rand(10,50);
$forum_topics = rand(100,500);
$forum_posts = rand(1000,5000);

// TODO
//$learning_paths = rand(100,500);

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
	$announcement->set_parent_id(random_category($user));
	$announcement->create();
}

for ($i = 0; $i < $calendar_events; $i ++)
{
	$user = random_user();
	$event = new CalendarEvent();
	$event->set_owner_id($user);
	$event->set_title(random_string(2));
	$event->set_description(random_string(8));
	$event->set_parent_id(random_category($user));
	$start_date = rand(strtotime('-1 Month',time()),strtotime('+1 Month',time()));
	$end_date = rand($start_date+1,strtotime('+1 Month',$start_date));
	$event->set_start_date($start_date);
	$event->set_end_date($end_date);
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
	$document->set_parent_id(random_category($user));
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
	$link->set_parent_id(random_category($user));
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
//	$student_publication->set_parent_id(random_category($user));
//	$student_publication->create();
//}

$created_forums = array();
for ($i = 0; $i < $forums; $i++)
{
	$user = random_user();
	$forum = new Forum();
	$forum->set_owner_id($user);
	$forum->set_title(random_string(2));
	$forum->set_description(random_string(8));
	$forum->set_parent_id(random_category($user));
	$forum->create();
	$created_forums[] = $forum;
}

$created_forum_topics = array();
$topic_to_forum = array();
for ($i = 0; $i < $forum_topics; $i++)
{
	$forum = random_forum();
	$user = random_user();
	$topic = new ForumTopic();
	$topic->set_owner_id($user);
	$topic->set_title(random_string(2));
	$topic->set_description(random_string(8));
	$topic->set_parent_id($forum->get_id());
	$topic->create();
	$created_forum_topics[] = $topic;
	// Map topic to its forum object, for convenience.
	$topic_to_forum[$topic->get_id()] = $forum;
	// Every topic needs at least one post.
	$post = new ForumPost();
	$post->set_owner_id($user);
	$post->set_title(random_string(2));
	$post->set_description(random_string(8));
	$post->set_parent_id($topic->get_id());
	$post->create();
	// Update the topic.
	$topic->set_last_post_id($post->get_id());
	// Update the forum.
	$forum->set_topic_count($forum->get_topic_count() + 1);
	$forum->set_post_count($forum->get_post_count() + 1);
	$forum->set_last_post_id($post->get_id());
}

for ($i = 0; $i < $forum_posts - $forum_topics; $i++)
{
	$user = random_user();
	$topic = random_forum_topic();
	$post = new ForumPost();
	$post->set_owner_id($user);
	$post->set_title(random_string(2));
	$post->set_description(random_string(8));
	$post->set_parent_id($topic->get_id());
	$post->create();
	// Update the topic.
	$topic->set_reply_count($topic->get_reply_count() + 1);
	$topic->set_last_post_id($post->get_id());
	// Update the forum.
	$forum = $topic_to_forum[$topic->get_id()];
	$forum->set_topic_count($forum->get_topic_count() + 1);
	$forum->set_post_count($forum->get_post_count() + 1);
	$forum->set_last_post_id($post->get_id());
}

foreach ($created_forum_topics as $topic)
{
	$topic->update();
}

foreach ($created_forums as $forum)
{
	$forum->update();
}

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
	return random_array_element(& $words);
}

function random_category($owner)
{
	global $created_categories;
	return random_array_element(& $created_categories[$owner]);
}

function random_forum()
{
	global $created_forums;
	return random_array_element(& $created_forums);
}

function random_forum_topic()
{
	global $created_forum_topics;
	return random_array_element(& $created_forum_topics);
}

function random_array_element(& $array)
{
	return $array[rand(0, count($array) - 1)];
}

function create_category($owner, $parent = 0, $level = 0)
{
	global $max_categories, $created_categories;
	$cat = new Category();
	$cat->set_owner_id($owner);
	$cat->set_parent_id($parent);
	$cat->set_title($parent == 0 ? 'My Repository' : random_string(2));
	$cat->set_description(random_string(8));
	$cat->create();
	$id = $cat->get_id();
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