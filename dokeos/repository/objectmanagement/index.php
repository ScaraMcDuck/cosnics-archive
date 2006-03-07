<?php
require_once('../../claroline/inc/claro_init_global.inc.php');
require_once(api_get_library_path().'/formvalidator/FormValidator.class.php');
require_once('../lib/datamanager.class.php');
require_once('../lib/learningobject_form.class.php');
require_once('../lib/learning_object/announcement/form.class.php');
require_once(api_get_library_path().'/text.lib.php');
/**
 * Get the condition to use when retrieving objects from the datamanager
 */
function get_condition()
{
	$condition = new ExactMatchCondition('owner',api_get_user_id());
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
	$datamanager = DataManager::get_instance();
	$objects = $datamanager->retrieve_learning_objects(null,get_condition());
	return count($objects);
}
/**
 * Get the objects to display in the table
 */
function get_objects($from, $number_of_items, $column, $direction)
{
	$table_columns = array('type','title','description','modified','id');
	$datamanager = DataManager::get_instance();
	$order_by[] = $table_columns[$column];
	$order_desc[] = $direction;
	$objects = $datamanager->retrieve_learning_objects(null,get_condition(),$order_by,$order_desc,$from,$number_of_items);
	$table_data = array();
	foreach($objects as $index => $object)
	{
		$row = array();
		$row[] = '<img src="'.api_get_path(WEB_CODE_PATH).'img/'.$object->get_type().'.gif" alt="'.$object->get_type().'"/>';
		$row[] = $object->get_title();
		$row[] = $object->get_description();
		$row[] = date('Y-m-d, H:i', is_null($object->get_modification_date()) ? $object->get_creation_date() : $object->get_modification_date());
		$row[] = '<a href="edit.php?id='.$object->get_id().'" title="'.get_lang('Edit').'"><img src="'.api_get_path(WEB_CODE_PATH).'img/edit.gif" alt="'.get_lang('Edit').'"/></a>';
 		$table_data[] = $row;
	}
	return $table_data;
}
/*
 * Display page
 */
// Display header
$tool_name = get_lang('MyLearningObjects');
Display::display_header($tool_name);
api_display_tool_title($tool_name);
$datamanager = DataManager::get_instance();
// Create a search-box
$form = new FormValidator('search_simple','get','','',null,false);
$renderer =& $form->defaultRenderer();
$renderer->setElementTemplate('<span>{element}</span> ');
$form->addElement('text','keyword',get_lang('keyword'));
$form->addElement('submit','submit',get_lang('Search'));
$form->display();
// Create a sortable table to display the learning objects
$table = new SortableTable('objects','get_number_of_objects','get_objects');
$parameters = array();
if (isset ($_GET['keyword']))
{
	$parameters = array ('keyword' => $_GET['keyword']);
}
$table->set_additional_parameters($parameters);
$column = 0;
$table->set_header($column++,get_lang('Type'));
$table->set_header($column++,get_lang('Title'));
$table->set_header($column++,get_lang('Description'));
$table->set_header($column++,get_lang('LastModified'));
$table->set_header($column++,get_lang('Modify'),false);
$table->display();
// Display footer
Display::display_footer();
?>