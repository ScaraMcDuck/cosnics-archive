<?php
/**
 * @package reservation.lib.reservation_manager.component
 */
require_once dirname(__FILE__).'/../reservations_manager.class.php';
require_once dirname(__FILE__).'/../reservations_manager_component.class.php';
require_once dirname(__FILE__).'/item_browser/item_browser_table.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__).'/../../reservations_menu.class.php';
require_once dirname(__FILE__).'/../../forms/pool_form.class.php';

class ReservationsManagerAdminItemBrowserComponent extends ReservationsManagerComponent
{
	private $ab;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadCrumbTrail();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
		$trail->add(new BreadCrumb($this->get_url(), Translation :: get('View items')));
		
		$this->ab = $this->get_action_bar();
		$menu = new ReservationsMenu($_GET[ReservationsManager :: PARAM_CATEGORY_ID], '?application=reservations&go=admin_browse_items&category_id=%s');
		
		$this->display_header($trail);
		echo $this->ab->as_html() . '<br />';
		echo '<div style="float: left; padding-right: 18px; overflow: auto;">' . $menu->render_as_tree() . '</div>';
		echo '<div style="float: right; width: 80%;">';
		echo '<br />';
		echo $this->get_user_html();
		echo '</div>';
		$this->display_footer();
	}
	
	function get_user_html()
	{		
		$parameters = array_merge($this->get_parameters(), array(ReservationsManager :: PARAM_CATEGORY_ID => $this->get_category()));
		$table = new ItemBrowserTable($this, $parameters, $this->get_condition());
		
		$html = array();
		$html[] = $table->as_html();
		
		return implode($html, "\n");
	}
	
	function get_condition()
	{
		$cat_id = $this->get_category();
		$conditions[] = new EqualityCondition(Item :: PROPERTY_CATEGORY, $cat_id);
		$conditions[] = new EqualityCondition(Item :: PROPERTY_STATUS, Item :: STATUS_NORMAL);
		$condition = new AndCondition($conditions);
		
		$search = $this->ab->get_query();
		if(isset($search) && ($search != ''))
		{
			$conditions = array();
			$conditions[] = new LikeCondition(Item :: PROPERTY_NAME, $search);
			$conditions[] = new LikeCondition(Item :: PROPERTY_DESCRIPTION, $search);
			$orcondition = new OrCondition($conditions);
			
			$conditions = array();
			$conditions[] = $orcondition;
			$conditions[] = $condition;
			$condition = new AndCondition($conditions);
		}
		return $condition;
	}
	
	function get_category()
	{
		return (isset($_GET[ReservationsManager :: PARAM_CATEGORY_ID])?$_GET[ReservationsManager :: PARAM_CATEGORY_ID]:0);
	}
	
	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		$action_bar->set_search_url($this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID => $this->get_category())));
		if($this->has_right('category', $this->get_category(), ReservationsRights :: ADD_RIGHT))
		{
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path().'action_add.png', $this->get_create_item_url($this->get_category()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}
		
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		if($this->has_right('category', $this->get_category(), ReservationsRights :: EDIT_RIGHT))
		{
			$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Blackout'), Theme :: get_common_image_path().'action_lock.png', $this->get_blackout_category_url($this->get_category(), 1), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
			$action_bar->add_tool_action(new ToolbarItem(Translation :: get('UnBlackout'), Theme :: get_common_image_path().'action_unlock.png', $this->get_blackout_category_url($this->get_category(), 0), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
			$action_bar->add_tool_action(new ToolbarItem(Translation :: get('SetCredits'), Theme :: get_common_image_path().'action_statistics.png', $this->get_credit_category_url($this->get_category()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}
		
		return $action_bar;
	}
}
?>