<?php
/**
 * @package admin.lib.admin_manager.component
 */
require_once dirname(__FILE__).'/../admin_manager.class.php';
require_once dirname(__FILE__).'/../admin_manager_component.class.php';
require_once dirname(__FILE__).'/system_announcement_browser/system_announcement_browser_table.class.php';
/**
 * Admin component to manage system announcements
 */
class AdminSystemAnnouncementBrowserComponent extends AdminComponent
{
    
	function run()
	{
		$trail = new BreadcrumbTrail();		
		
		$output = $this->get_publications_html();
		
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('PlatformAdmin')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('SystemAnnouncements')));
		
		$this->display_header($trail, true);
		echo $output;
		$this->display_footer();
	}
	
	private function get_publications_html()
	{
		$parameters = $this->get_parameters(true);
		
		$table = new SystemAnnouncementBrowserTable($this, null, $parameters, $this->get_condition());
		
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
			
			$conditions[] = new EqualityCondition(SystemAnnouncement :: PROPERTY_HIDDEN, false);
			
			$time_conditions = array();
			
			$forever_conditions = array();
			//$forever_conditions[] = new EqualityCondition();
			$forever_conditions[] = new EqualityCondition(SystemAnnouncement :: PROPERTY_FROM_DATE, 0);
			$forever_conditions[] = new EqualityCondition(SystemAnnouncement :: PROPERTY_TO_DATE, 0);
			$time_conditions[] = new AndCondition($forever_conditions);
			
			$limited_conditions = array();
			$limited_conditions[] = new InequalityCondition(SystemAnnouncement :: PROPERTY_FROM_DATE, InequalityCondition :: LESS_THAN_OR_EQUAL, time());
			$limited_conditions[] = new InequalityCondition(SystemAnnouncement :: PROPERTY_TO_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, time());
			$time_conditions[] = new AndCondition($limited_conditions);
			
			$conditions[] = new OrCondition($time_conditions);
			
			$condition = new AndCondition($conditions);
		}
		
		return $condition;
	}
}
?>