<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../help_manager.class.php';
require_once dirname(__FILE__).'/../help_manager_component.class.php';
require_once dirname(__FILE__).'/help_item_browser_table/help_item_browser_table.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';
/**
 * Weblcms component which allows the user to manage his or her user subscriptions
 */
class HelpManagerBrowserComponent extends HelpManagerComponent
{
	private $ab;
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{

		$trail = new BreadcrumbTrail();
		$admin = new AdminManager();
		$trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('PlatformAdmin')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('HelpItemList')));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$this->ab = $this->get_action_bar();
		$output = $this->get_user_html();
		
		$this->display_header($trail, false);
		echo '<br />' . $this->ab->as_html() . '<br />';
		echo $output;
		$this->display_footer();
	}
	
	function get_user_html()
	{		
		$table = new HelpItemBrowserTable($this, array(HelpManager :: PARAM_ACTION => HelpManager :: ACTION_BROWSE_HELP_ITEMS), $this->get_condition());
		
		$html = array();
		$html[] = '<div style="float: right; width: 100%;">';
		$html[] = $table->as_html();
		$html[] = '</div>';
		
		return implode($html, "\n");
	}
	
	function get_help_item()
	{
		return (isset($_GET[HelpManager :: PARAM_HELP_ITEM])?$_GET[HelpManager :: PARAM_HELP_ITEM]:0);
	}

	function get_condition()
	{	
		$query = $this->ab->get_query();
		if(isset($query) && $query != '')
		{
			$condition = new LikeCondition(HelpItem :: PROPERTY_NAME, $query);
		}
		
		return $condition;
	}
	
	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		
		$action_bar->set_search_url($this->get_url(array(HelpManager :: PARAM_HELP_ITEM => $this->get_help_item())));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(HelpManager :: PARAM_HELP_ITEM => $this->get_help_item())), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		return $action_bar;
	}
}
?>