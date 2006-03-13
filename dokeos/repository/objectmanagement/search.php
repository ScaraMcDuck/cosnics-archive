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
	$cond_owner = new EqualityCondition('owner',api_get_user_id());
	if (isset ($_GET['action']))
	{
		switch($_GET['action'])
		{
			case 'advanced_search':
				$cond_title = RepositoryUtilities::query_to_condition($_GET['title'],'title');
				$cond_description = RepositoryUtilities::query_to_condition($_GET['description'],'description');
				foreach($_GET['type'] as $index => $type)
				{
					$cond_type[] = new EqualityCondition('type',$type);
				}
				$search_conditions = array();
				if( !is_null($cond_title))
				{
					$search_conditions[] = $cond_title;
				}
				if( !is_null($cond_description))
				{
					$search_conditions[] = $cond_description;
				}
				if(count($cond_type)>0)
				{
					$search_conditions[] = new OrCondition($cond_type);
				}
				$condition = new AndCondition($search_conditions);
				break;
			default:
				$cond_keyword = RepositoryUtilities :: query_to_condition($_GET['keyword']);
				if (!is_null($cond_keyword)) {
					$condition = new AndCondition($cond_owner, $cond_keyword);
				}
				break;
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
			$search_form->addElement('hidden','action','advanded_search');
			$search_form->addElement('text','title',get_lang('Title'),'size="50"');
			$search_form->addElement('text','description',get_lang('Description'),'size="50"');
			$search_form->addElement('select','type',get_lang('Type'),$type_choices,'multiple="multiple" size="5"');
			$search_form->addElement('submit','submit',get_lang('Search'));
			break;
		// Create a simple search-box
		default:
			$search_form = new FormValidator('search_simple','get','search.php','',null,false);
			$renderer =& $search_form->defaultRenderer();
			$renderer->setElementTemplate('<span>{element}</span> ');
			$search_form->addElement('text','keyword',get_lang('keyword'),'size="50"');
			$search_form->addElement('submit','submit',get_lang('Search'));
			$search_form->addElement('static','advanced','pom','<a href="search.php?action=advanced_search">'.get_lang('AdvancedSearch').'</a>');
			break;
	}
}
if(isset($_GET['action']))
{
	// Create a sortable table to display the learning objects
	$table = new SortableTable('objects','get_number_of_objects','get_objects');
	$parameters = $search_form->exportValues();
	$table->set_additional_parameters($parameters);
	$column = 0;
	$table->set_header($column++,get_lang('Type'));
	$table->set_header($column++,get_lang('Title'));
	$table->set_header($column++,get_lang('Description'));
	$table->set_header($column++,get_lang('LastModified'));
}

// Display header
Display::display_header(get_lang('Search'));

echo '<div style="text-align:center;margin:20px;">';
// Display search box
$search_form->display();
echo '</div>';

if(!is_null($table))
{
	// Display search results
	$table->display();
}

// Display footer
Display::display_footer();
?>