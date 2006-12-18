<?php
$langFile = 'repository';
require_once dirname(__FILE__).'/../../main/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/../lib/repositorydatamanager.class.php';

set_time_limit(0);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
/**
 * This is a simple test script that removes all learning objects from the LCMS
 * data source and fills it with garbage data. For testing purposes only.
 *
 * @author Tim De Pauw
 * @package repository
 */


$users = 1;

$max_categories = array (3,2,1);

$announcements = rand(2, 10);
$calendar_events = rand(2, 10);
$documents = rand(2, 10);
$links = rand(5, 1);
$forums = rand(1,5);
$forum_topics = rand(1,5);
$forum_posts = rand(6,12);

// TODO
//$learning_paths = rand(100,500);

$randomTexts[] = 'Lorem ipsum dolor sit amet consectetuer adipiscing elit Cras vel erat Phasellus est Curabitur nunc leo laoreet eu varius sit amet faucibus faucibus magna Quisque venenatis ante quis dictum sodales orci velit molestie';
$randomTexts[] = 'На берегу пустынных волн Стоял он дум великих полн И вдаль глядел Пред ним широко Река неслася; бедный чёлн По ней стремился одиноко По мшистым топким берегам Чернели избы здесь и там Приют убогого чухонца И лес неведомый лучам В тумане спрятанного солнца Кругом шумел';
$randomTexts[] = 'Σὲ γνωρίζω ἀπὸ τὴν κόψη τοῦ σπαθιοῦ τὴν τρομερή σὲ γνωρίζω ἀπὸ τὴν ὄψη ποὺ μὲ βία μετράει τὴ γῆ ᾿Απ᾿ τὰ κόκκαλα βγαλμένη τῶν ῾Ελλήνων τὰ ἱερά  καὶ σὰν πρῶτα ἀνδρειωμένη χαῖρε ὦ χαῖρε ᾿Ελευθεριά!';
$randomTexts[] = 'สิบสองกษัตริย์ก่อนหน้าแลถัดไป สององค์ไซร้โง่เขลาเบาปัญญา ทรงนับถือขันทีเป็นที่พึ่ง บ้านเมืองจึงวิปริตเป็นนักหนา  โฮจิ๋นเรียกทัพทั่วหัวเมืองมา หมายจะฆ่ามดชั่วตัวสำคัญ เหมือนขับไสไล่เสือจากเคหา รับหมาป่าเข้ามาเลยอาสัญ ฝ่ายอ้องอุ้นยุแยกให้แตกกัน ใช้สาวนั้นเป็นชนวนชื่นชวนใจ พลันลิฉุยกุยกีกลับก่อเหตุ ช่างอาเพศจริงหนาฟ้าร้องไห้ ต้องรบราฆ่าฟันจนบรรลัย ฤๅหาใครค้ำชูกู้บรรลังก์ ฯ';
$randomTexts[] = 'Jeg kan spise glas det gør ikke ondt på mig';
$randomTexts[] = 'मैं काँच खा सकता हूँ मुझे उस से कोई पीडा नहीं होती';
$randomTexts[] = '私はガラス を食べられます れは 私を傷つけ ません';
$words = array();
foreach($randomTexts as $index => $randomText)
{
	$words = array_merge($words,explode(' ', $randomText));
}
$dataManager = RepositoryDataManager :: get_instance();

$dataManager->delete_all_learning_objects();
title('Categories');
for ($u = 1; $u <= $users; $u ++)
{
	create_category($u);
}
title('Announcements');
for ($i = 0; $i < $announcements; $i ++)
{
	$user = random_user();
	$announcement = new Announcement();
	$announcement->set_owner_id($user);
	$announcement->set_title(random_string(2));
	$announcement->set_description(random_string(8));
	$announcement->set_parent_id(random_category($user));
	$announcement->create();
	progress();
}
title('Calendar Events');
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
	progress();
}
title('Documents');
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
	progress();
}
title('Links');
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
	progress();
}
title('Forums');
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
	progress();
}
title('Forum Topics');
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
	progress();
}
title('Forum Posts');
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
	progress();
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
	return 'http://www.example.com/~'.totally_random_word(8).'/'.str_replace(' ', '%20', random_string(2)).'.'.totally_random_word(3);
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
		progress();
	}
	$created_categories[$owner][] = $id;
}
function title($title)
{
	echo '<br /><strong>'.$title.'</strong>';
}
function progress()
{
	echo ' =';
	flush();
}
?>