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
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class ReservationsManagerAdminItemBrowserComponent extends ReservationsManagerComponent
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
		$trail->add(new BreadCrumb($this->get_url(), Translation :: get('View items')));
		
		$this->ab = new ActionBarRenderer($this->get_left_toolbar_data(), $this->get_right_toolbar_data(), $this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID => $this->get_category())));
		$menu = new ReservationsMenu($_GET[ReservationsManager :: PARAM_CATEGORY_ID], '?go=admin_browse_items&category_id=%s');
		
		$this->display_header($trail);
		echo $this->ab->as_html() . '<br />';
		echo '<div style="float: left; padding-right: 20px;">' . $menu->render_as_tree() . '</div>';
		echo '<div style="float: right; width: 70%;">';
		echo '<br />';
		echo $this->get_user_html();
		echo '</div>';
		$this->display_footer();
	}
	
	function get_user_html()
	{		
		$table = new ItemBrowserTable($this, array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_ITEMS, ReservationsManager :: PARAM_CATEGORY_ID => $this->get_category()), $this->get_condition());
		
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
	
	function get_left_toolbar_data()
	{
		$tb_data = array();
		
		if($this->has_right('category', $this->get_category(), ReservationsRights :: ADD_RIGHT))
		{
			$tb_data[] = array(
					'href' => $this->get_create_item_url($this->get_category()),
					'label' => Translation :: get('Add'),
					'img' => Theme :: get_theme_path() . 'action_add.png'
			);
		}
		
		$tb_data[] = array(
				'href' => $this->get_url(),
				'label' => Translation :: get('ShowAll'),
				'img' => Theme :: get_theme_path() . 'action_browser.png'
		);
		
		return $tb_data;
	}
	
	function get_right_toolbar_data()
	{
		$tb_data = array();
		
		if($this->has_right('category', $this->get_category(), ReservationsRights :: EDIT_RIGHT))
		{
			$tb_data[] = array(
				'href' => $this->get_blackout_category_url($this->get_category(), 1),
				'label' => Translation :: get('Blackout'),
				'img' => Theme :: get_theme_path() . 'action_lock.png'
			);

			$tb_data[] = array(
				'href' => $this->get_blackout_category_url($this->get_category(), 0),
				'label' => Translation :: get('UnBlackout'),
				'img' => Theme :: get_theme_path() . 'action_unlock.png'
			);
		
			$tb_data[] = array(
				'href' => $this->get_credit_category_url($this->get_category()),
				'label' => Translation :: get('SetCredits'),
				'img' => Theme :: get_theme_path() . 'action_statistics.png'
			);
		}
		
		return $tb_data;
	}
}
?>