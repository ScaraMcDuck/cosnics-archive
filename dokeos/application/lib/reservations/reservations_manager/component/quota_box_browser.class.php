<?php
/**
 * @package reservation.lib.reservation_manager.component
 */
require_once dirname(__FILE__).'/../reservations_manager.class.php';
require_once dirname(__FILE__).'/../reservations_manager_component.class.php';
require_once dirname(__FILE__).'/quota_box_browser/quota_box_browser_table.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';

class ReservationsManagerQuotaBoxBrowserComponent extends ReservationsManagerComponent
{
	private $ab;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadCrumbTrail();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
		$trail->add(new BreadCrumb($this->get_url(), Translation :: get('ViewQuotaBoxes')));
		
		$this->ab = $this->get_action_bar();
		
		$this->display_header($trail);
		echo $this->ab->as_html() . '<br />';
		echo $this->get_user_html();
		$this->display_footer();
	}
	
	function get_user_html()
	{		
		$table = new QuotaBoxBrowserTable($this, array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_CATEGORIES), $this->get_condition());
		
		$html = array();
		$html[] = $table->as_html();
		
		return implode($html, "\n");
	}
	
	function get_condition()
	{
		$search = $this->ab->get_query();
		if(isset($search) && ($search != ''))
		{
			$conditions = array();
			$conditions[] = new LikeCondition(QuotaBox :: PROPERTY_NAME, $search);
			$conditions[] = new LikeCondition(QuotaBox :: PROPERTY_DESCRIPTION, $search);
			$condition = new OrCondition($conditions);

			return $condition;
		}
	}
	
	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		$action_bar->set_search_url($this->get_url());
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path().'action_add.png', $this->get_create_quota_box_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		return $action_bar;
	}
}