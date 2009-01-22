<?php
/**
 * @package admin.lib.admin_manager.component
 */
require_once dirname(__FILE__).'/../admin_manager.class.php';
require_once dirname(__FILE__).'/../admin_manager_component.class.php';
require_once dirname(__FILE__).'/whois_online_table/whois_online_table.class.php';
require_once dirname(__FILE__).'/../../../trackers/online_tracker.class.php';

/**
 * Component to view whois online
 */
class AdminWhoisOnlineComponent extends AdminManagerComponent
{
	function run()
	{
		$trail = new BreadcrumbTrail();		
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('WhoisOnline')));
		
		$world = PlatformSetting :: get('whoisonlineaccess');
		
		if($world == "1" || $this->get_user_id())
		{
			$output = $this->get_table_html();
			$this->display_header($trail, true);
			echo $output;
			$this->display_footer();
		}
		else
		{
			$this->display_header($trail, true);
			$this->display_error_message('NotAllowed');
			$this->display_footer();
		}
		
	}
	
	private function get_table_html()
	{
		$parameters = $this->get_parameters(true);
		
		$table = new WhoisOnlineTable($this, $parameters, $this->get_condition());
		
		$html = array();
		$html[] = $table->as_html();
		
		return implode($html, "\n");
	}
	
	function get_condition()
	{
		$tracking = new OnlineTracker();
		$items = $tracking->retrieve_tracker_items();
		foreach($items as $item)
			$users[] = $item->get_user_id();

		if($users)
			return new InCondition(User :: PROPERTY_USER_ID, $users);
	}

}
?>