<?php
/**
 * @package reservation.lib.reservation_manager.component
 */
require_once dirname(__FILE__).'/../reservations_manager.class.php';
require_once dirname(__FILE__).'/../reservations_manager_component.class.php';
require_once dirname(__FILE__).'/category_browser/category_browser_table.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__).'/../../reservations_menu.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class ReservationsManagerAdminCategoryBrowserComponent extends ReservationsManagerComponent
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
		$trail->add(new BreadCrumb($this->get_url(), Translation :: get('View categories')));
		
		$this->ab = new ActionBarRenderer($this->get_left_toolbar_data(), array(), $this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID => $this->get_category())));
		$menu = new ReservationsMenu($_GET[ReservationsManager :: PARAM_CATEGORY_ID], '?go=admin_browse_categories&category_id=%s');
		
		$this->display_header($trail);
		echo $this->ab->as_html() . '<br />';
		echo '<div style="float: left; padding-right: 20px; overflow: auto; width: 18%;">' . $menu->render_as_tree() . '</div>';
		echo $this->get_user_html();
		$this->display_footer();
	}
	
	function get_user_html()
	{		
		$table = new CategoryBrowserTable($this, array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_CATEGORIES, ReservationsManager :: PARAM_CATEGORY_ID => $this->get_category()), $this->get_condition());
		
		$html = array();
		$html[] = '<div style="float: right; width: 80%;">';
		$html[] = $table->as_html();
		$html[] = '</div>';
		
		return implode($html, "\n");
	}
	
	function get_condition()
	{
		$cat_id = $this->get_category();
		$conditions[] = new EqualityCondition(Category :: PROPERTY_PARENT, $cat_id);
		$conditions[] = new EqualityCondition(Category :: PROPERTY_STATUS, Category :: STATUS_NORMAL);
		$condition = new AndCondition($conditions);
		
		$search = $this->ab->get_query();
		if(isset($search) && ($search != ''))
		{
			$conditions = array();
			$conditions[] = new LikeCondition(Category :: PROPERTY_NAME, $search);
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
		
		//if($this->has_right('category', $this->get_category(), ReservationsRights :: ADD_RIGHT))
		{
			$tb_data[] = array(
					'href' => $this->get_create_category_url($_GET[ReservationsManager :: PARAM_CATEGORY_ID]),
					'label' => Translation :: get('Add'),
					'img' => Theme :: get_theme_path() . 'action_add.png'
			);
		}
		
		$tb_data[] = array(
				'href' => $this->get_url(),
				'label' => Translation :: get('ShowAll'),
				'img' => Theme :: get_theme_path() . 'action_browser.png'
		);
		
		$url = $this->get_browse_category_quota_boxes_url(0);
		$tb_data[] = array(
			'href' => $url,
			'label' => Translation :: get('ManageQuota'),
			'img' => Theme :: get_theme_path() .'quota.png'
		);
		
		return $tb_data;
		
	}
}