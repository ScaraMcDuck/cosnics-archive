<?php
/**
 * View a learning objects
 * @package repository.objectmanagement
 */
require_once('../../claroline/inc/claro_init_global.inc.php');
require_once('../lib/repositorydatamanager.class.php');
require_once('../lib/learningobjectdisplay.class.php');
require_once('../lib/categorymenu.class.php');
if( !api_get_user_id())
{
	api_not_allowed();
}
if( isset($_GET['id']))
{
	$datamanager = RepositoryDataManager::get_instance();
	$object = $datamanager->retrieve_learning_object($_GET['id']);
	//TODO: this should change to roles & rights stuff
	if($object->get_owner_id() != api_get_user_id())
	{
		api_not_allowed();
	}
	$display = LearningObjectDisplay::factory($object);
	// Create a navigation menu to browse through the categories
	$current_category_id = $object->get_parent_id();
	$menu = new CategoryMenu(api_get_user_id(),$current_category_id,'index.php?category=%s');
	$interbredcrump = $menu->get_breadcrumbs();
	Display::display_header($object->get_title());
	api_display_tool_title(get_lang('LearningObjectDetails'));
	echo '<p>';
	echo '<a href="edit.php?id='.$object->get_id().'" title="'.get_lang('Edit').'"><img src="'.api_get_path(WEB_CODE_PATH).'img/edit.gif" alt="'.get_lang('Edit').'"/> '.get_lang('Edit').'</a>';
	echo '</p>';
	echo $display->get_full_html();
	$publication_attr = $datamanager->get_learning_object_publication_attributes($object->get_id());
	if(count($publication_attr) > 0)
	{
		echo '<br/><strong>'.get_lang('ObjectPublished').'</strong>';
		echo '<ul>';
		foreach($publication_attr as $index => $info)
		{
			$publisher = api_get_user_info($info->get_publisher_user_id());
			echo '<li>';
			//TODO: date formatting
			echo $info->get_application().': '.$info->get_location().' ('.$publisher['firstName'].' '.$publisher['lastName'].', '.date('r',$info->get_publication_date()).')';
			echo '</li>';
		}
		echo '</ul>';
	}
	Display::display_footer();
}
?>