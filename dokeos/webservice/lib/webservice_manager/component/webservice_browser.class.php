<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../webservice_manager.class.php';
require_once dirname(__FILE__).'/../webservice_manager_component.class.php';
require_once dirname(__FILE__).'/webservice_browser_table/webservice_browser_table.class.php';
require_once dirname(__FILE__).'/../../webservice_category_menu.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__).'/../../webservice_category.class.php';

/**
 * Weblcms component which allows the user to manage his or her user subscriptions
 */
class WebserviceManagerWebserviceBrowserComponent extends WebserviceManagerComponent
{
	private $action_bar;
    
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{

		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Webservices')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseWebservices')));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$this->action_bar = $this->get_action_bar();
		$output = $this->get_user_html();
		$menu = $this->get_menu_html();
		
		$this->display_header($trail, false);        
		echo '<br />' . $this->action_bar->as_html() . '<br />';
		echo $output;
		echo $menu;
		$this->display_footer();
        
	}
	
	function get_user_html()
	{        
		$table = new WebserviceBrowserTable($this, array(WebserviceManager :: PARAM_WEBSERVICE_CATEGORY_ID =>$this->get_webservice_category()), $this->get_condition());
        
		$html = array();
		$html[] = '<div style="float: right; width: 80%;">';
		$html[] = $table->as_html();		 
		$html[] = '</div>';
		
		return implode($html, "\n");
	}
	
	function get_menu_html()
	{
		$webservice_category_menu = new WebserviceCategoryMenu($this->get_webservice_category());
		$html = array();
		$html[] = '<div style="float: left; width: 20%;">';
		$html[] = $webservice_category_menu->render_as_tree();
		$html[] = '</div>';
		
		return implode($html, "\n");
	}

	function get_condition()
	{
		$condition = new EqualityCondition(WebserviceCategory :: PROPERTY_PARENT, $this->get_webservice_category());
		
		$query = $this->action_bar->get_query();
		if(isset($query) && $query != '')
		{
			$or_conditions = array();
			$or_conditions[] = new LikeCondition(WebserviceCategory :: PROPERTY_NAME, $query);
			$or_condition = new OrCondition($or_conditions); 
			
			$and_conditions = array();
			$and_conditions[] = $condition;
			$and_conditions[] = $or_condition;
			$condition = new AndCondition($and_conditions);
		}
		
		return $condition;
	}
	
	function get_webservice()
    {
        
		return (isset($_GET[WebserviceManager :: PARAM_WEBSERVICE_ID]) ? $_GET[WebserviceManager :: PARAM_WEBSERVICE_ID] : 0);
	}
	
	function get_webservice_category()
	{
		return (isset($_GET[WebserviceManager :: PARAM_WEBSERVICE_CATEGORY_ID]) ? $_GET[WebserviceManager :: PARAM_WEBSERVICE_CATEGORY_ID] : 0);
	}
	
	function get_action_bar()
	{        
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $ID = Request :: get('webservice_category_id');
		$action_bar->set_search_url($this->get_url(array(WebserviceManager :: PARAM_WEBSERVICE_CATEGORY_ID => $this->get_webservice_category())));		
        $action_bar->add_common_action(WebserviceManager :: get_tool_bar_item($ID));
        $action_bar->set_help_action(HelpManager :: get_tool_bar_help_item('webservice'));

		return $action_bar;
	}
}
?>