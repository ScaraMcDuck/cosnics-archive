<?php
/**
 * @package users.lib.usermanager.component
 */
require_once dirname(__FILE__).'/../user_manager.class.php';
require_once dirname(__FILE__).'/../user_manager_component.class.php';
require_once dirname(__FILE__).'/../../buddy_list.class.php';

class UserManagerBuddyListViewerComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		global $this_section;
		$this_section='myaccount';

		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BuddyList')));

		$this->display_header($trail, false, 'user general');
		echo "<br />";
		
		$buddylist = new BuddyList($this->get_user(), $this);
		echo $buddylist->to_html();
		
		$this->display_footer();
	}
}
?>