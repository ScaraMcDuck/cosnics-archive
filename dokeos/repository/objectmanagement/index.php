<?php
$cidReset = true;
require_once('../../claroline/inc/claro_init_global.inc.php');
require_once(api_get_library_path().'/formvalidator/FormValidator.class.php');
require_once('../lib/repositorydatamanager.class.php');
require_once('../lib/learningobject_form.class.php');
require_once('../lib/categorymenu.class.php');
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
	$condition1 = new ExactMatchCondition('owner',api_get_user_id());
	$condition2 = new ExactMatchCondition('category',$current_category_id);
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
	$objects = $datamanager->retrieve_learning_objects(null,get_condition());
	return count($objects);
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

$tool_name = get_lang('MyLearningObjects');
// Retrieve learning objecttypes
$object_types = $datamanager->get_registered_types();
$t = array();
foreach($object_types as $key => $type)
{
	$t[$type] = $type;
}
$tool_name = get_lang('MyRepository');
// Create a search-box
$form = new FormValidator('search_simple','get','','',null,false);
$renderer =& $form->defaultRenderer();
$renderer->setElementTemplate('<span>{element}</span> ');
$form->addElement('text','keyword',get_lang('keyword'));
$form->addElement('submit','submit',get_lang('Search'));
// Create a dropdownlist with learning objecttypes
$create_form = new FormValidator('type_list', 'post', 'create.php');
$renderer =& $create_form->defaultRenderer();
$renderer->setElementTemplate('<span>{element}</span> ');
$create_form->addElement('select','type',null,$t,$t);
$create_form->addElement('submit','submit',get_lang('Create'));
// Create a navigation menu to browse through the categories
$menu = new CategoryMenu(api_get_user_id(),$current_category_id);
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
		case 'delete':
			$object = $datamanager->retrieve_learning_object($_GET['id']);
			$object->delete();
			$message = get_lang('ObjectDeleted');
			break;
		case 'move':
			$condition = new ExactMatchCondition('owner',api_get_user_id());
			$categories = $datamanager->retrieve_learning_objects('category',$condition);
			$category_choices = array();
			foreach($categories as $index => $category)
			{
				$category_choices[$category->get_id()] = $category->get_title();
			}
			$popup_form = new FormValidator('move_form','get');
			$popup_form->addElement('hidden','id',$_GET['id']);
			$popup_form->addElement('hidden','action','move');
			$popup_form->addElement('select','new_category',get_lang('Category'),$category_choices);
			$popup_form->addElement('submit','submit',get_lang('Ok'));
			if($popup_form->validate())
			{
				$values = $popup_form->exportValues();
				$object = $datamanager->retrieve_learning_object($_GET['id']);
				$object->set_category_id($values['new_category']);
				$object->update(false);
				$message = get_lang('ObjectMoved');
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
			break;
		case 'move_selected':
			$condition = new ExactMatchCondition('owner',api_get_user_id());
			$categories = $datamanager->retrieve_learning_objects('category',$condition);
			$category_choices = array();
			foreach($categories as $index => $category)
			{
				$category_choices[$category->get_id()] = $category->get_title();
			}
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
					$object->set_category_id($values['category']);
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
$renderer =& new HTML_Menu_ArrayRenderer();
$menu->render($renderer,'urhere');
$breadcrumbs = $renderer->toArray();
//$tool_name = $breadcrumbs[count[$breadcrumbs]]
$current_location = array_pop($breadcrumbs);
foreach($breadcrumbs as $index => $breadcrumb)
{
	$interbredcrump[] = array ("url" => $breadcrumb['url'], "name" => $breadcrumb['title']);
}
// Display header
Display::display_header($current_location['title']);
api_display_tool_title($current_location['title']);
// Display create form
$create_form->display();
// Display search form
$form->display();
// Display message if needed
if(isset($message))
{
	Display::display_normal_message($message);
}
echo '<div style="float:left;width:20%;">';
// Display menu
$renderer =& new HTML_Menu_DirectTreeRenderer();
$menu->render($renderer,'sitemap');
echo $renderer->toHtml();
echo '</div><div style="float:right;width:80%;">';
// Display table
$table->display();
echo '</div>';
// Display footer
Display::display_footer();
?>