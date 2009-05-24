<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms_manager.class.php';
require_once dirname(__FILE__).'/../weblcms_manager_component.class.php';
require_once dirname(__FILE__).'/../../course/course_import_form.class.php';

/**
 * Weblcms component allows the use to import a course
 */
class WeblcmsManagerCourseImporterComponent extends WeblcmsManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		Header :: set_section('admin');

		$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']."?application=weblcms", Translation :: get('MyCourses')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CourseImportCSV')));
        $trail->add_help('courses importer');

		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail, false, true);
			echo '<div class="clear"></div><br />';
			Display :: error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}

		$form = new CourseImportForm(CourseImportForm :: TYPE_IMPORT, $this->get_url());

		if($form->validate())
		{
			$success = $form->import_courses();
			$this->redirect(Translation :: get($success ? 'CourseCreatedCsv' : 'CourseNotCreatedCsv'). '<br />' .$form->get_failed_csv(), ($success ? false : true));
		}
		else
		{
			$this->display_header($trail, false, true);
			echo '<div class="clear"></div><br />';
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
		$html[] = '<b>code</b>;<b>title</b>;<b>category</b>;<b>teacher</b>';
		$html[] = 'BIO0015;Biology;BIO;username';
		$html[] = '</pre>';
		$html[] = '</blockquote>';

		echo implode($html, "\n");
	}
}
?>