<?php
/**
 * @package tracking.lib.tracking_manager.component
 */
require_once dirname(__FILE__).'/../trackingmanager.class.php';
require_once dirname(__FILE__).'/../trackingmanagercomponent.class.php';
require_once dirname(__FILE__).'/admintrackingbrowser/admintrackingbrowsercellrenderer.class.php';
require_once Path :: get(SYS_LIB_PATH).'/html/table/simpletable.class.php';

/**
 * Component for viewing tracker events 
 */
class TrackingManagerAdminTrackingBrowserComponent extends TrackingManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('TrackersList')));
		
		if (!$this->get_user() || !$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$this->display_header($trail);

		$properties = $this->get_properties();
		$cellrenderer = new AdminTrackingBrowserCellRenderer($this);
		
		/*echo('<div style="margin-bottom: 5px;"> ' . Translation :: get_lang('LeftContentboxes')  . '</div>');
		$lefttable = new SimpleTable($properties, $this->retrieve_contentboxes('left'), $cellrenderer);
		echo $lefttable->toHTML();
		
		echo('<br /><div style="margin-bottom: 5px;"> ' . Translation :: get_lang('RightContentboxes') . '</div>');
		
		$righttable = new SimpleTable($properties, $this->retrieve_contentboxes('right'), $cellrenderer);
		echo $righttable->toHTML();*/
		
		$this->display_footer();
	}
	
	function get_properties()
	{
		/*return array(
					ContentBox :: PROPERTY_ID,
					ContentBox :: PROPERTY_PATH,
					ContentBox :: PROPERTY_NAME,
					ContentBox :: PROPERTY_ORDER,
						);*/
	}

}
?>