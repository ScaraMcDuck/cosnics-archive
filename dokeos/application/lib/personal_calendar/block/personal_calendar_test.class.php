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
class PersonalCalendarTest extends PersonalCalendarBlock
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
		
		$html[] = '<div class="block" id="b'. $this->get_block_info()->get_id() .'" style="background-image: url('.Theme :: get_common_img_path().'block_'.strtolower(PersonalCalendar :: APPLICATION_NAME).'.png);">';
		$html[] = '<div class="title">'. $this->get_block_info()->get_title() .'<a href="#" class="closeEl">[-]</a></div>';
		$html[] = '<div class="description">';
		
		$html[] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Fusce eget tortor. Mauris tristique nibh sagittis diam. Donec lobortis, lorem at condimentum tincidunt, lectus quam egestas est, quis vulputate sapien nunc et felis. Etiam iaculis dui at nulla. Quisque eu lectus. Duis tempus. Suspendisse ut nulla. Donec egestas metus hendrerit ipsum. Mauris fermentum metus at ipsum. Aenean eget eros quis purus iaculis ultrices. Nunc pede.';
		$html[] = '<br />';
		$html[] = 'Maecenas aliquet, metus semper placerat imperdiet, purus sem dignissim purus, ut sodales orci nulla non nisl. Quisque massa elit, pellentesque in, interdum nec, adipiscing at, nulla. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Phasellus velit pede, dignissim eu, posuere a, dignissim sed, nibh. Praesent venenatis risus sit amet augue. Curabitur lacinia orci sit amet erat. Pellentesque fringilla libero ac metus. Aenean rutrum. Proin sit amet leo id ipsum blandit lobortis. Proin nibh risus, tincidunt eu, auctor faucibus, elementum nec, ante. Ut in mauris. Phasellus ac arcu. In sed lectus. Donec aliquet iaculis nulla. Vestibulum sodales, nunc eget lacinia dictum, massa purus posuere dui, vel feugiat eros risus sit amet nibh. Nullam ut sapien id felis viverra pellentesque. Phasellus molestie vestibulum lorem. Proin pellentesque ligula.';
		
		$html[] = '<div style="clear: both;"></div>';
		$html[] = '</div>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
}
?>