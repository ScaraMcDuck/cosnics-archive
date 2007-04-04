<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms.class.php';
require_once dirname(__FILE__).'/../weblcmscomponent.class.php';
require_once dirname(__FILE__).'/coursecategorybrowser/coursecategorybrowsertable.class.php';

/**
 * Weblcms component allows the use to create a course
 */
class WeblcmsCourseCategoryManagerComponent extends WeblcmsComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		if (!api_is_platform_admin())
		{
			$breadcrumbs = array();
			$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('CourseCategoryManager'));
			$this->display_header($breadcrumbs);
			Display :: display_error_message(get_lang("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('CourseCategoryManager'));
		
		$this->display_header($breadcrumbs);
		
		$table = new CourseCategoryBrowserTable($this, null, null, null);
		echo $table->as_html();
		$this->display_footer();
	}
}
?>