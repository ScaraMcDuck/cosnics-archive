<?php
/**
 * Create new learning objects
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
// Check if a learning object type is requested
if( isset($_REQUEST['type']))
{
	$type = $_REQUEST['type'];
	$current_category_id = $_REQUEST['parent'];
	$form = LearningObjectForm::factory($type,'create','post','create.php?type='.$type);
	$form->set_default_category($current_category_id);
	$form->build_creation_form();
	// If form validates, create learning object
	if($form->validate())
	{
		$object = $form->create_learning_object(api_get_user_id());
		header('Location: index.php?parent='.$current_category_id.'&action=show_message&message='.urlencode(get_lang('ObjectCreated')));
	}
	// Else, show the form
	else
	{
		$menu = new CategoryMenu(api_get_user_id(),$current_category_id,'index.php?parent=%s');
		$interbredcrump = $menu->get_breadcrumbs();
		Display::display_header(get_lang('Create'));
		$form->display();
		Display::display_footer();
	}
}
?>