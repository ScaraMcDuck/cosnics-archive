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
		header('Location: index.php');
	}
	else
	{
		Display::display_header('Create');
		$form->display();
		Display::display_footer();
	}
}
?>