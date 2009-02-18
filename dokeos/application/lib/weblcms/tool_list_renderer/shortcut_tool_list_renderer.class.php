<?php
/**
 * $Id: menu_tool_list_renderer.class.php 16816 2008-11-19 20:25:56Z Scara84 $
 * @package application.weblcms
 */
require_once(dirname(__FILE__).'/../tool_list_renderer.class.php');
require_once('HTML/Table.php');
/**
 * Tool list renderer to display a navigation menu.
 */
class ShortcutToolListRenderer extends ToolListRenderer
{
	/**
	 *
	 */
	private $is_course_admin;
	/**
	 * Constructor
	 * @param  WebLcms $parent The parent application
	 */
	function MenuToolListRenderer($parent)
	{
		parent::ToolListRenderer($parent);
		$this->is_course_admin = $this->get_parent()->get_course()->is_course_admin($this->get_parent()->get_user());
	}
	/**
	 * Sets the type of this navigation menu renderer
	 * @param int $type
	 */
	function set_type($type)
	{
		$this->type = $type;
	}
	// Inherited
	function display()
	{
		$parent = $this->get_parent();
		$tools = $parent->get_registered_tools();
		$this->show_tools($tools);
	}
	/**
	 * Show the tools of a given section
	 * @param array $tools
	 */
	private function show_tools($tools)
	{
		$parent = $this->get_parent();
		$course = $parent->get_course();
		
		foreach ($tools as $index => $tool)
		{
			$sections = WeblcmsDataManager :: get_instance()->retrieve_course_sections(new EqualityCondition('id', $tool->section));
			$section = $sections->next_result();
			
			if(!PlatformSetting :: get($tool->name . '_active', 'weblcms') && $section->get_type() != CourseSection :: TYPE_ADMIN)
				continue;
				
			if((($tool->visible && $tool->section != 'course_admin') || $this->is_course_admin) && $tool->visible)
			{
				$new = '';
				if($parent->tool_has_new_publications($tool->name))
				{
					$new = '_new';
				}
				$tool_image = 'tool_mini_' . $tool->name . $new . '.png';
				$title = htmlspecialchars(Translation :: get(Tool :: type_to_class($tool->name).'Title'));
				$html[] = '<a href="'.$parent->get_url(array (WebLcms :: PARAM_ACTION=>Weblcms :: ACTION_VIEW_COURSE,WebLcms :: PARAM_TOOL => $tool->name), true).'" title="'.$title.'">';
				$html[] = '<img src="'.Theme :: get_image_path().$tool_image.'" style="vertical-align: middle;" alt="'.$title.'"/> ';
				$html[] = '</a>';
			}
		}
		echo implode("\n",$html);
	}
}
?>