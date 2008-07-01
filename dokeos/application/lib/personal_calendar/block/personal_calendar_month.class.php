<?php
/**
 * @package application.lib.calendar.publisher
 */
require_once dirname(__FILE__).'/../personal_calendar_block.class.php';

require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learning_object_display.class.php';
require_once dirname(__FILE__).'/../renderer/personal_calendar_mini_month_renderer.class.php';
/**
 * This class represents a calendar publisher component which can be used
 * to browse through the possible learning objects to publish.
 */
class PersonalCalendarMonth extends PersonalCalendarBlock
{
	function run()
	{
		return $this->as_html();
	}
	
	/*
	 * Inherited
	 */
	function as_html()
	{
		$html = array();
		
		$html[] = '<div class="block" id="block_'. $this->get_block_info()->get_id() .'" style="background-image: url('.Theme :: get_img_path().'block_'.strtolower(PersonalCalendar :: APPLICATION_NAME).'.png);">';
		$html[] = '<div class="title">'. $this->get_block_info()->get_title() .'<a href="#" class="closeEl"><img class="visible"'. ($this->get_block_info()->is_visible() ? ' style="display: block"' : ' style="display: none"') .' src="'.Theme :: get_common_img_path().'action_visible.png" /><img class="invisible"'. ($this->get_block_info()->is_visible() ? ' style="display: none"' : ' style="display: block"') .' src="'.Theme :: get_common_img_path().'action_invisible.png" /></a></div>';
		$html[] = '<div class="description"'. ($this->get_block_info()->is_visible() ? '' : ' style="display: none"') .'>';
		
		$time = isset ($_GET['time']) ? intval($_GET['time']) : time();
		$minimonthcalendar = new PersonalCalendarMiniMonthRenderer($this->get_parent(), $time, 'link');
		$html[] =   $minimonthcalendar->render();
		
		$html[] = '<div style="clear: both;"></div>';
		$html[] = '</div>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
}
?>