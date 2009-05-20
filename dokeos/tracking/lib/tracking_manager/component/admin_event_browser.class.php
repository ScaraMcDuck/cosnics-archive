<?php
/**
 * @package tracking.lib.tracking_manager.component
 */
require_once dirname(__FILE__).'/../tracking_manager.class.php';
require_once dirname(__FILE__).'/../tracking_manager_component.class.php';
require_once dirname(__FILE__).'/admin_event_browser/event_browser_table.class.php';
require_once Path :: get(SYS_LIB_PATH).'/html/table/simple_table.class.php';

/**
 * Component for viewing tracker events 
 */
class TrackingManagerAdminEventBrowserComponent extends TrackingManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
        $admin = new AdminManager();
        $trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('EventsList')));
		
		if (!$this->get_user() || !$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$this->display_header($trail);
		
		$isactive = (PlatformSetting :: get('enable_tracking', 'tracking') == 1);
		
		if($isactive)
		{
			$output = $this->get_user_html();
			echo($output);
		}
		else
		{
			$this->display_error_message('<a href="' . $this->get_platform_administration_link() . '">' . Translation :: get('Tracking_is_disabled') . '</a>');
		}
		
		$this->display_footer();
	}
	
	function get_user_html()
	{		
		$table = new EventBrowserTable($this, null, array(Application :: PARAM_ACTION => TrackingManager :: ACTION_BROWSE_EVENTS), null);
		
		$html = array();
		$html[] = '<div>';
		$html[] = $table->as_html();
		$html[] = '</div>';
		
		return implode($html, "\n");
	}

}
?>