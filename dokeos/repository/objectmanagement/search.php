<?php
$langFile = 'admin';
require_once '../../claroline/inc/claro_init_global.inc.php';
require_once api_get_library_path().'/formvalidator/FormValidator.class.php';
require_once '../lib/quotamanager.class.php';
require_once api_get_library_path().'/fileDisplay.lib.php';
require_once api_get_library_path().'/text.lib.php';
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
	$condition = new EqualityCondition('owner',api_get_user_id());
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
	$table_columns = array('id','type','title','description','modified','category');
	$datamanager = RepositoryDataManager::get_instance();
	$order_by[] = $table_columns[$column];
	$order_desc[] = $direction;
	$objects = $datamanager->retrieve_learning_objects(null,get_condition(),$order_by,$order_desc,$from,$number_of_items);
	$table_data = array();
	foreach($objects as $index => $object)
	{
		$row = array();
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
 		$table_data[] = $row;
	}
	return $table_data;
}
// Load datamanager
$datamanager = RepositoryDataManager::get_instance();

$search_form = null;

if( isset($_GET['action']))
{
	switch ($_GET['action'])
	{
		// Create an advanced search-box
		case 'advanced_search':
			$types = $datamanager->get_registered_types();
			$type_choices = array();
			foreach($types as $index => $type)
			{
				$type_choices[$type] = get_lang($type);
			}
			$search_form = new FormValidator('search_advanced','get','search.php','',null,false);
			$search_form->addElement('text','keyword',get_lang('Title'));
			$search_form->addElement('text','description',get_lang('Description'));
			$search_form->addElement('select','type',get_lang('Type'),$type_choices,'multiple="multiple"');
			$search_form->addElement('submit','submit',get_lang('Search'));
			break;
	}
}
if( is_null($search_form))
{
	// Create a search-box
	$search_form = new FormValidator('search_simple','get','search.php','',null,false);
	$renderer =& $search_form->defaultRenderer();
	$renderer->setElementTemplate('<span>{element}</span> ');
	$search_form->addElement('text','keyword',get_lang('keyword'));
	$search_form->addElement('submit','submit',get_lang('Search'));
	$search_form->addElement('static','advanced','pom','<a href="search.php?action=advanced_search">'.get_lang('AdvancedSearch').'</a>');
}

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
$table->set_header($column++,get_lang('Type'));
$table->set_header($column++,get_lang('Title'));
$table->set_header($column++,get_lang('Description'));
$table->set_header($column++,get_lang('LastModified'));

// Display header
Display::display_header(get_lang('Search'));

echo '<div style="text-align:center;margin:20px;">';
// Display search box
$search_form->display();
echo '</div>';

// Display search results
$table->display();

// Display footer
Display::display_footer();
?>