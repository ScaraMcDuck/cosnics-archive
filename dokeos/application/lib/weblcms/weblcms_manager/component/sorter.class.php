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
			case 'add':
				$this->add_course_user_category();
				break;
			case 'move':
				$this->move_course_list();
				break;
			case 'movecat':
				$this->move_category_list();
				break;
			case 'assign':
				$this->assign_course_category();
				break;
			case 'edit':
				$this->edit_course_user_category();
				break;
			case 'delete':
				$this->delete_course_user_category();
				break;
			case 'view':
				$this->show_course_list();
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
			$this->redirect(null, Translation :: get($success ? 'CourseUserMoved' : 'CourseUserNotMoved'), ($success ? false : true), array(Weblcms :: PARAM_COMPONENT_ACTION => Weblcms :: ACTION_MANAGER_SORT));
		}
		else
		{
			$this->show_course_list();
		}
	}
	
	function move_category_list()
	{
		$direction = $_GET[Weblcms :: PARAM_DIRECTION];
		$category = $_GET[Weblcms :: PARAM_COURSE_CATEGORY_ID];
		
		if (isset($direction) && isset($category))
		{
			$success = $this->move_category($category, $direction);
			$this->redirect(null, Translation :: get($success ? 'CourseUserCategoryMoved' : 'CourseUserCategoryNotMoved'), ($success ? false : true), array(Weblcms :: PARAM_COMPONENT_ACTION => Weblcms :: ACTION_MANAGER_SORT));
		}
		else
		{
			$this->show_course_list();
		}
	}
	
	function assign_course_category()
	{
		$course_id = $_GET[Weblcms :: PARAM_COURSE_USER];
		$courseuserrelation = $this->retrieve_course_user_relation($course_id, $this->get_user_id());
		$form = new CourseUserRelationForm(CourseUserRelationForm :: TYPE_EDIT, $courseuserrelation, $this->get_user(), $this->get_url(array()));
		
		if($form->validate())
		{
			$success = $form->update_course_user_relation();
			$this->redirect(null, Translation :: get($success ? 'CourseUserCategoryUpdated' : 'CourseUserCategoryNotUpdated'), ($success ? false : true), array(Weblcms :: PARAM_COMPONENT_ACTION => Weblcms :: ACTION_MANAGER_SORT));
		}
		else
		{
			$this->display_page_header(Translation :: get('SetCourseUserCategory'));
			$form->display();
//			echo '<h3>'. Translation :: get('UserCourseList') .'</h3>';
//			$this->display_courses();
			$this->display_footer();
		}
	}
	
	function move_course($course, $direction)
	{
		$move_courseuserrelation = $this->retrieve_course_user_relation($course, $this->get_user_id());
		$sort = $move_courseuserrelation->get_sort();
		$next_courseuserrelation = $this->retrieve_course_user_relation_at_sort($this->get_user_id(), $move_courseuserrelation->get_category(), $sort, $direction);
		
		if ($direction == 'up')
		{
			$move_courseuserrelation->set_sort($sort-1);
			$next_courseuserrelation->set_sort($sort);
		}
		elseif($direction == 'down')
		{
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
	
	function move_category($courseusercategory, $direction)
	{
		$move_category = $this->retrieve_course_user_category($courseusercategory, $this->get_user_id());
		$sort = $move_category->get_sort();
		$next_category = $this->retrieve_course_user_category_at_sort($this->get_user_id(), $sort, $direction);
		
		if ($direction == 'up')
		{
			$move_category->set_sort($sort-1);
			$next_category->set_sort($sort);
		}
		elseif($direction == 'down')
		{
			$move_category->set_sort($sort+1);
			$next_category->set_sort($sort);
		}
		
		if ($move_category->update() && $next_category->update())
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
		
		$form = new CourseUserCategoryForm(CourseUserCategoryForm :: TYPE_CREATE, $courseusercategory, $this->get_user(), $this->get_url());
		
		if($form->validate())
		{
			$success = $form->create_course_user_category();
			$this->redirect(null, Translation :: get($success ? 'CourseUserCategoryAdded' : 'CourseUserCategoryNotAdded'), ($success ? false : true), array(Weblcms :: PARAM_COMPONENT_ACTION => 'view'));
		}
		else
		{
			$this->display_page_header(Translation :: get('CreateCourseUserCategory'));
			$form->display();
//			echo '<h3>'. Translation :: get('UserCourseList') .'</h3>';
//			$this->display_courses();
			$this->display_footer();
		}
	}
	
	function edit_course_user_category()
	{
		$course_user_category_id = $_GET[Weblcms :: PARAM_COURSE_USER_CATEGORY_ID];
		$courseusercategory = $this->retrieve_course_user_category($course_user_category_id);
		
		$form = new CourseUserCategoryForm(CourseUserCategoryForm :: TYPE_EDIT, $courseusercategory, $this->get_user(), $this->get_url(array(Weblcms :: PARAM_COURSE_USER_CATEGORY_ID => $course_user_category_id)));
		
		if($form->validate())
		{
			$success = $form->update_course_user_category();
			$this->redirect(null, Translation :: get($success ? 'CourseUserCategoryUpdated' : 'CourseUserCategoryNotUpdated'), ($success ? false : true), array(Weblcms :: PARAM_COMPONENT_ACTION => 'view', Weblcms :: PARAM_COURSE_USER_CATEGORY_ID => $course_user_category_id));
		}
		else
		{
			$this->display_page_header(Translation :: get('EditCourseUserCategory'));
			$form->display();
//			echo '<h3>'. Translation :: get('UserCourseList') .'</h3>';
//			$this->display_courses();
			$this->display_footer();
		}
	}
	
	function delete_course_user_category()
	{
		$course_user_category_id = $_GET[Weblcms :: PARAM_COURSE_USER_CATEGORY_ID];
		$courseusercategory = $this->retrieve_course_user_category($course_user_category_id);
		
		$relations = $this->retrieve_course_user_relations($this->get_user_id(), $course_user_category_id);
		
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $this->get_user_id());
		$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_CATEGORY, 0);		
		$condition = new AndCondition($conditions);
			
		$sort = $this->retrieve_max_sort_value('course_rel_user', CourseUserRelation :: PROPERTY_SORT, $condition);
			
		while ($relation = $relations->next_result())
		{
			$relation->set_sort($sort+1);
			$relation->update();
			$sort++;
		}
		
		$success = $courseusercategory->delete();
		$this->redirect(null, Translation :: get($success ? 'CourseUserCategoryDeleted' : 'CourseUserCategoryNotDeleted'), ($success ? false : true), array(Weblcms :: PARAM_COMPONENT_ACTION => 'view'));
	}
	
	function show_course_list()
	{
		$this->display_page_header(Translation :: get('SortMyCourses'));
		$this->display_courses();
		$this->display_footer();
	}
	
	function display_page_header($title)
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(null, false, true, array(Weblcms :: PARAM_ACTION)), Translation :: get('MyCourses')));
		$trail->add(new Breadcrumb($this->get_url(), $title));
		$this->display_header($trail);
	}
	
	function display_courses()
	{
		
		echo $this->get_sort_modification_links();
		
		$condition = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $this->get_user_id());
		
		$course_categories = $this->retrieve_course_user_categories($condition, null, null, array(CourseUserCategory :: PROPERTY_SORT), array(SORT_ASC));
		$courses = $this->retrieve_courses($this->get_user_id(), 0);
		echo $this->display_course_digest($courses);
		
		$cat_key = 0;
		while ($course_category = $course_categories->next_result())
		{
			$courses = $this->retrieve_courses($this->get_user_id(), $course_category->get_id());
			echo $this->display_course_digest($courses, $course_category, $cat_key, $course_categories->size());
			$cat_key++;
		}
		
	}
	
	function display_course_digest($courses, $course_category = null, $cat_key = null, $cat_count = null)
	{
		$html = array();
		
		if (isset($course_category))
		{
			$html[] = '<div class="user_course_category">';
			$html[] = '<div class="title">';
			$html[] = htmlentities($course_category->get_title());
			$html[] = '</div>';
			$html[] = '<div class="options">';
			$html[] = $this->get_category_modification_links($course_category, $cat_key, $cat_count);
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
				'label' => Translation :: get('Up'),
				'img' => Theme :: get_common_img_path().'up.png'
			);
		}
		else
		{
			$toolbar_data[] = array(
				'label' => Translation :: get('Up'),
				'img' => Theme :: get_common_img_path().'up_na.png'
			);
		}
		
		if ($key < ($total - 1) && $total > 1)
		{
			$toolbar_data[] = array(
				'href' => $this->get_course_user_move_url($course, 'down'),
				'label' => Translation :: get('Down'),
				'img' => Theme :: get_common_img_path().'down.png'
			);
		}
		else
		{
			$toolbar_data[] = array(
				'label' => Translation :: get('Up'),
				'img' => Theme :: get_common_img_path().'down_na.png'
			);
		}
		
		$toolbar_data[] = array(
			'href' => $this->get_course_user_edit_url($course),
			'label' => Translation :: get('Edit'),
			'img' => Theme :: get_common_img_path().'edit.png'
		);
		
		$toolbar_data[] = array(
			'img' => Theme :: get_common_img_path().'spacer.gif'
		);

		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
	
	function get_category_modification_links($courseusercategory, $key, $total)
	{
		$toolbar_data = array();
		
		if ($key > 0 && $total > 1)
		{
			$toolbar_data[] = array(
				'href' => $this->get_course_user_category_move_url($courseusercategory, 'up'),
				'label' => Translation :: get('Up'),
				'img' => Theme :: get_common_img_path().'up.gif'
			);
		}
		else
		{
			$toolbar_data[] = array(
				'label' => Translation :: get('Up'),
				'img' => Theme :: get_common_img_path().'up_na.gif'
			);
		}
		
		if ($key < ($total - 1) && $total > 1)
		{
			$toolbar_data[] = array(
				'href' => $this->get_course_user_category_move_url($courseusercategory, 'down'),
				'label' => Translation :: get('Down'),
				'img' => Theme :: get_common_img_path().'down.gif'
			);
		}
		else
		{
			$toolbar_data[] = array(
				'label' => Translation :: get('Up'),
				'img' => Theme :: get_common_img_path().'down_na.gif'
			);
		}
		
		$toolbar_data[] = array(
			'href' => $this->get_course_user_category_edit_url($courseusercategory),
			'label' => Translation :: get('Edit'),
			'img' => Theme :: get_common_img_path().'edit.png'
		);
			
		$toolbar_data[] = array(
			'href' => $this->get_course_user_category_delete_url($courseusercategory),
			'label' => Translation :: get('Delete'),
			'confirm' => true,
			'img' => Theme :: get_common_img_path().'delete.png'
		);
		
		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
	
	function get_sort_modification_links()
	{
		$toolbar_data = array();
			
		$toolbar_data[] = array(
			'href' => $this->get_course_user_category_add_url(),
			'label' => Translation :: get('CreateCourseUserCategory'),
			'img' => Theme :: get_common_img_path().'folder.gif',
			'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
		);
		
		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
}
?>