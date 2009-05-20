<?php
/**
 * $Id: course_settings_tool.class.php 15449 2008-05-27 11:10:16Z Scara84 $
 * Course settings tool
 * @package application.weblcms.tool
 * @subpackage course_settings
 */
require_once dirname(__FILE__).'/../course_sections_tool_component.class.php';
require_once dirname(__FILE__).'/course_sections_browser/course_sections_browser_table.class.php';

class CourseSectionsToolViewerComponent extends CourseSectionsToolComponent
{
	private $action_bar;
	
	function run()
	{
		$trail = new BreadcrumbTrail();
		
		if (!$this->get_course()->is_course_admin($this->get_parent()->get_user()))
		{
			$this->display_header($trail, true, 'courses sections');
			Display :: error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$this->action_bar = $this->get_action_bar();
		$table = $this->get_table_html();
		
		$this->display_header($trail, true, 'courses sections');
		echo '<br />';
		echo $this->action_bar->as_html();
		echo '<div id="action_bar_browser">';
		echo $table;
		echo '</div>';
		$this->display_footer();
	}
	
	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		$action_bar->set_search_url($this->get_url());
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Create'), Theme :: get_common_image_path().'action_publish.png', $this->get_url(array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_CREATE_COURSE_SECTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->set_help_action(HelpManager :: get_tool_bar_help_item('course sections'));
		return $action_bar;
	}
	
	function get_table_html()
	{
		$table = new CourseSectionsBrowserTable($this, array(), $this->get_condition());
		
		$html = array();
		$html[] = '<div style="float: right; width: 100%;">';
		$html[] = $table->as_html();
		$html[] = '</div>';
		
		return implode($html, "\n");
	}
	
	function get_condition()
	{
		return new EqualityCondition(CourseSection :: PROPERTY_COURSE_CODE, $this->get_course_id());
	}
}
?>