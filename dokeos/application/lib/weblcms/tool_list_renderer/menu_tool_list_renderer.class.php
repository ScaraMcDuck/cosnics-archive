<?php
/**
 * $Id$
 * @package application.weblcms
 */
require_once(dirname(__FILE__).'/../tool_list_renderer.class.php');
require_once('HTML/Table.php');
/**
 * Tool list renderer to display a navigation menu.
 */
class MenuToolListRenderer extends ToolListRenderer
{
	/**
	 *
	 */
	const MENU_TYPE_TOP_NAVIGATION = 1;
	const MENU_TYPE_LIST_NAVIGATION = 2;
	/**
	 *
	 */
	private $is_course_admin;
	/**
	 *
	 */
	private $type;
	/**
	 * Constructor
	 * @param  WebLcms $parent The parent application
	 */
	function MenuToolListRenderer($parent, $type = MENU_TYPE_LIST_NAVIGATION)
	{
		parent::ToolListRenderer($parent);
		$this->is_course_admin = $this->get_parent()->get_course()->is_course_admin($this->get_parent()->get_user());
		$this->type = $type;
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
		if( $this->type == MENU_TYPE_LIST_NAVIGATION)
		{
			$html[] = '<div id="tool_bar_left" class="tool_bar_left">';
			$html[] = '<div class="tool_menu">';
			
			$html[] = '<ul>';
		}
		
		foreach ($tools as $index => $tool)
		{
			if((($tool->visible && $tool->section != 'course_admin') || $this->is_course_admin) && $tool->visible)
			{
				$new = '';
				if($parent->tool_has_new_publications($tool->name))
				{
					$new = '_new';
				}
				$tool_image = 'tool_mini_' . $tool->name . $new . '.png';
				$title = htmlspecialchars(Translation :: get(Tool :: type_to_class($tool->name).'Title'));
				if( $this->type == MENU_TYPE_LIST_NAVIGATION)
				{
					$html[] = '<li class="tool_list_menu">';
				}
				$html[] = '<a href="'.$parent->get_url(array (WebLcms :: PARAM_ACTION=>Weblcms :: ACTION_VIEW_COURSE,WebLcms :: PARAM_TOOL => $tool->name), true).'" title="'.$title.'">';
				$html[] = '<img src="'.Theme :: get_img_path().$tool_image.'" style="vertical-align: middle;" alt="'.$title.'"/> ';
				$html[] = '</a>';
				if( $this->type == MENU_TYPE_LIST_NAVIGATION)
				{
					$html[] = '<a href="'.$parent->get_url(array (WebLcms :: PARAM_ACTION=>null,WebLcms :: PARAM_TOOL => $tool->name), true).'" title="'.$title.'">';
					$html[] = $title;
					$html[] = '</a>';
					$html[] = '</li>';
				}
			}
		}
		if( $this->type == MENU_TYPE_LIST_NAVIGATION)
		{
			$html[] = '</ul>';
			$html[] = '</div>';
			$html[] = '<div class="clear"></div>';
		
			$html[] = '<div id="tool_bar_left_hide_container" class="hide">';
			$html[] = '<a id="tool_bar_left_hide" href="#"><img src="'. Theme :: get_common_img_path() .'action_action_bar_hide.png" /></a>';
			$html[] = '<a id="tool_bar_left_show" href="#"><img src="'. Theme :: get_common_img_path() .'action_action_bar_show.png" /></a>';
			$html[] = '</div>';
			$html[] = '</div>';
			$html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/tool_bar.js' .'"></script>';
			$html[] = '<div class="clear"></div>';
		}
		echo implode("\n",$html);
	}
}
?>