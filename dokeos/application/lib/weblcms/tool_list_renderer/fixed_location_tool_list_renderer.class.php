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
	private $number_of_columns = 2;
	private $group_inactive;
	private $is_course_admin;
	/**
	 * Constructor
	 * @param  WebLcms $parent The parent application
	 */
	function FixedLocationToolListRenderer($parent)
	{
		parent::ToolListRenderer($parent);
		$course = $parent->get_course();
		$this->number_of_columns = ($course->get_layout() % 2 == 0)?3:2;
		$this->group_inactive = ($course->get_layout() > 2);
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
			if($this->group_inactive)
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
			else
			{
				$tools[$tool->section][] = $tool;
			}
		}
		$this->show_tools('basic',$tools);

		echo '<h4>'.Translation :: get('Links').'</h4>';
		$this->show_links();
		
		echo '<h4>'.Translation :: get('Disabled').'</h4>';
		if($this->group_inactive)
		{
			$this->show_tools('disabled',$tools);
		}
		
		if ($this->is_course_admin)
		{
			echo '<h4>'.Translation :: get('CourseAdministration').'</h4>';
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
		$table->setColCount($this->number_of_columns);
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
			$row = $count/$this->number_of_columns;
			$col = $count%$this->number_of_columns;
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
				$table->updateColAttributes($col,'style="width: '.floor(100/$this->number_of_columns).'%;"');
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
		$table->setColCount($this->number_of_columns);
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
			$row = $count/$this->number_of_columns;
			$col = $count%$this->number_of_columns;
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
				$table->updateColAttributes($col,'style="width: '.floor(100/$this->number_of_columns).'%;"');
				$count++;
			}
		}
		$table->display();
	}
}
?>