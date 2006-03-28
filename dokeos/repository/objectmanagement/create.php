<?php
require_once('../../claroline/inc/claro_init_global.inc.php');
require_once(api_get_library_path().'/formvalidator/FormValidator.class.php');
require_once('../lib/repositorydatamanager.class.php');
require_once('../lib/learningobject_form.class.php');
require_once('../lib/learning_object/announcement/form.class.php');
if( !api_get_user_id())
{
	api_not_allowed();
}
if( isset($_REQUEST['type']))
{
	$type = $_REQUEST['type'];
	$current_category_id = $_REQUEST['category'];
	$form = LearningObjectForm::factory($type,'create','post','create.php?type='.$type);
	$form->set_default_category($current_category_id);
	$form->build_creation_form();
	if($form->validate())
	{
		$object = $form->create_learning_object(api_get_user_id());
		header('Location: index.php?category='.$current_category_id.'&action=show_message&message='.urlencode(get_lang('ObjectCreated')));
	}
	else
	{
		$menu = new CategoryMenu(api_get_user_id(),$current_category_id,'index.php?category=%s');
		$interbredcrump = $menu->get_breadcrumbs();
		Display::display_header(get_lang('Create'));
		$form->display();
		Display::display_footer();
	}
}
?>