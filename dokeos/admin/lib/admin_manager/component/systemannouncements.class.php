<?php
/**
 * @package admin.lib.admin_manager.component
 */
require_once dirname(__FILE__).'/../admin.class.php';
require_once dirname(__FILE__).'/../admincomponent.class.php';
require_once dirname(__FILE__).'/../adminsearchform.class.php';
/**
 * Admin component to manage system announcements
 */
class AdminSystemannouncementsComponent extends AdminComponent
{
    function run()
    {
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => 'index_admin.php', 'name' => Admin :: get_lang('PlatformAdmin'));
		$breadcrumbs[] = array ('url' => '', 'name' => Admin :: get_lang('SystemAnnouncements'));
		$this->display_header($breadcrumbs);
		echo 'TODO';
		$this->display_footer();
    }
}
?>