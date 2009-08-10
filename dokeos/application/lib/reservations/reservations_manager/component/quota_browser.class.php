<?php
/**
 * @package reservation.lib.reservation_manager.component
 */
require_once dirname(__FILE__).'/../reservations_manager.class.php';
require_once dirname(__FILE__).'/../reservations_manager_component.class.php';
require_once dirname(__FILE__).'/quota_browser/quota_browser_table.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__).'/../../reservations_menu.class.php';
require_once dirname(__FILE__).'/../../forms/pool_form.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class ReservationsManagerQuotaBrowserComponent extends ReservationsManagerComponent
{
	private $ab;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadCrumbTrail();
		$admin = new Admin();
		$trail->add(new Breadcrumb($admin->get_link(array(Admin :: PARAM_ACTION => Admin :: ACTION_ADMIN_BROWSER)), Translation :: get('PlatformAdmin')));
		$trail->add(new BreadCrumb($this->get_url(), Translation :: get('View quotas')));
		
		$this->ab = new ActionBarRenderer($this->get_left_toolbar_data(), array(), $this->get_url());
		
		$this->display_header($trail);
		echo $this->ab->as_html() . '<br />';
		echo $this->get_user_html();
		$this->display_footer();
	}
	
	function get_user_html()
	{		
		$table = new QuotaBrowserTable($this, array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_ITEMS), $this->get_condition());
		
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
			$conditions[] = new LikeCondition(Quota :: PROPERTY_CREDITS, $search);
			$conditions[] = new LikeCondition(Quota :: PROPERTY_TIME_UNIT, $search);
			return new OrCondition($conditions);
		}
	}
	
	function get_left_toolbar_data()
	{
		$tb_data = array();
		
		$tb_data[] = array(
				'href' => $this->get_create_quota_url(),
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
?>

