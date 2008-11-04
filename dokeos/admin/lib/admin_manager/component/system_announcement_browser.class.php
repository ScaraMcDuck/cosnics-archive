<?php
/**
 * @package admin.lib.admin_manager.component
 */
require_once dirname(__FILE__).'/../admin_manager.class.php';
require_once dirname(__FILE__).'/../admin_manager_component.class.php';
require_once dirname(__FILE__).'/system_announcement_publication_browser/system_announcement_publication_browser_table.class.php';
require_once Path :: get_library_path().'html/toolbar/toolbar.class.php';
/**
 * Admin component to manage system announcements
 */
class AdminSystemAnnouncementBrowserComponent extends AdminManagerComponent
{
    
	function run()
	{
		$trail = new BreadcrumbTrail();		
		$trail->add(new Breadcrumb($this->get_url(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('PlatformAdmin')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('SystemAnnouncements')));
		
		$user = $this->get_user();
		
		if (!$user->is_platform_admin())
		{
			$this->not_allowed();
		}
		
		$publications_table = $this->get_publications_html();
		$toolbar = $this->get_toolbar();
		
		$this->display_header($trail, true);
		echo $toolbar;
		echo $publications_table;
		$this->display_footer();
	}
	
	private function get_publications_html()
	{
		$parameters = $this->get_parameters(true);
		
		$table = new SystemAnnouncementPublicationBrowserTable($this, null, $parameters, $this->get_condition());
		
		$html = array();
		$html[] = $table->as_html();
		
		return implode($html, "\n");
	}
	
	function get_condition()
	{
		$condition = null;
		$user = $this->get_user();
		
		if (!$user->is_platform_admin())
		{
			$conditions = array();
			
			$conditions[] = new EqualityCondition(SystemAnnouncementPublication :: PROPERTY_HIDDEN, false);
			
			$time_conditions = array();
			
			$forever_conditions = array();
			//$forever_conditions[] = new EqualityCondition();
			$forever_conditions[] = new EqualityCondition(SystemAnnouncementPublication :: PROPERTY_FROM_DATE, 0);
			$forever_conditions[] = new EqualityCondition(SystemAnnouncementPublication :: PROPERTY_TO_DATE, 0);
			$time_conditions[] = new AndCondition($forever_conditions);
			
			$limited_conditions = array();
			$limited_conditions[] = new InequalityCondition(SystemAnnouncementPublication :: PROPERTY_FROM_DATE, InequalityCondition :: LESS_THAN_OR_EQUAL, time());
			$limited_conditions[] = new InequalityCondition(SystemAnnouncementPublication :: PROPERTY_TO_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, time());
			$time_conditions[] = new AndCondition($limited_conditions);
			
			$conditions[] = new OrCondition($time_conditions);
			
			$condition = new AndCondition($conditions);
		}
		
		return $condition;
	}
	
	function get_toolbar()
	{
		$toolbar = new Toolbar();
		$toolbar->add_item(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_img_path().'action_publish.png', $this->get_system_announcement_publication_creating_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		return $toolbar->as_html();
	}
}
?>