<?php
/**
 * @package application.lib.calendar.calendar_manager
 */
require_once dirname(__FILE__).'/../weblcms.class.php';
require_once dirname(__FILE__).'/../weblcmscomponent.class.php';
require_once dirname(__FILE__).'/../../weblcmsblock.class.php';

class WeblcmsBlockerComponent extends WeblcmsComponent
{	
	/**
	 * Runs this component and displays its output.
	 */
	function render_block($type, $block_info)
	{
		$block = new WeblcmsBlock($this, $type, $block_info);
		$html[] =  $block->run();
		return implode($html, "\n");
	}
}
?>