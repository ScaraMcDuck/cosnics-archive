<?php
require_once dirname(__FILE__).'/../../claroline/inc/claro_init_global.inc.php';
require_once api_get_library_path().'/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../lib/learningobjectform.class.php';
require_once dirname(__FILE__).'/../lib/learningobjectdisplay.class.php';
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
	$tool_name = get_lang('Metadata').': '.$object->get_title();
	Display::display_header($tool_name);
	api_display_tool_title($tool_name);
	echo $display->get_full_html();
	//TODO: connect current metadata implementation in Dokeos to this repository-system
	echo '<p><b>TODO: Here you can edit the metadata of the selected object...</b></p>';
	Display::display_footer();
}
?>