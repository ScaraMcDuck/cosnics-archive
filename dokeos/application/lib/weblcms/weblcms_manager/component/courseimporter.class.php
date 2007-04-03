<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms.class.php';
require_once dirname(__FILE__).'/../weblcmscomponent.class.php';
require_once dirname(__FILE__).'/../../course/courseimportform.class.php';

/**
 * Weblcms component allows the use to create a course
 */
class WeblcmsCourseImporterComponent extends WeblcmsComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		if (!api_is_allowed_to_create_course() || !api_is_platform_admin())
		{
			$breadcrumbs = array();
			$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('CourseCreateCsv'));
			$this->display_header($breadcrumbs);
			Display :: display_error_message(get_lang("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$form = new CourseImportForm(CourseImportForm :: TYPE_IMPORT, $this->get_url());
		
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('CourseCreateCsv'));
		
		if($form->validate())
		{
			$success = $form->import_courses();
			$this->redirect(Weblcms :: ACTION_VIEW_WEBLCMS_HOME, get_lang($success ? 'CourseCreatedCsv' : 'CourseNotCreatedCsv'), ($success ? false : true));
		}
		else
		{
			$this->display_header($breadcrumbs);
			$form->display();
			$this->display_footer();
		}
	}
}
?>