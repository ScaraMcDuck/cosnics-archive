<?php
/**
 * $Id: glossary_tool.class.php 16640 2008-10-29 11:12:07Z Scara84 $
 * Announcement tool
 * @package application.weblcms.tool
 * @subpackage glossary
 */

require_once dirname(__FILE__) . '/../complex_display.class.php';
require_once dirname(__FILE__) . '/glossary_display_component.class.php';
/**
 * This tool allows a user to publish glossarys in his or her course.
 */
class GlossaryDisplay extends ComplexDisplay
{
	const ACTION_VIEW_GLOSSARY = 'view';

	/**
	 * Inherited.
	 */
	function run()
	{
		$action = $this->get_parent()->get_action();

		switch ($action)
		{
			case self :: ACTION_VIEW_GLOSSARY :
				$component = GlossaryDisplayComponent :: factory('GlossaryViewer', $this);
				break;
			default :
				$component = GlossaryDisplayComponent :: factory('GlossaryViewer', $this);
		}
		$component->run();
	}
}
?>