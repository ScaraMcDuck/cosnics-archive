<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms.class.php';
require_once dirname(__FILE__).'/../weblcmscomponent.class.php';
require_once dirname(__FILE__).'/../../course/courseuserimportform.class.php';

/**
 * Weblcms component allows the use to import course user relations
 */
class WeblcmsCourseUserImporterComponent extends WeblcmsComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		global $this_section;
		$this_section='platform_admin';
		
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('CourseUserCreateCsv'));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($breadcrumbs);
			Display :: display_error_message(get_lang("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$form = new CourseUserImportForm(CourseUserImportForm :: TYPE_IMPORT, $this->get_url());
		
		if($form->validate())
		{
			$success = $form->import_course_users();
			$this->redirect(null, get_lang($success ? 'CourseUserCreatedCsv' : 'CourseUserNotCreatedCsv'). '<br />' .$form->get_failed_csv(), ($success ? false : true));
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
		$html[] = '<b>UserName</b>;<b>CourseCode</b>;<b>Status</b>';
		$html[] = 'jdoe;course01;'. COURSEMANAGER;
		$html[] = 'a.dam;course01;'. STUDENT;
		$html[] = '</pre>';
		$html[] = COURSEMANAGER .': '. get_lang('Teacher');
		$html[] = STUDENT .': '. get_lang('Student');
		$html[] = '</blockquote>';
		
		echo implode($html, "\n");		
	}
}
?>