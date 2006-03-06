<?php
require_once('../../claroline/inc/claro_init_global.inc.php');
require_once(api_get_library_path().'/formvalidator/FormValidator.class.php');
require_once('../lib/datamanager.class.php');
require_once('../lib/learningobject_form.class.php');
Display::display_header('Create');
if( !api_get_user_id())
{
	api_not_allowed();
}
if( isset($_GET['id']))
{
	$datamanager = DataManager::get_instance();
	$object = $datamanager->retrieve_learning_object($_GET['id']);
	$form = LearningObjectForm::factory($object->get_type(),'edit','post','edit.php?id='.$object->get_id());
	$form->build_edit_form();
	if($form->validate())
	{
		$form->update_learning_object();
	}
	else
	{
		$form->display();
	}
}
Display::display_footer();
?>
