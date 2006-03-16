<?php
require_once('../../claroline/inc/claro_init_global.inc.php');
require_once(api_get_library_path().'/formvalidator/FormValidator.class.php');
require_once('../lib/repositorydatamanager.class.php');
require_once('../lib/learningobject_form.class.php');
if( !api_get_user_id())
{
	api_not_allowed();
}
if( isset($_GET['id']))
{
	$datamanager = RepositoryDataManager::get_instance();
	$object = $datamanager->retrieve_learning_object($_GET['id']);
	$form = LearningObjectForm::factory($object->get_type(),'edit','post','edit.php?id='.$object->get_id());
	$form->build_edit_form($object);
	if($form->validate())
	{
		$form->update_learning_object($object);
		$current_category_id = $object->get_parent_id();
		header('Location: index.php?category='.$current_category_id.'&action=show_message&message='.urlencode(get_lang('ObjectEdited')));
	}
	else
	{
		// Create a navigation menu to browse through the categories
		$current_category_id = $object->get_parent_id();
		$menu = new CategoryMenu(api_get_user_id(),$current_category_id,'index.php?category=%s');
		$interbredcrump = $menu->get_breadcrumbs();
		$tool_name = get_lang('Edit').': '.$object->get_title();
		Display::display_header($tool_name);
		api_display_tool_title($tool_name);
		$form->display();
		Display::display_footer();
	}
}
?>