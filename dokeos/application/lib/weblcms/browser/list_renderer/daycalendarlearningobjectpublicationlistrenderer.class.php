<?php
/**
 * @package application.weblcms
 * @subpackage browser.listrenderer
 */
require_once dirname(__FILE__).'/../learningobjectpublicationlistrenderer.class.php';

class DayCalendarLearningObjectPublicationListRenderer extends LearningObjectPublicationListRenderer
{
	/**
	 *
	 */
	private $display_time;
	/**
	 *
	 */
	function set_display_time($time)
	{
		$this->display_time = $time;
	}
	/**
	 *
	 */
	function as_html()
	{
		$calendar_table = new HTML_Table(array ('class' => 'calendar'));
		for($hour = 0; $hour < 24; $hour += 2)
		{
			$default_start_date = mktime($hour,0,0,date('m',$this->display_time),date('d',$this->display_time),date('Y',$this->display_time));
			$params = array('default_start_date' => $default_start_date,'default_end_date' => strtotime('+'.(date('H',$default_start_date)+2).' hours',$default_start_date),'publish_action' => 'publicationcreator','admin' => '1');
			$add_url = $this->get_url($params);
			$cell_contents = '<a href="'.$add_url.'">'.$hour.'u - '.($hour+2).'u'.'</a>';
			$calendar_table->setCellContents($hour/2,0,$cell_contents);

		}
		$from_time = mktime(0,0,0,date('m',$this->display_time),date('d',$this->display_time),date('Y',$this->display_time));
		$to_time = mktime(23,59,59,date('m',$this->display_time),date('d',$this->display_time),date('Y',$this->display_time));
		$publications = $this->browser->get_calendar_events($from_time,$to_time);
		foreach($publications as $index => $publication)
		{
			$event = $publication->get_learning_object();
			$event_url = $this->get_url(array('pid'=>$publication->get_id()), true);
			$row = 	date('H',$event->get_start_date())/2;
			$cell_contents = $calendar_table->getCellContents($row,0);
			$cell_contents .= '<div class="event"><a href="'.$event_url.'">'.date('H:i',$event->get_start_date()).' '.htmlentities($event->get_title()).'</a><br/>'.$event->get_description().'</div>';
			$calendar_table->setCellContents($row,0,$cell_contents);
		}
		$prev = strtotime('-1 Day',$this->display_time);
		$next = strtotime('+1 Day',$this->display_time);
		$html[] = '<div style="text-align: center;">';
		$html[] =  '<a href="'.$this->get_url(array('time' => $prev), true).'">&lt;&lt;</a> ';
		$html[] =  date('l d F Y',$this->display_time);
		$html[] =  ' <a href="'.$this->get_url(array('time' => $next), true).'">&gt;&gt;</a> ';
		$html[] =  '</div>';
		$html[] = $calendar_table->toHtml();
		return implode("\n",$html);
	}
}
?>