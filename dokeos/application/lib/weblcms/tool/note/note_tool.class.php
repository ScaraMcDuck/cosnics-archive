<?php
/**
 * $Id: note_tool.class.php 16640 2008-10-29 11:12:07Z Scara84 $
 * Note tool
 * @package application.weblcms.tool
 * @subpackage note
 */

require_once dirname(__FILE__).'/note_tool_component.class.php';
/**
 * This tool allows a user to publish notes in his or her course.
 */
class NoteTool extends Tool
{
	const ACTION_VIEW_NOTES = 'view';
	
	/**
	 * Inherited.
	 */
	function run()
	{
		$action = $this->get_action();
		$component = parent :: run();
		
		if($component)
		{
			return;
		}
		
		switch ($action)
		{
			case self :: ACTION_VIEW_NOTES :
				$component = NoteToolComponent :: factory('Viewer', $this);
				break;
			case self :: ACTION_PUBLISH :
				$component = NoteToolComponent :: factory('Publisher', $this);
				break;
				
			default :
				$component = NoteToolComponent :: factory('Viewer', $this);
		}
		$component->run();
	}
}
?>