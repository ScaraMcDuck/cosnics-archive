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
	const ACTION_BROWSE_GLOSSARIES = 'browse';
	const ACTION_VIEW_GLOSSARY = 'view';
    const ACTION_PUBLISH = 'publish';

	/**
	 * Inherited.
	 */
	function run()
	{
		$action = $this->get_parent()->get_action();
		/*$component = parent :: run();

		if($component)
		{
			return;
		}*/

		switch ($action)
		{
			case self :: ACTION_BROWSE_GLOSSARIES :
				$component = GlossaryDisplayComponent :: factory('GlossaryBrowser', $this);
				break;
			case self :: ACTION_VIEW_GLOSSARY :
				$component = GlossaryDisplayComponent :: factory('GlossaryViewer', $this);
				break;
			case self :: ACTION_PUBLISH :
				$component = GlossaryDisplayComponent :: factory('GlossaryPublisher', $this);
				break;

			default :
				$component = GlossaryDisplayComponent :: factory('GlossaryBrowser', $this);
		}
		$component->run();
	}

	static function get_allowed_types()
	{
		return array('glossary');
	}
}
?>