<?php
/**
 * @package admin.lib.admin_manager.component
 */
require_once dirname(__FILE__).'/../admin.class.php';
require_once dirname(__FILE__).'/../admin_component.class.php';
require_once dirname(__FILE__).'/../admin_search_form.class.php';
/**
 * Admin component to manage system announcements
 */
class AdminSystemAnnouncementsComponent extends AdminComponent
{
    function run()
    {
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('PlatformAdmin')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('SystemAnnouncements')));
    	
		$this->display_header($trail);
		echo 'TODO';
		$this->display_footer();
    }
}
?>