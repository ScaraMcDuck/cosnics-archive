<?php
require_once('../../claroline/inc/claro_init_global.inc.php');
require_once(api_get_library_path().'/formvalidator/FormValidator.class.php');
require_once('../lib/repositorydatamanager.class.php');
require_once('../lib/learningobject_form.class.php');
require_once('../lib/learning_object/announcement/form.class.php');
Display::display_header('Create');
if( !api_get_user_id())
{
	api_not_allowed();
}
if( isset($_REQUEST['type']))
{
	$type = $_REQUEST['type'];
	$current_category = $_REQUEST['category_id'];
	$form = LearningObjectForm::factory($type,'create','post','create.php?type='.$type);
	$form->set_default_category($current_category);
	$form->build_create_form();
	if($form->validate())
	{
		$object = $form->create_learning_object(api_get_user_id());
		var_dump($object);	
	}
	else
	{
		$form->display();
	}
}
Display::display_footer();
?>