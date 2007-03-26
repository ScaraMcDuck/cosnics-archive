<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms.class.php';
require_once dirname(__FILE__).'/../weblcmscomponent.class.php';
/**
 * Weblcms component which provides the course page
 */
class WeblcmsCourseComponent extends WeblcmsComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		Display :: display_header(get_lang('MyCourses'), 'Mycourses');
		$this->display_footer();
	}
}
?>