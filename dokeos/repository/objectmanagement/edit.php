<?php
/**
 * Edit existing learning objects
 * @package repository.objectmanagement
 */
require_once dirname(__FILE__).'/../../claroline/inc/claro_init_global.inc.php';
require_once api_get_library_path().'/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../lib/learningobjectform.class.php';
if( !api_get_user_id())
{
	api_not_allowed();
}
// Check if id is set
if( isset($_GET['id']))
{
	$datamanager = RepositoryDataManager::get_instance();
	$object = $datamanager->retrieve_learning_object($_GET['id']);
	//TODO: this should change to roles & rights stuff
	if($object->get_owner_id() != api_get_user_id())
	{
		api_not_allowed();
	}
	$form = LearningObjectForm::factory($object->get_type(),'edit','post','edit.php?id='.$object->get_id());
	$form->build_editing_form($object);
	// If form validates, update the learning object
	if($form->validate())
	{
		$success = $form->update_learning_object($object);
		$current_category_id = $object->get_parent_id();
		header('Location: index.php?category='.$current_category_id.'&action=show_message&message='.urlencode(get_lang($success ? 'ObjectUpdated' : 'ObjectUpdateFailed')));
	}
	// Else, show the form
	else
	{
		// Create a navigation menu to browse through the categories
		$current_category_id = $object->get_parent_id();
		$menu = new CategoryMenu(api_get_user_id(),$current_category_id,'index.php?category=%s');
		$interbredcrump = $menu->get_breadcrumbs();
		$tool_name = get_lang('Edit').': '.$object->get_title();
		// Display page
		Display::display_header($tool_name);
		api_display_tool_title($tool_name);
		$form->display();
		Display::display_footer();
	}
}
?>