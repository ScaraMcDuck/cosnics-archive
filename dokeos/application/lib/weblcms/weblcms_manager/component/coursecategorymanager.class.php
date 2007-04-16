<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/coursecategorybrowser/coursecategorybrowsertable.class.php';
require_once dirname(__FILE__).'/../weblcms.class.php';
require_once dirname(__FILE__).'/../weblcmscomponent.class.php';
require_once dirname(__FILE__).'/../../course/coursecategoryform.class.php';
require_once dirname(__FILE__).'/../../course/coursecategorymenu.class.php';

/**
 * Weblcms component allows the use to create a course
 */
class WeblcmsCourseCategoryManagerComponent extends WeblcmsComponent
{
	private $category;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		if (!$this->get_user()->is_platform_admin())
		{
			$breadcrumbs = array();
			$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('CourseCategoryManager'));
			$this->display_header($breadcrumbs);
			Display :: display_error_message(get_lang("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$this->category = $_GET[Weblcms :: PARAM_COURSE_CATEGORY_ID];
		
		$component_action = $this->get_parameter(Weblcms::PARAM_COMPONENT_ACTION);
		
		switch($component_action)
		{
			case 'edit':
				$this->edit_course_category();
				break;
			case 'delete':
				$this->delete_course_category();
				break;
			case 'add':
				$this->add_course_category();
				break;
			case 'view':
				$this->show_course_category_list();
				break;
			default :
				$this->show_course_category_list();
		}
	}
	
	function show_course_category_list()
	{
		$this->display_page_header(get_lang('CourseCategoryManager'));
		$this->display_course_categories();
		$this->display_footer();
	}
	
	function display_page_header($title)
	{
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => $title);
		$this->display_header($breadcrumbs);
	}
	
	function display_course_categories()
	{
		echo $this->get_course_category_manager_modification_links();
		echo '<div style="clear: both;">&nbsp;</div>';
		echo $this->get_menu_html();
		echo $this->get_course_category_html();
	}
	
	function get_course_category_html()
	{
		$table = new CourseCategoryBrowserTable($this, null, null,$this->get_condition());
		
		$html = array();
		$html[] = '<div style="float: right; width: 80%;">';
		$html[] = $table->as_html();
		$html[] = '</div>';
		
		return implode($html, "\n");
	}
	
	function get_menu_html()
	{
		$extra_items = array ();
		if ($this->get_search_validate())
		{
			// $search_url = $this->get_url();
			$search_url = '#';
			$search = array ();
			$search['title'] = get_lang('SearchResults');
			$search['url'] = $search_url;
			$search['class'] = 'search_results';
			$extra_items[] = & $search;
		}
		else
		{
			$search_url = null;
		}
		
		$temp_replacement = '__CATEGORY_ID__';
		$url_format = $this->get_url(array (Weblcms :: PARAM_ACTION => Weblcms :: ACTION_COURSE_CATEGORY_MANAGER, Weblcms :: PARAM_COURSE_CATEGORY_ID => $temp_replacement));
		$url_format = str_replace($temp_replacement, '%s', $url_format);
		$category_menu = new CourseCategoryMenu($this->category, $url_format);
		
		if (isset ($search_url))
		{
			$category_menu->forceCurrentUrl($search_url, true);
		}
		
		$html = array();
		$html[] = '<div style="float: left; width: 20%;">';
		$html[] = $category_menu->render_as_tree();
		$html[] = '</div>';
		
		return implode($html, "\n");
	}
	
	function add_course_category()
	{
		$coursecategory = new CourseCategory();
		
		$coursecategory->set_auth_cat_child(1);
		$coursecategory->set_auth_course_child(1);
		
		$form = new CourseCategoryForm(CourseCategoryForm :: TYPE_CREATE, $coursecategory, $this->get_url());
		
		if($form->validate())
		{
			$success = $form->create_course_category();
			$this->redirect(null, get_lang($success ? 'CourseCategoryAdded' : 'CourseCategoryNotAdded'), ($success ? false : true));
		}
		else
		{
			$this->display_page_header(get_lang('CreateCourseCategory'));
			$form->display();
			echo '<h3>'. get_lang('CourseCategoryList') .'</h3>';
			$this->display_course_categories();
			$this->display_footer();
		}
	}
	
	function edit_course_category()
	{
		$course_category_code = $_GET[Weblcms :: PARAM_COURSE_CATEGORY_ID];
		$course_category = $this->retrieve_course_category($course_category_code);
		
		$form = new CourseCategoryForm(CourseCategoryForm :: TYPE_EDIT, $course_category, $this->get_url(array(Weblcms :: PARAM_COURSE_CATEGORY_ID => $course_category_code)));
		
		if($form->validate())
		{
			$success = $form->update_course_category();
			$this->redirect(null, get_lang($success ? 'CourseCategoryUpdated' : 'CourseCategoryNotUpdated'), ($success ? false : true), array(Weblcms :: PARAM_COURSE_CATEGORY_ID => $course_category_code));
		}
		else
		{
			$this->display_page_header(get_lang('UpdateCourseCategory'));
			$form->display();
			echo '<h3>'. get_lang('CourseCategoryList') .'</h3>';
			$this->display_course_categories();
			$this->display_footer();
		}
	}
	
	function delete_course_category()
	{
		$course_category_id = $_GET[Weblcms :: PARAM_COURSE_CATEGORY_ID];
		$coursecategory = $this->retrieve_course_category($course_category_id);
		
		$success = $coursecategory->delete();
		$this->redirect(null, get_lang($success ? 'CourseCategoryDeleted' : 'CourseCategoryNotDeleted'), ($success ? false : true), array(Weblcms :: PARAM_COMPONENT_ACTION => 'view'));
	}
	
	function get_course_category_manager_modification_links()
	{
		$toolbar_data = array();
			
		$toolbar_data[] = array(
			'href' => $this->get_course_category_add_url(),
			'label' => get_lang('CreateCourseCategory'),
			'img' => $this->get_web_code_path().'img/folder.gif',
			'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
		);
		
		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
	
	function get_condition()
	{
		//$search_conditions = $this->get_search_condition();
		
		$condition = null;
		if (isset($this->category))
		{
			$condition = new EqualityCondition(CourseCategory :: PROPERTY_PARENT, $this->category);
			
//			if (count($search_conditions))
//			{
//				$condition = new AndCondition($condition, $search_conditions);
//			}
		}
//		else
//		{
//			if (count($search_conditions))
//			{
//				$condition = $search_conditions;
//			}
//		}
		
		return $condition;
	}
}
?>