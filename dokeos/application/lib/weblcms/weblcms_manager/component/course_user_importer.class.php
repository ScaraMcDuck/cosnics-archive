<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms.class.php';
require_once dirname(__FILE__).'/../weblcms_component.class.php';
require_once dirname(__FILE__).'/../../course/course_user_import_form.class.php';

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
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CourseUserCreateCsv')));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$form = new CourseUserImportForm(CourseUserImportForm :: TYPE_IMPORT, $this->get_url());
		
		if($form->validate())
		{
			$success = $form->import_course_users();
			$this->redirect(null, Translation :: get($success ? 'CourseUserCreatedCsv' : 'CourseUserNotCreatedCsv'). '<br />' .$form->get_failed_csv(), ($success ? false : true));
		}
		else
		{
			$this->display_header($trail);
			$form->display();
			$this->display_extra_information();
			$this->display_footer();
		}
	}
	
	function display_extra_information()
	{
		$html = array();
		$html[] = '<p>'. Translation :: get('CSVMustLookLike') .' ('. Translation :: get('MandatoryFields') .')</p>';
		$html[] = '<blockquote>';
		$html[] = '<pre>';
		$html[] = '<b>UserName</b>;<b>CourseCode</b>;<b>Status</b>';
		$html[] = 'jdoe;course01;'. COURSEMANAGER;
		$html[] = 'a.dam;course01;'. STUDENT;
		$html[] = '</pre>';
		$html[] = COURSEMANAGER .': '. Translation :: get('Teacher');
		$html[] = STUDENT .': '. Translation :: get('Student');
		$html[] = '</blockquote>';
		
		echo implode($html, "\n");		
	}
}
?>