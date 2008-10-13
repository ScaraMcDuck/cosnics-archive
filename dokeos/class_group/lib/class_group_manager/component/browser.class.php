<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../class_group_manager.class.php';
require_once dirname(__FILE__).'/../class_group_manager_component.class.php';
require_once dirname(__FILE__).'/class_group_browser/class_group_browser_table.class.php';
require_once dirname(__FILE__).'/../../class_group_menu.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';
/**
 * Weblcms component which allows the user to manage his or her user subscriptions
 */
class ClassGroupManagerBrowserComponent extends ClassGroupManagerComponent
{
	private $firstletter;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$this->firstletter = $_GET[ClassGroupManager :: PARAM_FIRSTLETTER];

		$trail = new BreadcrumbTrail();
		$admin = new AdminManager();
		$trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ClassGroupList')));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$menu = $this->get_menu_html();
		$output = $this->get_user_html();
		
		$this->display_header($trail, true);
		echo $menu;
		echo $output;
		$this->display_footer();
	}
	
	function get_user_html()
	{		
		$table = new ClassGroupBrowserTable($this, array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_BROWSE_CLASSGROUPS), $this->get_condition());
		
		$html = array();
		$html[] = '<div style="float: right; width: 80%;">';
		$html[] = $table->as_html();
		$html[] = '</div>';
		
		return implode($html, "\n");
	}
	
	function get_menu_html()
	{
		$extra_items = array ();
		if ($this->get_search_validate())
		{
			// $search_url = $this->get_url();
			$search_url = '#';
			$search = array ();
			$search['title'] = Translation :: get('SearchResults');
			$search['url'] = $search_url;
			$search['class'] = 'search_results';
			$extra_items[] = & $search;
		}
		else
		{
			$search_url = null;
		}
		
		$temp_replacement = '__FIRSTLETTER__';
		$url_format = $this->get_url(array (ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_BROWSE_CLASSGROUPS, ClassGroupManager :: PARAM_FIRSTLETTER => $temp_replacement));
		$url_format = str_replace($temp_replacement, '%s', $url_format);
		$group_menu = new ClassGroupMenu($this->firstletter, $url_format, & $extra_items);
		
		if (isset ($search_url))
		{
			$group_menu->forceCurrentUrl($search_url, true);
		}
		
		$html = array();
		$html[] = '<div style="float: left; width: 20%;">';
		$html[] = $group_menu->render_as_tree();
		$html[] = '</div>';
		
		return implode($html, "\n");
	}

	function get_condition()
	{
		$search_conditions = $this->get_search_condition();
		$condition = null;
		if (isset($this->firstletter))
		{
			$conditions = array();
			$conditions[] = new PatternMatchCondition(ClassGroup :: PROPERTY_NAME, $this->firstletter. '*');
			$conditions[] = new PatternMatchCondition(ClassGroup :: PROPERTY_NAME, chr(ord($this->firstletter)+1). '*');
			$conditions[] = new PatternMatchCondition(ClassGroup :: PROPERTY_NAME, chr(ord($this->firstletter)+2). '*');
			$condition = new OrCondition($conditions);
			if (count($search_conditions))
			{
				$condition = new AndCondition($condition, $search_conditions);
			}
		}
		else
		{
			if (count($search_conditions))
			{
				$condition = $search_conditions;
			}
		}
		return $condition;
	}
}
?>