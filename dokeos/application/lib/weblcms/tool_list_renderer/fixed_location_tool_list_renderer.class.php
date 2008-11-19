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
		//echo '<h4>'.Translation :: get('Tools').'</h4>';
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
		
		echo $this->display_block_header(Translation :: get('Tools'));
		$this->show_tools('basic',$tools);
		echo $this->display_block_footer();

		//echo '<h4>'.Translation :: get('Links').'</h4>';
		$this->show_links();
		
		if($this->group_inactive)
		{
			//echo '<h4>'.Translation :: get('DisabledTools').'</h4>';
			echo $this->display_block_header(Translation :: get('DisabledTools'));
			$this->show_tools('disabled',$tools);
			echo $this->display_block_footer();
		}
		
		if ($this->is_course_admin)
		{
			//echo '<h4>'.Translation :: get('CourseAdministration').'</h4>';
			echo $this->display_block_header(Translation :: get('CourseAdministration'));
			$this->show_tools('course_admin',$tools);
			echo $this->display_block_footer();
		}
		
		echo '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/home_ajax.js' .'"></script>';
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
		
		if($publications->size() > 0)
			echo $this->display_block_header(Translation :: get('Links'));
		
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
		
		if($publications->size() > 0)
			echo $this->display_block_footer();
	}
	
	function display_block_header($block_name)
	{
		$html = array();
		
		$html[] = '<div class="block" id="block_'. $block_name .'" style="background-image: url('.Theme :: get_img_path().'block_weblcms.png);">';
		$html[] = '<div class="title">'. $block_name;
		$html[] = '<a href="#" class="closeEl"><img class="visible" src="'.Theme :: get_common_img_path().'action_visible.png" /><img class="invisible" style="display: none;") src="'.Theme :: get_common_img_path().'action_invisible.png" /></a></div>';
		$html[] = '<div class="description">';
		
		return implode ("\n", $html);
	}
	
	function display_block_footer()
	{
		$html = array();
		
		$html[] = '<div style="clear: both;"></div>';
		$html[] = '</div>';
		$html[] = '</div>';
		
		return implode ("\n", $html);
	}
}
?>