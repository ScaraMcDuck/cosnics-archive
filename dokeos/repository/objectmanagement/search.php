<?php
/**
 * Search in the repository
 * @package repository.objectmanagement
 */
// TODO: Share learning object table display code between index.php and search.php.

$langFile = 'admin';
require_once dirname(__FILE__).'/../../claroline/inc/claro_init_global.inc.php';
require_once api_get_library_path().'/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../lib/categorymenu.class.php';
//require_once api_get_library_path().'/fileDisplay.lib.php';
require_once api_get_library_path().'/text.lib.php';
require_once dirname(__FILE__).'/../lib/repositoryutilities.class.php';
require_once dirname(__FILE__).'/../lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../lib/repositorybrowsertable.class.php';
if( !api_get_user_id())
{
	api_not_allowed();
}

if(!isset($_GET['action']))
{
	$_GET['action'] = 'simple_search';
}


// Load datamanager
$datamanager = RepositoryDataManager::get_instance();

if(isset($_GET['action']))
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
			$search_form->addElement('submit','submit_search',get_lang('Search'));
			$search_form->addElement('static','simple','','<a href="search.php?action=simple_search">'.get_lang('SimpleSearch').'</a>');
			break;
		// Create a simple search-box
		case 'simple_search':
			$search_form = new FormValidator('search_simple','get','search.php','',null,false);
			$renderer = $search_form->defaultRenderer();
			$renderer->setElementTemplate('<span>{element}</span> ');
			$search_form->addElement('hidden','action','simple_search');
			$search_form->addElement('text','keyword',get_lang('keyword'),'size="50"');
			$search_form->addElement('submit','submit_search',get_lang('Search'));
			$search_form->addElement('static','advanced','pom','<a href="search.php?action=advanced_search">'.get_lang('AdvancedSearch').'</a>');
			break;
	}
}

/**
 * Create a sortable table to display the learning objects
 */
function create_repository_table()
{
	global $search_form;

	if(isset($_GET['action']))
	{
		$table = new RepositoryBrowserTable();
		$parameters = $search_form->exportValues();
		$table->set_additional_parameters($parameters);
		if(!is_null($table))
		{
			$table->display();
		}
	}
}
$tool_name = get_lang('Search');

$root_category = $datamanager->retrieve_root_category(api_get_user_id());
$menu = new CategoryMenu(api_get_user_id(),$root_category->get_id(),'index.php?category=%s');
$interbredcrump = $menu->get_breadcrumbs();
if(isset($_GET['action']) && $_GET['action'] == 'advanced_search')
{
	$interbredcrump[] = array('url' => 'search.php', 'name' => get_lang('Search'));
	$tool_name = get_lang('AdvancedSearch');
}
// Display header
Display::display_header($tool_name);
api_display_tool_title($tool_name);
echo '<div style="text-align:center;margin:20px;">';
// Display search box
$search_form->display();
echo '</div>';
// Display table
create_repository_table();

// Display footer
Display::display_footer();
?>