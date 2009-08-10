<?php
/**
 * @package reservation.lib.reservation_manager.component
 */
require_once dirname(__FILE__).'/../reservations_manager.class.php';
require_once dirname(__FILE__).'/../reservations_manager_component.class.php';
require_once dirname(__FILE__).'/category_quota_box_browser/category_quota_box_browser_table.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class ReservationsManagerCategoryQuotaBoxBrowserComponent extends ReservationsManagerComponent
{
	private $ab;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadCrumbTrail();
		$admin = new Admin();
		
		$category_id = $this->get_category_id();
		
		$trail->add(new Breadcrumb($admin->get_link(array(Admin :: PARAM_ACTION => Admin :: ACTION_ADMIN_BROWSER)), Translation :: get('PlatformAdmin')));
		$trail->add(new BreadCrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_CATEGORIES)), Translation :: get('View categories')));
		$trail->add(new BreadCrumb($this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID, $category_id)), Translation :: get('ViewCategoryQuotaBoxes')));
		
		//$this->ab = new ActionBarRenderer($this->get_left_toolbar_data(), array(), $this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID, $category_id)));
		$this->ab = new ActionBarRenderer($this->get_left_toolbar_data(), array());
		
		$this->display_header($trail);
		echo $this->ab->as_html() . '<br />';
		echo $this->get_user_html();
		$this->display_footer();
	}
	
	function get_user_html()
	{		
		$table = new CategoryQuotaBoxBrowserTable($this, array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_CATEGORY_QUOTA_BOXES, ReservationsManager :: PARAM_CATEGORY_ID, $category_id), $this->get_condition());
		
		$html = array();
		$html[] = $table->as_html();
		
		return implode($html, "\n");
	}
	
	function get_category_id()
	{
		$id = Request :: get(ReservationsManager :: PARAM_CATEGORY_ID);
		if(!isset($id) || is_null($id))
			$id = 0;
		
		return $id;
	}
	
	function get_condition()
	{
		return new EqualityCondition(QuotaBoxRelCategory :: PROPERTY_CATEGORY_ID, $this->get_category_id());
	}

	function get_left_toolbar_data()
	{
		$tb_data = array();
		
		$tb_data[] = array(
				'href' => $this->get_create_category_quota_box_url($this->get_category_id()),
				'label' => Translation :: get('Add'),
				'img' => Theme :: get_theme_path() . 'action_add.png'
		);

		$tb_data[] = array(
				'href' => $this->get_url(),
				'label' => Translation :: get('ShowAll'),
				'img' => Theme :: get_theme_path() . 'action_browser.png'
		);
		
		return $tb_data;
		
	}
}