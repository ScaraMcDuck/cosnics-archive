<?php
/**
 * $Id$
 * @package application.weblcms
 */
require_once(dirname(__FILE__).'/../toollistrenderer.class.php');
require_once('HTML/Table.php');
/**
 * Tool list renderer which displays all course tools in different sections. One
 * section with the active course tools, one section with the course admin tools
 * and one section with all disabled tools.
 */
class DisabledSectionToolListRenderer extends ToolListRenderer
{
	/**
	 * The number of columns
	 */
	const NUMBER_OF_COLUMNS = 4;
	/**
	 * Constructor
	 * @param  WebLcms $parent The parent application
	 */
	function DisabledSectionToolListRenderer($parent)
	{
		parent::ToolListRenderer($parent);
	}
	// Inherited
	function display()
	{
		$parent = $this->get_parent();
		$tools = array ();
		foreach ($parent->get_registered_tools() as $tool)
		{
			if($tool->visible)
			{
				$tools[$tool->section][] = $tool;
			}
			else
			{
				$tools['disabled'][] = $tool;
			}
		}
		$this->show_tools('basic',$tools);
		echo '<h4>'.Translation :: get('CourseAdministration').'</h4>';
		$this->show_tools('course_admin',$tools);
		echo '<h4>'.Translation :: get('DisabledTools').'</h4>';
		$this->show_tools('disabled',$tools);
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
		$table->setColCount(DisabledSectionToolListRenderer::NUMBER_OF_COLUMNS);
		$count = 0;
		foreach ($tools as $index => $tool)
		{
			if($tool->visible || $section == 'course_admin')
			{
				$action = 'make_invisible';
				$visible_image = 'action_visible.png';
				$new = '';
				if($parent->tool_has_new_publications($tool->name))
				{
					$new = '_new';
				}
				$tool_image = 'tool_' . $tool->name .  $new .'.png';
				$link_class='';
			}
			else
			{
				$action = 'make_visible';
				$visible_image = 'action_invisible.png';
				$tool_image = 'tool_' . $tool->name.'_na.png';
				$link_class=' class="invisible"';
			}
			$title = htmlspecialchars(Translation :: get(Tool :: type_to_class($tool->name).'Title'));
			$row = $count/DisabledSectionToolListRenderer::NUMBER_OF_COLUMNS;
			$col = $count%DisabledSectionToolListRenderer::NUMBER_OF_COLUMNS;
			$html = array();
			$html[] = '<a href="'.$parent->get_url(array (WebLcms :: PARAM_ACTION=>null,WebLcms :: PARAM_TOOL => $tool->name), true).'" '.$link_class.'>';
			$html[] = '<img src="'.Theme :: get_img_path().$tool_image.'" style="vertical-align: middle;"/>';
			$html[] = $title;
			$html[] = '</a>';
			if($section!= 'course_admin')
			{
				$html[] = '<a href="'.$parent->get_url(array(WebLcms :: PARAM_ACTION=>$action,WebLcms :: PARAM_TOOL=>$tool->name)).'"><img src="'.Theme :: get_common_img_path().$visible_image.'"/></a>';
			}
			$table->setCellContents($row,$col,implode("\n",$html));
			$table->updateColAttributes($col,'style="width: '.floor(100/DisabledSectionToolListRenderer::NUMBER_OF_COLUMNS).'%;"');
			$count++;
		}
		$table->display();
	}
}
?>