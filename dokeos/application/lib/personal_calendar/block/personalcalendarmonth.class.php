<?php
/**
 * @package application.lib.calendar.publisher
 */
require_once dirname(__FILE__).'/../personalcalendarblock.class.php';
require_once dirname(__FILE__).'/../personalcalendarblockcomponent.class.php';

require_once dirname(__FILE__).'/../../../../repository/lib/repositoryutilities.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learningobjectdisplay.class.php';
require_once dirname(__FILE__).'/../renderer/personal_calendar_mini_month_renderer.class.php';
/**
 * This class represents a calendar publisher component which can be used
 * to browse through the possible learning objects to publish.
 */
class PersonalCalendarMonth extends PersonalCalendarBlockComponent
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
		
		$html[] = '<div class="block" style="background-image: url('.Theme :: get_common_img_path().'block_'.strtolower(PersonalCalendar :: APPLICATION_NAME).'.png);">';
		$html[] = '<div class="title">'. $this->get_block_info()->get_title() .'<a href="#" class="closeEl">[-]</a></div>';
		$html[] = '<div class="description" style="text-align: center;">';
		
		$time = isset ($_GET['time']) ? intval($_GET['time']) : time();
		$minimonthcalendar = new PersonalCalendarMiniMonthRenderer($this, $time, 'link');
		$html[] =   $minimonthcalendar->render();
		
		$html[] = '<div style="clear: both;"></div>';
		$html[] = '</div>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
}
?>