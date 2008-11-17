<?php
/**
 * $Id$
 * @package application.weblcms
 */
require_once(dirname(__FILE__).'/../tool_list_renderer.class.php');
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
		$this->is_course_admin = $this->get_parent()->get_course()->is_course_admin($this->get_parent()->get_user());
	}
	// Inherited
	function display()
	{
		$parent = $this->get_parent();
		$tools = array ();
		echo '<h4>'.Translation :: get('Tools').'</h4>';
		foreach ($parent->get_registered_tools() as $tool)
		{
			$tools[$tool->section][] = $tool;
		}
		$this->show_tools('basic',$tools);

		if ($this->is_course_admin)
		{
			echo '<h4>'.Translation :: get('CourseAdministration').'</h4>';
			$this->show_tools('course_admin',$tools);
		}
		
		echo '<h4>'.Translation :: get('Links').'</h4>';
		$this->show_links();
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
				$visible_image = 'action_visible.png';
				$new = '';
				if($parent->tool_has_new_publications($tool->name))
				{
					$new = '_new';
				}
				$tool_image = 'tool_' . $tool->name . $new . '.png';
				$link_class='';
			}
			else
			{
				$lcms_action = 'make_visible';
				$visible_image = 'action_invisible.png';
				$tool_image = 'tool_' . $tool->name.'_na.png';
				$link_class=' class="invisible"';
			}
			$title = htmlspecialchars(Translation :: get(Tool :: type_to_class($tool->name).'Title'));
			$row = $count/FixedLocationToolListRenderer::NUMBER_OF_COLUMNS;
			$col = $count%FixedLocationToolListRenderer::NUMBER_OF_COLUMNS;
			$html = array();
			if($this->is_course_admin || $tool->visible)
			{
				// Show visibility-icon
				if ($this->is_course_admin && $section!= 'course_admin')
				{
					$html[] = '<a href="'.$parent->get_url(array(WebLcms :: PARAM_COMPONENT_ACTION=>$lcms_action,WebLcms :: PARAM_TOOL=>$tool->name)).'"><img src="'.Theme :: get_common_img_path().$visible_image.'" style="vertical-align: middle;" alt=""/></a>';
					$html[] = '&nbsp;&nbsp;&nbsp;';
				}
				
				// Show tool-icon + name
				$html[] = '<a href="'.$parent->get_url(array (WebLcms :: PARAM_COMPONENT_ACTION=>null,WebLcms :: PARAM_TOOL => $tool->name), true).'" '.$link_class.'>';
				$html[] = '<img src="'.Theme :: get_img_path().$tool_image.'" style="vertical-align: middle;" alt="'.$title.'"/>';
				$html[] = '&nbsp;';
				$html[] = $title;
				$html[] = '</a>';
				
				$table->setCellContents($row,$col,implode("\n",$html));
				$table->updateColAttributes($col,'style="width: '.floor(100/FixedLocationToolListRenderer::NUMBER_OF_COLUMNS).'%;"');
				$count++;
			}
		}
		$table->display();
	}
	
	private function show_links()
	{
		$parent = $this->get_parent();
		
		$condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_SHOW_ON_HOMEPAGE, 1);
		$publications = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publications($parent->get_course_id(), null, null, null, $condition);
		
		$table = new HTML_Table('style="width: 100%;"');
		$table->setColCount(FixedLocationToolListRenderer::NUMBER_OF_COLUMNS);
		$count = 0;
		while($publication = $publications->next_result())
		{
			if($publication->is_visible_for_target_users())
			{
				$lcms_action = 'make_publication_invisible';
				$visible_image = 'action_visible.png';
				$tool_image = 'tool_' . $publication->get_tool(). '.png';
				$link_class='';
			}
			else
			{
				$lcms_action = 'make_publication_visible';
				$visible_image = 'action_invisible.png';
				$tool_image = 'tool_' . $publication->get_tool() .'_na.png';
				$link_class=' class="invisible"';
			}
			$title = htmlspecialchars($publication->get_learning_object()->get_title());
			$row = $count/FixedLocationToolListRenderer::NUMBER_OF_COLUMNS;
			$col = $count%FixedLocationToolListRenderer::NUMBER_OF_COLUMNS;
			$html = array();
			if($this->is_course_admin || $publication->is_visible_for_target_users())
			{
				// Show visibility-icon
				if ($this->is_course_admin )
				{
					$html[] = '<a href="'.$parent->get_url(array(WebLcms :: PARAM_COMPONENT_ACTION=>$lcms_action,'pid' => $publication->get_id())).'"><img src="'.Theme :: get_common_img_path().$visible_image.'" style="vertical-align: middle;" alt=""/></a>';
					$html[] = '<a href="'.$parent->get_url(array(WebLcms :: PARAM_COMPONENT_ACTION=>'delete_publication','pid' => $publication->get_id())).'"><img src="'.Theme :: get_common_img_path().'action_delete.png" style="vertical-align: middle;" alt=""/></a>';
					$html[] = '&nbsp;&nbsp;&nbsp;';
				}
				
				// Show tool-icon + name
				$html[] = '<a href="'.$parent->get_url(array (WebLcms :: PARAM_COMPONENT_ACTION=>null,WebLcms :: PARAM_TOOL => $publication->get_tool(), 'pid' => $publication->get_id()), true).'" '.$link_class.'>';
				$html[] = '<img src="'.Theme :: get_img_path().$tool_image.'" style="vertical-align: middle;" alt="'.$title.'"/>';
				$html[] = '&nbsp;';
				$html[] = $title;
				$html[] = '</a>';
				
				$table->setCellContents($row,$col,implode("\n",$html));
				$table->updateColAttributes($col,'style="width: '.floor(100/FixedLocationToolListRenderer::NUMBER_OF_COLUMNS).'%;"');
				$count++;
			}
		}
		$table->display();
	}
}
?>