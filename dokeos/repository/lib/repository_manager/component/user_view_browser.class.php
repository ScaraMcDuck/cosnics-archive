<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__).'/user_view_browser/user_view_browser_table.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';
/**
 * Weblcms component which allows the user to manage his or her user subscriptions
 */
class RepositoryManagerUserViewBrowserComponent extends RepositoryManagerComponent
{
	private $ab;
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{

		$trail = new BreadcrumbTrail(false);
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UserViewList')));

        $admin = new AdminManager();

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
		$table = new UserViewBrowserTable($this, array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_USER_VIEWS), $this->get_condition());
		
		$html = array();
		$html[] = '<div style="float: right; width:100%;">';
		$html[] = $table->as_html();
		$html[] = '</div>';
		
		return implode($html, "\n");
	}

	function get_condition()
	{
		$condition = new EqualityCondition(UserView :: PROPERTY_USER_ID, $this->get_user_id());
		
		$query = $this->ab->get_query();
		if(isset($query) && $query != '')
		{
			$or_conditions = array();
			$or_conditions[] = new LikeCondition(UserView :: PROPERTY_NAME, $query);
			$or_conditions[] = new LikeCondition(UserView :: PROPERTY_DESCRIPTION, $query);
			$or_condition = new OrCondition($or_conditions); 
			
			$and_conditions[] = array();
			$and_conditions = $condition;
			$and_conditions = $or_condition;
			$condition = new AndCondition($and_conditions);
		}
		
		return $condition;
	}
	
	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		
		$action_bar->set_search_url($this->get_url());
		
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path().'action_add.png', $this->get_create_user_view_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		return $action_bar;
	}
}
?>