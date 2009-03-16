<?php
/**
 * $Id: glossary_tool.class.php 16640 2008-10-29 11:12:07Z Scara84 $
 * Announcement tool
 * @package application.weblcms.tool
 * @subpackage glossary
 */

require_once dirname(__FILE__).'/glossary_tool_component.class.php';
/**
 * This tool allows a user to publish glossarys in his or her course.
 */
class GlossaryTool extends Tool
{
	const ACTION_BROWSE_GLOSSARIES = 'browse';
	const ACTION_VIEW_GLOSSARY = 'view';
	
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
			case self :: ACTION_BROWSE_GLOSSARIES :
				$component = GlossaryToolComponent :: factory('Browser', $this);
				break;
			case self :: ACTION_VIEW_GLOSSARY : 
				$component = GlossaryToolComponent :: factory('Viewer', $this);
				break;
			case self :: ACTION_PUBLISH :
				$component = GlossaryToolComponent :: factory('Publisher', $this);
				break;
				
			default :
				$component = GlossaryToolComponent :: factory('Browser', $this);
		}
		$component->run();
	}
	
	static function get_allowed_types()
	{
		return array('glossary');
	}
}
?>