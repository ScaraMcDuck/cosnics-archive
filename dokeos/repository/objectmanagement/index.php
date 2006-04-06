<?php
$cidReset = true;
$langFile = 'admin';
require_once('../../claroline/inc/claro_init_global.inc.php');
require_once(api_get_library_path().'/formvalidator/FormValidator.class.php');
require_once('../lib/repositorydatamanager.class.php');
require_once('../lib/learningobjectform.class.php');
require_once('../lib/categorymenu.class.php');
require_once('../lib/treemenurenderer.class.php');
require_once('../lib/optionsmenurenderer.class.php');
require_once(api_get_library_path().'/text.lib.php');
require_once dirname(__FILE__).'/../lib/repositoryutilities.class.php';
if( !api_get_user_id())
{
	api_not_allowed();
}
/**
 * Get the condition to use when retrieving objects from the datamanager
 */
function get_condition()
{
	global $current_category_id;
	$condition1 = new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID,api_get_user_id());
	$condition2 = new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID,$current_category_id);
	$condition = new AndCondition($condition1,$condition2);
	if (isset ($_GET['keyword']))
	{
		$c = RepositoryUtilities :: query_to_condition($_GET['keyword']);
		if (!is_null($c)) {
			$condition = new AndCondition($condition, $c);
		}
	}
	return $condition;
}
/**
 * Get the total number of objects
 */
function get_number_of_objects()
{
	$datamanager = RepositoryDataManager::get_instance();
	return $datamanager->count_learning_objects(null,get_condition());
}
/**
 * Get the objects to display in the table
 */
function get_objects($from, $number_of_items, $column, $direction)
{
	global $current_category_id;
	$table_columns = array('id','type','title','description','modified','id');
	$datamanager = RepositoryDataManager::get_instance();
	$order_by[] = $table_columns[$column];
	$order_desc[] = $direction;
	$objects = $datamanager->retrieve_learning_objects(null,get_condition(),$order_by,$order_desc,$from,$number_of_items);
	$table_data = array();
	foreach($objects as $index => $object)
	{
		$row = array();
		$row[] = $object->get_id();
		$row[] = '<img src="'.api_get_path(WEB_CODE_PATH).'img/'.$object->get_type().'.gif" alt="'.$object->get_type().'"/>';
		if($object->get_type() == 'category')
		{
			$row[] = '<a href="index.php?category='.$object->get_id().'">'.$object->get_title().'</a>';
		}
		else
		{
			$row[] = '<a href="view.php?id='.$object->get_id().'">'.$object->get_title().'</a>';
		}
		$row[] = $object->get_description();
		$row[] = date('Y-m-d, H:i', is_null($object->get_modification_date()) ? $object->get_creation_date() : $object->get_modification_date());
		$modify = '<a href="edit.php?id='.$object->get_id().'" title="'.get_lang('Edit').'"><img src="'.api_get_path(WEB_CODE_PATH).'img/edit.gif" alt="'.get_lang('Edit').'"/></a>';
 		$modify .= '<a href="index.php?category='.$current_category_id.'&amp;action=delete&amp;id='.$object->get_id().'" title="'.get_lang('Delete').'"  onclick="javascript:if(!confirm(\''.addslashes(htmlentities(get_lang("ConfirmYourChoice"))).'\')) return false;"><img src="'.api_get_path(WEB_CODE_PATH).'img/delete.gif" alt="'.get_lang('Delete').'"/></a>';
 		$modify .= '<a href="index.php?category='.$current_category_id.'&amp;action=move&amp;id='.$object->get_id().'" title="'.get_lang('Move').'"><img src="'.api_get_path(WEB_CODE_PATH).'img/move.gif" alt="'.get_lang('Move').'"/></a>';
 		$modify .= '<a href="metadata.php?id='.$object->get_id().'" title="'.get_lang('Metadata').'"><img src="'.api_get_path(WEB_CODE_PATH).'img/info_small.gif" alt="'.get_lang('Metadata').'"/></a>';
 		$modify .= '<a href="rights.php?id='.$object->get_id().'" title="'.get_lang('Rights').'"><img src="'.api_get_path(WEB_CODE_PATH).'img/group_small.gif" alt="'.get_lang('Rights').'"/></a>';

 		$row[] = $modify;
 		$table_data[] = $row;
	}
	return $table_data;
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
// Retrieve learning objecttypes
$object_types = $datamanager->get_registered_types();
$type_choices = array();
foreach($object_types as $key => $type)
{
	$type_choices[$type] = get_lang($type);
}
$tool_name = get_lang('MyRepository');
// Create a search-box
$search_form = new FormValidator('search_simple','get','search.php','',null,false);
$renderer =& $search_form->defaultRenderer();
$renderer->setElementTemplate('<span>{element}</span> ');
$search_form->addElement('hidden','action','simple_search');
$search_form->addElement('text','keyword',get_lang('keyword'));
$search_form->addElement('submit','submit',get_lang('Search'));
// Create a dropdownlist with learning objecttypes
$create_form = new FormValidator('type_list', 'post', 'create.php');
$renderer =& $create_form->defaultRenderer();
$renderer->setElementTemplate('<span>{element}</span> ');
$create_form->addElement('hidden', 'category',$current_category_id);
$create_form->addElement('select','type',null,$type_choices);
$create_form->addElement('submit','submit',get_lang('Create'));
// Create a navigation menu to browse through the categories
create_category_menu();
// Create a sortable table to display the learning objects
$table = new SortableTable('objects','get_number_of_objects','get_objects');
$parameters = array();
$parameters['category'] = $current_category_id;
if (isset ($_GET['keyword']))
{
	$parameters = array ('keyword' => $_GET['keyword']);
}
$table->set_additional_parameters($parameters);
$column = 0;
$table->set_header($column++,'');
$table->set_header($column++,get_lang('Type'));
$table->set_header($column++,get_lang('Title'));
$table->set_header($column++,get_lang('Description'));
$table->set_header($column++,get_lang('LastModified'));
$table->set_header($column++,get_lang('Modify'),false);
$actions['delete_selected'] = get_lang('Delete');
$actions['move_selected'] = get_lang('Move');
$table->set_form_actions($actions);

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
$create_form->display();
echo '</div><div style="float:right;width:40%;text-align:right;margin:5px;">';
// Display search form
$search_form->display();
// Display message if needed
echo '</div><div style="float:left;width:20%;">';
// Display menu
$renderer =& new TreeMenuRenderer();
$menu->render($renderer,'sitemap');
echo $renderer->toHtml();
echo '</div><div style="float:right;width:80%;">';
// Display table
$table->display();
// Link to quota-page
echo '<a href="quota.php" style="float:right;"><img src="'.api_get_path(WEB_CODE_PATH).'/img/statistics.gif" style="vertical-align:middle;">'.get_lang('Quota').'</a>';
echo '</div>';
// Display footer
Display::display_footer();

function create_category_menu ()
{
	global $menu, $current_category_id;
	$menu = new CategoryMenu(api_get_user_id(),$current_category_id,'?category=%s',true);
}
?>