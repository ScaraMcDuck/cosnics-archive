<?php
// TODO: Share learning object table display code between index.php and search.php. 

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

// Load datamanager
$datamanager = RepositoryDataManager::get_instance();

// Load category
$current_category_id = isset($_GET['category']) ? intval($_GET['category']) : NULL;
if($current_category_id <= 0)
{
	$root_category = $datamanager->retrieve_root_category(api_get_user_id());
	$current_category_id = $root_category->get_id();
}
$object = $datamanager->retrieve_learning_object($current_category_id);

if($object->get_owner_id() != api_get_user_id())
{
	api_not_allowed();
}

$tool_name = get_lang('MyLearningObjects');

//$tool_name = get_lang('MyRepository');


// Create a navigation menu to browse through the categories

create_category_menu();

// Create a sortable table to display the learning objects



// Perform actions if needed
if(isset($_GET['action']))
{
	switch($_GET['action'])
	{
		case 'show_message':
			$message = $_GET['message'];
			break;
		case 'delete':
			$object = $datamanager->retrieve_learning_object($_GET['id']);
			$object->delete();
			$message = get_lang('ObjectDeleted');
			// re-initialize the menu
			create_category_menu();
			break;
		case 'move':
			$renderer =& new OptionsMenuRenderer();
			$menu->render($renderer,'sitemap');
			$category_choices = $renderer->toArray('id',$_GET['id']);
			$popup_form = new FormValidator('move_form','get');
			$popup_form->addElement('hidden','id',$_GET['id']);
			$popup_form->addElement('hidden','action','move');
			$popup_form->addElement('select','new_category',get_lang('Category'),$category_choices);
			$popup_form->addElement('submit','submit',get_lang('Ok'));
			if($popup_form->validate())
			{
				$values = $popup_form->exportValues();
				$object = $datamanager->retrieve_learning_object($_GET['id']);
				$object->set_parent_id($values['new_category']);
				$object->update(false);
				$message = get_lang('ObjectMoved');
				// re-initialize the menu
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
			foreach($_POST['id'] as $index => $object_id)
			{
				$object = $datamanager->retrieve_learning_object($object_id);
				$object->delete();
			}
			$message = get_lang('ObjectsDeleted');
			// re-initialize the menu
			create_category_menu();
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
			$popup_form->addElement('select','category',get_lang('Category'),$category_choices);
			$popup_form->addElement('submit','submit',get_lang('Ok'));
			if($popup_form->validate())
			{
				$values = $popup_form->exportValues();
				foreach($_POST['id'] as $index => $object_id)
				{
					$object = $datamanager->retrieve_learning_object($object_id);
					$object->set_parent_id($values['category']);
					$object->update(false);
				}
				$message = get_lang('ObjectsMoved');
			}
			else
			{
				$message = get_lang('SelectCategory');
				$message .= $popup_form->toHtml();
			}
			// re-initialize the menu
			create_category_menu();
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

// Create a search-box
function create_search_form()
{
	$search_form = new FormValidator('search_simple','get','search.php','',null,false);
	$renderer =& $search_form->defaultRenderer();
	$renderer->setElementTemplate('<span>{element}</span> ');
	$search_form->addElement('hidden','action','simple_search');
	$search_form->addElement('text','keyword',get_lang('keyword'));
	$search_form->addElement('submit','submit',get_lang('Search'));
	$search_form->display();
}
// Create a dropdownlist with learning objecttypes
function create_learning_object_list()
{
	global $current_category_id;
	$create_form = new FormValidator('type_list', 'post', 'create.php');
	$renderer =& $create_form->defaultRenderer();
	$renderer->setElementTemplate('<span>{element}</span> ');
	$create_form->addElement('hidden', 'category',$current_category_id);
	$create_form->addElement('select','type',null,retrieve_learning_object_types());
	$create_form->addElement('submit','submit',get_lang('Create'));
	$create_form->display();
}

function create_repository_table()
{
	global $object;
	$table = new RepositoryBrowserTable($object);
	$table->set_additional_parameters(array('category' =>$object->get_id()));
	$table->display();
}

function create_category_menu ()
{
	global $menu, $current_category_id;
	$menu = new CategoryMenu(api_get_user_id(),$current_category_id,'?category=%s',true);
}
// Retrieve learning objecttypes
function retrieve_learning_object_types()
{
	global $datamanager;
	$object_types = $datamanager->get_registered_types();
	$type_choices = array();
	foreach($object_types as $key => $type)
	{
		$type_choices[$type] = get_lang($type);
	}
	return $type_choices;
}
?>