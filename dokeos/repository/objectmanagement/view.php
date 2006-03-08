<?php
require_once('../../claroline/inc/claro_init_global.inc.php');
require_once('../lib/repositorydatamanager.class.php');
require_once('../lib/learningobject_display.class.php');
if( !api_get_user_id())
{
	api_not_allowed();
}
if( isset($_GET['id']))
{
	$datamanager = RepositoryDataManager::get_instance();
	$object = $datamanager->retrieve_learning_object($_GET['id']);
	$display = LearningObjectDisplay::factory($object);
	$interbredcrump[] = array ("url" => "index.php", "name" => get_lang('MyLearningObjects'));
	Display::display_header($object->get_title());
	api_display_tool_title(get_lang('LearningObjectDetails'));
	echo '<p>';
	echo '<a href="edit.php?id='.$object->get_id().'" title="'.get_lang('Edit').'"><img src="'.api_get_path(WEB_CODE_PATH).'img/edit.gif" alt="'.get_lang('Edit').'"/> '.get_lang('Edit').'</a>';
	echo '</p>';
	echo $display->get_full_html();
	Display::display_footer();
}
?>