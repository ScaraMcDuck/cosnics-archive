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
		if (!api_is_platform_admin())
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
			$this->redirect(null, get_lang($success ? 'CourseCreatedCsv' : 'CourseNotCreatedCsv'). '<br />' .$form->get_failed_csv(), ($success ? false : true));
		}
		else
		{
			$this->display_header($breadcrumbs);
			$form->display();
			$this->display_extra_information();
			$this->display_footer();
		}
	}
	
	function display_extra_information()
	{
		$html = array();
		$html[] = '<p>'. get_lang('CSVMustLookLike') .' ('. get_lang('MandatoryFields') .')</p>';
		$html[] = '<blockquote>';
		$html[] = '<pre>';
		$html[] = '<b>Code</b>;<b>Title</b>;<b>CourseCategory</b>;<b>Teacher</b>';
		$html[] = 'BIO0015;Biology;BIO;username';
		$html[] = '</pre>';
		$html[] = '</blockquote>';
		
		echo implode($html, "\n");		
	}
}
?>