<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms.class.php';
require_once dirname(__FILE__).'/../weblcmscomponent.class.php';
require_once dirname(__FILE__).'/../../course/courseuserrelationform.class.php';
require_once dirname(__FILE__).'/../../course/courseusercategoryform.class.php';
/**
 * Weblcms component which allows the user to manage his or her course subscriptions
 */
class WeblcmsSorterComponent extends WeblcmsComponent
{
	private $category;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$component_action = $this->get_parameter(Weblcms::PARAM_COMPONENT_ACTION);
		
		switch($component_action)
		{
			case 'move':
				$this->move_course_list();
				break;
			case 'assign':
				$this->edit_course_category();
				break;
			case 'edit':
				$this->edit_course_user_category();
				break;
			case 'delete':
				$this->delete_course_user_category();
				break;
			default :
				$this->show_course_list();
		}
	}
	
	function move_course_list()
	{
		$direction = $_GET[Weblcms :: PARAM_DIRECTION];
		$course = $_GET[Weblcms :: PARAM_COURSE_USER];
		
		if (isset($direction) && isset($course))
		{
			$success = $this->move_course($course, $direction);
			$this->redirect(null, get_lang($success ? 'CourseUserMoved' : 'CourseUserNotMoved'), ($success ? false : true), array(Weblcms :: PARAM_COMPONENT_ACTION => Weblcms :: ACTION_MANAGER_SORT));
		}
		else
		{
			$this->show_course_list();
		}
	}
	
	function edit_course_category()
	{
		$course_id = $_GET[Weblcms :: PARAM_COURSE_USER];
		$courseuserrelation = $this->retrieve_course_user_relation($course_id, api_get_user_id());
		$form = new CourseUserRelationForm(CourseUserRelationForm :: TYPE_EDIT, $courseuserrelation, $this->get_url(array()));
		
		if($form->validate())
		{
			$success = $form->update_course_user_relation();
			$this->redirect(null, get_lang($success ? 'CourseUserCategoryUpdated' : 'CourseUserCategoryNotUpdated'), ($success ? false : true), array(Weblcms :: PARAM_COMPONENT_ACTION => Weblcms :: ACTION_MANAGER_SORT));
		}
		else
		{
			$this->display_header_courses();
			echo '<h3>'. get_lang('SetCourseUserCategory') .'</h3>';
			$form->display();
			$this->display_footer();
		}
	}
	
	function move_course($course, $direction)
	{
		$move_courseuserrelation = $this->retrieve_course_user_relation($course, api_get_user_id());
		$sort = $move_courseuserrelation->get_sort();
		
		if ($direction == 'up')
		{
			$next_courseuserrelation = $this->retrieve_course_user_relation_at_sort(api_get_user_id(), $move_courseuserrelation->get_category(), $sort-1);
			$move_courseuserrelation->set_sort($sort-1);
			$next_courseuserrelation->set_sort($sort);
		}
		elseif($direction == 'down')
		{
			$next_courseuserrelation = $this->retrieve_course_user_relation_at_sort(api_get_user_id(), $move_courseuserrelation->get_category(), $sort+1);
			$move_courseuserrelation->set_sort($sort+1);
			$next_courseuserrelation->set_sort($sort);
		}
		
		if ($move_courseuserrelation->update() && $next_courseuserrelation->update())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function add_course_user_category()
	{
		$courseusercategory = new CourseUserCategory();
		
		$form = new CourseUserCategoryForm(CourseUserCategoryForm :: TYPE_CREATE, $courseusercategory, $this->get_url());
		
		if($form->validate())
		{
			$success = $form->create_course_user_category();
			$this->redirect(null, get_lang($success ? 'CourseUserCategoryAdded' : 'CourseUserCategoryNotAdded'), ($success ? false : true));
		}
		else
		{
			$this->display_header_courses();
			echo '<h3>'. get_lang('CreateCourseUserCategory') .'</h3>';
			$form->display();
			$this->display_footer();
		}
	}
	
	function edit_course_user_category()
	{
		$course_user_category_id = $_GET[Weblcms :: PARAM_COURSE_USER_CATEGORY_ID];
		$courseusercategory = $this->retrieve_course_user_category($course_user_category_id);
		
		$form = new CourseUserCategoryForm(CourseUserCategoryForm :: TYPE_EDIT, $courseusercategory, $this->get_url(array(Weblcms :: PARAM_COURSE_USER_CATEGORY_ID => $course_user_category_id)));
		
		if($form->validate())
		{
			$success = $form->update_course_user_category();
			$this->redirect(null, get_lang($success ? 'CourseUserCategoryUpdated' : 'CourseUserCategoryNotUpdated'), ($success ? false : true), array(Weblcms :: PARAM_COMPONENT_ACTION => 'add', Weblcms :: PARAM_COURSE_USER_CATEGORY_ID => $course_user_category_id));
		}
		else
		{
			$this->display_header_courses();
			echo '<h3>'. get_lang('EditCourseUserCategory') .'</h3>';
			$form->display();
			$this->display_footer();
		}
	}
	
	function delete_course_user_category()
	{
		$course_user_category_id = $_GET[Weblcms :: PARAM_COURSE_USER_CATEGORY_ID];
		$courseusercategory = $this->retrieve_course_user_category($course_user_category_id);
		
		$relations = $this->retrieve_course_user_relations(api_get_user_id(), $courseusercategory->get_id());
		while ($relation = $relations->next_result())
		{
			$conditions = array();
			$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, api_get_user_id());
			$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_CATEGORY, 0);		
			$condition = new AndCondition($conditions);
			
			$sort = $this->retrieve_max_sort_value('course_rel_user', CourseUserRelation :: PROPERTY_SORT, $condition);
			
			$relation->set_sort($sort+1);
			$relation->update();
		}
		$success = $courseusercategory->delete();
		$this->redirect(null, get_lang($success ? 'CourseUserCategoryDeleted' : 'CourseUserCategoryNotDeleted'), ($success ? false : true), array(Weblcms :: PARAM_COMPONENT_ACTION => 'add'));
	}
	
	function show_course_list()
	{
		$this->display_header_courses();
		$this->display_footer();
	}
	
	function display_header_courses()
	{
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('CourseSorter'));
		$this->display_header($breadcrumbs);
		
		$course_categories = $this->retrieve_course_user_categories($this->get_user_id());
		$courses = $this->retrieve_courses($this->get_user_id(), 0);
		echo $this->display_course_digest($courses);
		
		while ($course_category = $course_categories->next_result())
		{
			$courses = $this->retrieve_courses($this->get_user_id(), $course_category->get_id());
			echo $this->display_course_digest($courses, $course_category);
		}
		
	}
	
	function display_course_digest($courses, $course_category = null)
	{
		$html = array();
		
		if (isset($course_category))
		{
			$html[] = '<div class="user_course_category">';
			$html[] = '<div class="title">';
			$html[] = htmlentities($course_category->get_title());
			$html[] = '</div>';
			$html[] = '<div class="options">';
			$html[] = $this->get_category_modification_links($course_category);
			$html[] = '</div>';
			$html[] = '<div style="clear:both;"></div>';
			$html[] = '</div>';
		}
		
		if($courses->size() > 0)
		{
			$html[] = '<div>';
			$key = 0;
			while ($course = $courses->next_result())
			{				
				$html[] = '<div class="user_course"><a href="'. $this->get_course_viewing_url($course) .'">'.$course->get_name().'</a><br />'. $course->get_id() .' - '. $course->get_titular() .'</div>';
				$html[] = '<div class="user_course_options">';
				$html[] = $this->get_course_modification_links($course, $key, $courses->size());
				$html[] = '</div>';
				$html[] = '<div style="clear:both;"></div>';
				$key++;
			}
			$html[] = '</div>';
		}
		return implode($html, "\n");
	}
	
	function get_course_modification_links($course, $key, $total)
	{
		$toolbar_data = array();
		
		if ($key > 0 && $total > 1)
		{
			$toolbar_data[] = array(
				'href' => $this->get_course_user_move_url($course, 'up'),
				'label' => get_lang('Up'),
				'img' => $this->get_web_code_path().'img/up.gif'
			);
		}
		else
		{
			$toolbar_data[] = array(
				'label' => get_lang('Up'),
				'img' => $this->get_web_code_path().'img/up_na.gif'
			);
		}
		
		if ($key < ($total - 1) && $total > 1)
		{
			$toolbar_data[] = array(
				'href' => $this->get_course_user_move_url($course, 'down'),
				'label' => get_lang('Down'),
				'img' => $this->get_web_code_path().'img/down.gif'
			);
		}
		else
		{
			$toolbar_data[] = array(
				'label' => get_lang('Up'),
				'img' => $this->get_web_code_path().'img/down_na.gif'
			);
		}
		
		$toolbar_data[] = array(
			'href' => $this->get_course_user_edit_url($course),
			'label' => get_lang('Edit'),
			'img' => $this->get_web_code_path().'img/edit.gif'
		);		

		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
	
	function get_category_modification_links($courseusercategory)
	{
		$toolbar_data = array();
		
		$toolbar_data[] = array(
			'href' => $this->get_course_user_category_edit_url($courseusercategory),
			'label' => get_lang('Edit'),
			'img' => $this->get_web_code_path().'img/edit.gif'
		);
			
		$toolbar_data[] = array(
			'href' => $this->get_course_user_category_delete_url($courseusercategory),
			'label' => get_lang('Delete'),
			'confirm' => true,
			'img' => $this->get_web_code_path().'img/delete.gif'
		);
		
		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
}
?>