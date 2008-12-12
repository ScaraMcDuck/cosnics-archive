<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms.class.php';
require_once dirname(__FILE__).'/../weblcms_component.class.php';
require_once dirname(__FILE__).'/../../course/course_category_menu.class.php';
require_once dirname(__FILE__).'/admin_course_browser/admin_course_browser_table.class.php';
/**
 * Weblcms component which allows the the platform admin to browse the courses
 */
class WeblcmsAdminCourseBrowserComponent extends WeblcmsComponent
{
	private $category;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		global $this_section;
		$this_section='platform_admin';
		
		$this->category = $_GET[Weblcms :: PARAM_COURSE_CATEGORY_ID];
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CourseList')));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$menu = $this->get_menu_html();
		$output = $this->get_course_html();
		
		$this->display_header($trail, true);
		echo $menu;
		echo $output;
		$this->display_footer();
	}
	
	function get_course_html()
	{		
		$table = new AdminCourseBrowserTable($this, null, $this->get_condition());
		
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
			$search['title'] = Translation :: get('SearchResults');
			$search['url'] = $search_url;
			$search['class'] = 'search_results';
			$extra_items[] = $search;
		}
		else
		{
			$search_url = null;
		}
		
		$temp_replacement = '__CATEGORY_ID__';
		$url_format = $this->get_url(array (Weblcms :: PARAM_ACTION => Weblcms :: ACTION_ADMIN_COURSE_BROWSER, Weblcms :: PARAM_COURSE_CATEGORY_ID => $temp_replacement));
		$url_format = str_replace($temp_replacement, '%s', $url_format);
		$category_menu = new CourseCategoryMenu($this->category, $url_format, $extra_items);
		
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
	
	function get_condition()
	{
		$search_conditions = $this->get_search_condition();
		
		$condition = null;
		if (isset($this->category))
		{
			$condition = new EqualityCondition(Course :: PROPERTY_CATEGORY, $this->category);
			
			if (count($search_conditions))
			{
				$condition = new AndCondition($condition, $search_conditions);
			}
		}
		else
		{
			if (count($search_conditions))
			{
				$condition = $search_conditions;
			}
		}
		
		return $condition;
	}
}
?>