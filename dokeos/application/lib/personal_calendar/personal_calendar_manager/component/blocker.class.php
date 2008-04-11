<?php
/**
 * @package application.lib.calendar.calendar_manager
 */
require_once dirname(__FILE__).'/../personal_calendar.class.php';
require_once dirname(__FILE__).'/../personalcalendarcomponent.class.php';
require_once dirname(__FILE__).'/../../personalcalendarblock.class.php';

class PersonalCalendarBlockerComponent extends PersonalCalendarComponent
{	
	/**
	 * Runs this component and displays its output.
	 */
	function render_block($type, $block_info)
	{
		$block = new PersonalCalendarBlock($this, $type, $block_info);
		$html[] =  $block->run();
		return implode($html, "\n");
	}
}
?>