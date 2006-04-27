<?php
/**
 * Overview of personal learning object repository
 * @package repository.objectmanagement
 */
$cidReset = true;
$langFile = 'admin';
require_once dirname(__FILE__).'/../../claroline/inc/claro_init_global.inc.php';
require_once api_get_library_path().'/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../lib/learningobjectform.class.php';
require_once dirname(__FILE__).'/../lib/categorymenu.class.php';
require_once dirname(__FILE__).'/../lib/treemenurenderer.class.php';
require_once dirname(__FILE__).'/../lib/optionsmenurenderer.class.php';
require_once api_get_library_path().'/text.lib.php';
require_once dirname(__FILE__).'/../lib/repositoryutilities.class.php';
require_once dirname(__FILE__).'/../lib/repositorybrowsertable.class.php';

if( !api_get_user_id())
{
	api_not_allowed();
}

$object = get_property();

// Create a navigation menu to browse through the categories
create_category_menu();

// Perform actions if needed
if(isset($_GET['action']))
{
	switch($_GET['action'])
	{
		case 'show_message':
			$message = $_GET['message'];
			break;
		case 'delete':
			$object = get_datamanager()->retrieve_learning_object($_GET['id']);
			if(get_datamanager()->learning_object_can_be_deleted($object))
			{
				$object->delete();
				$message = get_lang('ObjectDeleted');
				create_category_menu();
			}
			else
			{
				$message = get_lang('OperationNotAllowed');
			}
			break;
		case 'move':
			$renderer =& new OptionsMenuRenderer($_GET['id']);
			$menu->render($renderer,'sitemap');
			$category_choices = $renderer->toArray('id');
			$popup_form = new FormValidator('move_form','get');
			$popup_form->addElement('hidden','id',$_GET['id']);
			$popup_form->addElement('hidden','action','move');
			$popup_form->addElement('select',RepositoryBrowserTable::PARAM_PARENT_ID,get_lang('Category'),$category_choices);
			$popup_form->addElement('submit','submit',get_lang('Ok'));
			if($popup_form->validate())
			{
				$values = $popup_form->exportValues();
				$object = get_datamanager()->retrieve_learning_object($_GET['id']);
				$object->set_parent_id($values[RepositoryBrowserTable::PARAM_PARENT_ID]);
				$object->update(false);
				$message = get_lang('ObjectMoved');
				create_category_menu();
			}
			else
			{
				$message = get_lang('SelectCategory');
				$message .= $popup_form->toHtml();
			}
			break;
	}
}
if(isset($_POST['action']))
{
	switch($_POST['action'])
	{
		case 'delete_selected':
			$deleted_all = true;
			foreach($_POST['id'] as $index => $object_id)
			{
				$object = get_datamanager()->retrieve_learning_object($object_id);
				if(get_datamanager()->learning_object_can_be_deleted($object))
				{
					$object->delete();
				}
				else
				{
					$deleted_all = false;
				}
			}
			create_category_menu();
			$message = $deleted_all ? get_lang('ObjectsDeleted') : get_lang('NotAllObjectsDeleted');
			break;
		case 'move_selected':
			$condition = new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID,api_get_user_id());
			$renderer =& new OptionsMenuRenderer();
			$menu->render($renderer,'sitemap');
			$category_choices = $renderer->toArray('id',$_POST['id']);
			$popup_form = new FormValidator('move_form','post');
			foreach($_POST['id'] as $index => $object_id)
			{
				$popup_form->addElement('hidden','id[]',$object_id);
			}
			$popup_form->addElement('hidden','action','move_selected');
			$popup_form->addElement('select',RepositoryBrowserTable::PARAM_PARENT_ID,get_lang('Category'),$category_choices);
			$popup_form->addElement('submit','submit',get_lang('Ok'));
			if($popup_form->validate())
			{
				$values = $popup_form->exportValues();
				foreach($_POST['id'] as $index => $object_id)
				{
					$object = get_datamanager()->retrieve_learning_object($object_id);
					$object->set_parent_id($values[RepositoryBrowserTable::PARAM_PARENT_ID]);
					$object->update(false);
				}
				$message = get_lang('ObjectsMoved');
			}
			else
			{
				$message = get_lang('SelectCategory');
				$message .= $popup_form->toHtml();
			}
			break;
	}
}


/*
 * Display page
 */
$interbredcrump = $menu->get_breadcrumbs();
$current_location = array_pop($interbredcrump);
// Display header
Display::display_header($current_location['name']);
if(isset($message))
{
	Display::display_normal_message($message);
}
api_display_tool_title($current_location['name']);
echo '<div style="float:left;width:40%;margin:5px;">';
// Display create form
create_learning_object_list();
echo '</div><div style="float:right;width:40%;text-align:right;margin:5px;">';
// Display search form
create_search_form();
// Display message if needed
echo '</div><div style="float:left;width:20%;">';
// Display menu
$renderer =& new TreeMenuRenderer();
$menu->render($renderer,'sitemap');
echo $renderer->toHtml();
echo '</div><div style="float:right;width:80%;">';
// Display table
create_repository_table();
// Link to quota-page
echo '<a href="quota.php" style="float:right;"><img src="'.api_get_path(WEB_CODE_PATH).'/img/statistics.gif" style="vertical-align:middle;">'.get_lang('Quota').'</a>';
echo '</div>';
// Display footer
Display::display_footer();

/**
 * Create a search-box
 */
function create_search_form()
{
	$search_form = new FormValidator('search_simple','get','search.php','',null,false);
	$renderer = $search_form->defaultRenderer();
	$renderer->setElementTemplate('<span>{element}</span> ');
	//$search_form->addElement('hidden','action','simple_search');
	$search_form->addElement('hidden','searchtype','simple_search');
	$search_form->addElement('text',RepositoryBrowserTable::PARAM_KEYWORD,get_lang('keyword'));
	$search_form->addElement('submit','submit',get_lang('Search'));
	$search_form->display();
}
/**
 * Create a dropdownlist with learning objecttypes
 */
function create_learning_object_list()
{
	$create_form = new FormValidator('type_list', 'post', 'create.php');
	$renderer = $create_form->defaultRenderer();
	$renderer->setElementTemplate('<span>{element}</span> ');
	$create_form->addElement('hidden', RepositoryBrowserTable::PARAM_PARENT_ID,get_current_category());
	$create_form->addElement('select',RepositoryBrowserTable::PARAM_TYPE,null,retrieve_learning_object_types());
	$create_form->addElement('submit','submit',get_lang('Create'));
	$create_form->display();
}
/**
 * Create a repository table
 */
function create_repository_table()
{
	$property = get_property();
	if(!is_null($property))
	{
		$object = get_datamanager()->retrieve_learning_object($property);
		$table = new RepositoryBrowserTable($object);
	}
	else
	{
		$parameters[RepositoryBrowserTable::PARAM_TYPE] = $_GET[RepositoryBrowserTable::PARAM_TYPE];
		$table = new RepositoryBrowserTable($parameters);
	}
	$table->display();
}

/**
 * Retrieve learning objecttypes
 */
function retrieve_learning_object_types()
{
	$object_types = get_datamanager()->get_registered_types();
	$type_choices = array();
	foreach($object_types as $key => $type)
	{
		$type_choices[$type] = get_lang($type);
	}
	return $type_choices;
}
/**
 * Create a category menu
 */
function create_category_menu ()
{
	global $menu;
	$menu = new CategoryMenu(api_get_user_id(),get_current_category(),'?'.RepositoryBrowserTable::PARAM_PARENT_ID.'=%s',true);
}
/**
 * Load datamanager
 */
function get_datamanager()
{
	return RepositoryDataManager::get_instance();
}

/**
 * Load current category
 */
function get_current_category()
{
	$current_category_id = isset($_GET[RepositoryBrowserTable::PARAM_PARENT_ID]) ? intval($_GET[RepositoryBrowserTable::PARAM_PARENT_ID]) : NULL;
	if($current_category_id <= 0)
	{
		$root_category = get_datamanager()->retrieve_root_category(api_get_user_id());
		$current_category_id = $root_category->get_id();
	}
	return $current_category_id;
}
/**
 * Load used property
 */
function get_property()
{
	if(isset($_GET[RepositoryBrowserTable::PARAM_TYPE]))
	{
		$current_property = NULL;
	}
	else
	{
		$current_property = get_current_category();
		if(get_datamanager()->retrieve_learning_object($current_property)->get_owner_id() != api_get_user_id())
		{
			api_not_allowed();
		}
	}
	return $current_property;
}
?>