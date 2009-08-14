<?php
/**
 */
require_once dirname(__FILE__).'/../reservations_manager.class.php';
require_once dirname(__FILE__).'/../reservations_manager_component.class.php';
require_once dirname(__FILE__).'/../../calendar/reservations_calendar_week_renderer.class.php';
require_once 'Pager/Pager.php';

/**
 * Component to delete an item
 */
class ReservationsManagerOverviewBrowserComponent extends ReservationsManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{ 
		//Header
		$trail = new BreadCrumbTrail();
		$trail->add(new BreadCrumb($this->get_url(), Translation :: get('Statistics')));
		
		$this->display_header($trail);
		
		//Toolbar
		$tb_data[] = array(
				'href' => $this->get_manage_overview_url(),
				'label' => Translation :: get('ManageOverview'),
				'img' => Theme :: get_theme_path() . 'action_statistics.png',
				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
		);
		
		echo DokeosUtilities :: build_toolbar($tb_data) . '<br /><br />';
		
		//Paging
		$condition = new EqualityCondition(OverviewItem :: PROPERTY_USER_ID, $this->get_user_id());
		$count = $this->count_overview_items($condition);
		
		$pager_options = array(
		    'mode'       => 'Sliding',
		    'perPage'    => 5,
		    'totalItems' => $count,
		);
		
		$pager = Pager::factory($pager_options);
		list($from, $to) = $pager->getOffsetByPageId();
		
		if($pager->links)
			echo $pager->links . '<br /><br />';
		
		// Overview list
		$overview_items = $this->retrieve_overview_items($condition, $from - 1, $to);
		
		if($overview_items->size() == 0)
			$this->display_message(Translation :: get('NoItemsSelected'));
		
		while($overview_item = $overview_items->next_result())
		{
			$item = $this->retrieve_items(new EqualityCondition(Item :: PROPERTY_ID, $overview_item->get_item_id()))->next_result();
			if(!$item) continue;
			
			echo '<h3>' . $item->get_name() . '</h3>';
			
			$time = Request :: get('time');
			$time = $time ? $time : time();
			$calendar = new ReservationsCalendarWeekRenderer($this, $time);
			echo $calendar->render($overview_item->get_item_id());
			echo '<div class="clear">&nbsp;</div><br />';
		}
		
		// Footer
		echo $pager->links;
		$this->display_footer();
	}

}
?>