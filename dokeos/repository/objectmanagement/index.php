<?php
require_once('../../claroline/inc/claro_init_global.inc.php');
require_once(api_get_library_path().'/formvalidator/FormValidator.class.php');
require_once('../lib/repositorydatamanager.class.php');
require_once('../lib/learningobject_form.class.php');
require_once('../lib/learning_object/announcement/form.class.php');
require_once('../lib/categorymenu.class.php');
require_once(api_get_library_path().'/text.lib.php');
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
	$condition1 = new ExactMatchCondition('owner',api_get_user_id());
	$condition2 = new ExactMatchCondition('category',$current_category_id);
	$condition = new AndCondition($condition1,$condition2);
	if (isset ($_GET['keyword']))
	{
		$pattern = '*'.$_GET['keyword'].'*';
		$simple_search_conditions[] = new PatternMatchCondition('title',$pattern);
		$simple_search_conditions[] = new PatternMatchCondition('description',$pattern);
		$simple_search_condition = new OrCondition($simple_search_conditions);
		$condition = new AndCondition(array($condition,$simple_search_condition));
	}
	return $condition;
}
/**
 * Get the total number of objects
 */
function get_number_of_objects()
{
	$datamanager = RepositoryDataManager::get_instance();
	$objects = $datamanager->retrieve_learning_objects(null,get_condition());
	return count($objects);
}
/**
 * Get the objects to display in the table
 */
function get_objects($from, $number_of_items, $column, $direction)
{
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
 		$modify .= '<a href="index.php?action=delete&amp;id='.$object->get_id().'" title="'.get_lang('Delete').'"  onclick="javascript:if(!confirm(\''.addslashes(htmlentities(get_lang("ConfirmYourChoice"))).'\')) return false;"><img src="'.api_get_path(WEB_CODE_PATH).'img/delete.gif" alt="'.get_lang('Delete').'"/></a>';
 		$row[] = $modify;
 		$table_data[] = $row;
	}
	return $table_data;
}
// Load datamanager
$datamanager = RepositoryDataManager::get_instance();
// Load category
$current_category_id = isset($_GET['category']) ? intval($_GET['category']) : NULL;
if( is_null($current_category_id))
{
	$root_category = $datamanager->retrieve_root_category(api_get_user_id());
	$current_category_id = $root_category->get_id();
}

// Perform actions if needed
if(isset($_GET['action']))
{
	switch($_GET['action'])
	{
		case 'delete':
			$object = $datamanager->retrieve_learning_object($_GET['id']);
			$object->delete();
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
			break;
	}
}
/*
 * Display page
 */
// Display header
$tool_name = get_lang('MyLearningObjects');
Display::display_header($tool_name);
api_display_tool_title($tool_name);
// Create a search-box
$form = new FormValidator('search_simple','get','','',null,false);
$renderer =& $form->defaultRenderer();
$renderer->setElementTemplate('<span>{element}</span> ');
$form->addElement('text','keyword',get_lang('keyword'));
$form->addElement('submit','submit',get_lang('Search'));
$form->display();
// Create a navigation menu to browse through the categories
$menu = new CategoryMenu(api_get_user_id(),$current_category_id);
$menu->show();
// Create a sortable table to display the learning objects
$table = new SortableTable('objects','get_number_of_objects','get_objects');
$parameters = array();
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
$table->set_form_actions($actions);
$table->display();
// Display footer
Display::display_footer();
?>