<?php

/**
 * @package admin.component
 */
require_once dirname(__FILE__).'/../admin.class.php';
require_once dirname(__FILE__).'/../admincomponent.class.php';
/**
 * Admin component
 */
class AdminBrowserComponent extends AdminComponent
{	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => '', 'name' => get_lang('PlatformAdmin'));
		
		if (!api_is_platform_admin())
		{
			$this->display_header($breadcrumbs);
			Display :: display_error_message(get_lang("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$this->display_header($breadcrumbs);
		foreach ($this->get_application_platform_admin_links() as $application_links)
		{
			echo $application_links['application'] . '<br />';
		}
		$this->display_footer();
	}
}
?>