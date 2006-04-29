<?php
/**
 * Create new learning objects
 * @package repository.objectmanagement
 */
require_once dirname(__FILE__).'/../../claroline/inc/claro_init_global.inc.php';
require_once api_get_library_path().'/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../lib/learningobjectform.class.php';
require_once dirname(__FILE__).'/../repository/lib/abstractlearningobject.class.php';
if( !api_get_user_id())
{
	api_not_allowed();
}
// Check if a learning object type is requested
if( isset($_REQUEST['type']))
{
	$type = $_REQUEST['type'];
	$current_category_id = $_REQUEST['parent'];
	$lo = new AbstractLearningObject($type, api_get_user_id(), $current_category);
	$form = LearningObjectForm::factory(LearningObjectForm::TYPE_CREATE,$lo,'create','post','create.php?type='.$type);
	// If form validates, create learning object
	if($form->validate())
	{
		$object = $form->create_learning_object();
		header('Location: index.php?parent='.$current_category_id.'&action=show_message&message='.urlencode(get_lang('ObjectCreated')));
	}
	// Else, show the form
	else
	{
		$menu = new LearningObjectCategoryMenu(api_get_user_id(),$current_category_id,'index.php?parent=%s');
		$interbredcrump = $menu->get_breadcrumbs();
		Display::display_header(get_lang('Create'));
		$form->display();
		Display::display_footer();
	}
}
?>