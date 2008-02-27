<?php
/**
 * $Id$
 * @package application.weblcms
 */
require_once(dirname(__FILE__).'/../toollistrenderer.class.php');
require_once('HTML/Table.php');
/**
 * Tool list renderer which displays all course tools on a fixed location.
 * Disabled tools will be shown in a disabled looking way.
 */
class FixedLocationToolListRenderer extends ToolListRenderer
{
	/**
	 * Number of columns.
	 */
	const NUMBER_OF_COLUMNS = 3;
	/**
	 *
	 */
	private $is_course_admin;
	/**
	 * Constructor
	 * @param  WebLcms $parent The parent application
	 */
	function FixedLocationToolListRenderer($parent)
	{
		parent::ToolListRenderer($parent);
		$this->is_course_admin = $this->get_parent()->get_course()->is_course_admin($this->get_parent()->get_user_id());
	}
	// Inherited
	function display()
	{
		$parent = $this->get_parent();
		$tools = array ();
		foreach ($parent->get_registered_tools() as $tool)
		{
			$tools[$tool->section][] = $tool;
		}
		$this->show_tools('basic',$tools);

		if ($this->is_course_admin)
		{
			echo '<h4>'.Translation :: get_lang('CourseAdministration').'</h4>';
			$this->show_tools('course_admin',$tools);
		}
	}
	/**
	 * Show the tools of a given section
	 * @param string $section
	 * @param array $tools
	 */
	private function show_tools($section, $tools)
	{
		$tools = $tools[$section];
		$parent = $this->get_parent();
		$table = new HTML_Table('style="width: 100%;"');
		$table->setColCount(FixedLocationToolListRenderer::NUMBER_OF_COLUMNS);
		$count = 0;
		foreach ($tools as $index => $tool)
		{
			if($tool->visible || $section == 'course_admin')
			{
				$lcms_action = 'make_invisible';
				$visible_image = 'visible.gif';
				$new = '';
				if($parent->tool_has_new_publications($tool->name))
				{
					$new = '_new';
				}
				$tool_image = $tool->name.'_tool'.$new.'.gif';
				$link_class='';
			}
			else
			{
				$lcms_action = 'make_visible';
				$visible_image = 'invisible.gif';
				$tool_image = $tool->name.'_tool_na.gif';
				$link_class=' class="invisible"';
			}
			$title = htmlspecialchars(Translation :: get_lang(Tool :: type_to_class($tool->name).'Title'));
			$row = $count/FixedLocationToolListRenderer::NUMBER_OF_COLUMNS;
			$col = $count%FixedLocationToolListRenderer::NUMBER_OF_COLUMNS;
			$html = array();
			if($this->is_course_admin || $tool->visible)
			{
				$html[] = '<a href="'.$parent->get_url(array (WebLcms :: PARAM_COMPONENT_ACTION=>null,WebLcms :: PARAM_TOOL => $tool->name), true).'" '.$link_class.'>';
			}
			$html[] = '<img src="'.$parent->get_path(WEB_IMG_PATH).$tool_image.'" style="vertical-align: middle;" alt="'.$title.'"/>';
			$html[] = $title;
			if($this->is_course_admin || $tool->visible)
			{
				$html[] = '</a>';
			}
			if($this->is_course_admin && $section!= 'course_admin')
			{
				$html[] = '<a href="'.$parent->get_url(array(WebLcms :: PARAM_COMPONENT_ACTION=>$lcms_action,WebLcms :: PARAM_TOOL=>$tool->name)).'"><img src="'.$parent->get_path(WEB_IMG_PATH).$visible_image.'" alt=""/></a>';
			}
			$table->setCellContents($row,$col,implode("\n",$html));
			$table->updateColAttributes($col,'style="width: '.floor(100/FixedLocationToolListRenderer::NUMBER_OF_COLUMNS).'%;"');
			$count++;
		}
		$table->display();
	}
}
?>