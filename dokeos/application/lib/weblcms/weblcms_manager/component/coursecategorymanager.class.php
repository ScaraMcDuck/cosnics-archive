<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms.class.php';
require_once dirname(__FILE__).'/../weblcmscomponent.class.php';
require_once dirname(__FILE__).'/coursecategorybrowser/coursecategorybrowsertable.class.php';
require_once dirname(__FILE__).'/../../course/coursecategoryform.class.php';

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
		
		$component_action = $this->get_parameter(Weblcms::PARAM_COMPONENT_ACTION);
		
		switch($component_action)
		{
			case 'edit':
				$this->edit_course_category();
				break;
			case 'delete':
				$this->edit_course_category();
				break;
			case 'add':
				$this->add_course_category();
				break;
			default :
				$this->show_course_category_list();
		}
	}
	
	function show_course_category_list()
	{
		$this->display_header_course_categories();
		$this->display_footer();
	}
	
	function display_header_course_categories()
	{
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('CourseCategoryManager'));
		$this->display_header($breadcrumbs);
		
		$table = new CourseCategoryBrowserTable($this, null, null, null);
		echo $table->as_html();
	}
	
	function add_course_category()
	{
		$coursecategory = new CourseCategory();
		
		$form = new CourseCategoryForm(CourseCategoryForm :: TYPE_CREATE, $coursecategory, $this->get_url());
		
		if($form->validate())
		{
			$success = $form->create_course_category();
			$this->redirect(null, get_lang($success ? 'CourseCategoryAdded' : 'CourseCategoryNotAdded'), ($success ? false : true));
		}
		else
		{
			$this->display_header_course_categories();
			echo '<h3>'. get_lang('CreateCourseCategory') .'</h3>';
			$form->display();
			$this->display_footer();
		}
	}
}
?>