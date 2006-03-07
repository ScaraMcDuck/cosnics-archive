<?php
require_once('../../claroline/inc/claro_init_global.inc.php');
require_once('../lib/datamanager.class.php');
require_once('../lib/learningobject_display.class.php');
if( !api_get_user_id())
{
	api_not_allowed();
}
if( isset($_GET['id']))
{
	$datamanager = DataManager::get_instance();
	$object = $datamanager->retrieve_learning_object($_GET['id']);
	$display = LearningObjectDisplay::factory($object);
	Display::display_header($object->get_title());
	echo $display->get_full_html();
	Display::display_footer();
}
?>