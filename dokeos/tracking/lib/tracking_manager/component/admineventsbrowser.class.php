<?php
/**
 * @package tracking.lib.tracking_manager.component
 */
require_once dirname(__FILE__).'/../trackingmanager.class.php';
require_once dirname(__FILE__).'/../trackingmanagercomponent.class.php';
require_once dirname(__FILE__).'/admineventsbrowser/admineventsbrowsercellrenderer.class.php';
require_once dirname(__FILE__).'/admineventsbrowser/admineventsbrowseractionhandler.class.php';
require_once Path :: get(SYS_LIB_PATH).'/html/table/simpletable.class.php';

/**
 * Component for viewing tracker events 
 */
class TrackingManagerAdminEventsBrowserComponent extends TrackingManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('EventsList')));
		
		if (!$this->get_user() || !$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$cellrenderer = new AdminEventsBrowserCellRenderer($this);
		$actionhandler = new AdminEventsBrowserActionHandler($this);
		
		$previous_events_block = null;
		
		$events = $this->retrieve_events();
		$blockevents = array();
		
		$tables = array();
		
		foreach($events as $event)
		{
			if(($event->get_block() != $previous_events_block))
			{
				if($previous_events_block != null) 
				{
					$tables[$blockevents[0]->get_block()] = $this->create_table($blockevents, $cellrenderer, $actionhandler);
				}
					
				$blockevents = array();
			}

			$blockevents[] = $event;
			$previous_events_block = $event->get_block();
		
		}
		
		$tables[$blockevents[0]->get_block()] = $this->create_table($blockevents, $cellrenderer, $actionhandler);
		
		$this->display_header($trail);
		
		$isactive = (PlatformSetting :: get('enable_tracking', 'tracker') == 1);
		
		$toolbar_data = array();
		
		$toolbar_data[] = array(
			'href' => $this->get_change_active_url('all'),
			'label' => ($isactive == 1)?Translation :: get('Deactivate_all_events'):Translation :: get('Activate_all_events'),
			'confirm' => false,
			'img' => ($isactive == 1)?
				Path :: get(WEB_LAYOUT_PATH).'img/visible.gif':
				Path :: get(WEB_LAYOUT_PATH).'img/invisible.gif'
		);
		
		$toolbar_data[] = array(
			'href' => $this->get_empty_tracker_url('all'),
			'label' => Translation :: get('Empty_all_events'),
			'confirm' => true,
			'img' => Path :: get(WEB_LAYOUT_PATH).'img/recycle_bin.gif'
		);
		
		echo '<div style="width: 100%; text-align: right"><div>Entire tracking options: </div>' . 
			RepositoryUtilities :: build_toolbar($toolbar_data) . '</div><br />';
		
		if($isactive)
		{
			foreach($tables as $key => $table)
				$this->display_table($key, $table);
		}
		
		$this->display_footer();
	}
	
	function create_table($events, $cellrenderer, $actionhandler)
	{
		if(count($events) == 0) return;
		return new SimpleTable($events, $cellrenderer, $actionhandler, $events[0]->get_block());	
	}
	
	function display_table($block, $table)
	{
		echo('<div style="margin-bottom: 5px;"> ' . Translation :: get('Events_for_block') . ': ' . $block . '</div>');
		echo $table->toHTML() . '<br />';
	}

}
?>