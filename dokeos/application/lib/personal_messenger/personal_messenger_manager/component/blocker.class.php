<?php
/**
 * @package application.lib.calendar.calendar_manager
 */
require_once dirname(__FILE__).'/../personal_messenger.class.php';
require_once dirname(__FILE__).'/../personalmessengercomponent.class.php';
require_once dirname(__FILE__).'/../../personalmessengerblock.class.php';

class PersonalMessengerBlockerComponent extends PersonalMessengerComponent
{	
	/**
	 * Runs this component and displays its output.
	 */
	function render_block($type, $block_info)
	{
		$block = new PersonalMessengerBlock($this, $type, $block_info);
		$html[] =  $block->run();
		return implode($html, "\n");
	}
}
?>