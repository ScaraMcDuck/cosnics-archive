<?php
require_once dirname(__FILE__).'/../learningobjectpublicationlistrenderer.class.php';

class WeekCalendarLearningObjectPublicationListRenderer extends LearningObjectPublicationListRenderer
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
		$week_number = date('W',$this->display_time);
		// Go 1 week back end them jump to the next monday to reach the first day of this week
		$first_day = strtotime('Monday',strtotime('-1 Week',$this->display_time));
		$last_day = strtotime('Sunday',$first_day);
		$calendar_table = new HTML_Table(array ('class' => 'calendar'));
		for($hour = 0; $hour < 24; $hour += 4)
		{
			$cell_content = $hour.'u - '.($hour+4).'u';
			$calendar_table->setCellContents($hour/4+1,0,$cell_content);
			for($column = 1; $column <= 7; $column++)
			{
				$day = strtotime('+'.($column-1).' day',$first_day);
				$default_start_date = mktime($hour,0,0,date('m',$day),date('d',$day),date('Y',$day));
				$params = array('default_start_date' => $default_start_date,'default_end_date' => strtotime('+'.(date('H',$default_start_date)+4).' hours',$default_start_date),'publish_action' => 'publicationcreator','calendaradmin' => '1');
				$add_url = $this->get_url($params);
				$calendar_table->setCellContents($hour/4+1,$column,'<div style="text-align:right;"><a href="'.$add_url.'">+</a></div>');
			}
		}
		$dates[] = '';
		$today = date('Y-m-d');
		for($day = 0; $day < 7; $day++)
		{
			$week_day = strtotime('+'.$day.' days',$first_day);
			$calendar_table->setCellContents(0,$day+1,date('l',$week_day).'<br/>'.date('Y-m-d',$week_day));
			if($today == date('Y-m-d',$week_day))
			{
				$calendar_table->updateColAttributes($day+1,'class="highlight"');
			}
		}
		$from_time = mktime(0,0,0,date('m',$first_day),date('d',$first_day),date('Y',$first_day));
		$to_time = mktime(23,59,59,date('m',$last_day),date('d',$last_day),date('Y',$last_day));
		$publications = $this->browser->get_calendar_events($from_time,$to_time);
		foreach($publications as $index => $publication)
		{
			$event = $publication->get_learning_object();
			$row = date('H',$event->get_start_date())/4+1;
			$col = date('w',$event->get_start_date());
			$col = ($col == 0 ? 7 : $col);
			$cell_contents = $calendar_table->getCellContents($row,$col);
			$cell_contents .= $this->render_publication($publication);
			$calendar_table->setCellContents($row,$col,$cell_contents);
		}
		$calendar_table->setRowType(0,'th');
		$calendar_table->setColType(0,'th');
		$prev = strtotime('-1 Week',$this->display_time);
		$next = strtotime('+1 Week',$this->display_time);
		$html[] = '<div style="text-align: center;">';
		$html[] =  '<a href="'.$this->get_url(array('time' => $prev), true).'">&lt;&lt;</a> ';
		$html[] =  get_lang('Week').' '.$week_number.' : '.date('l d M Y',$first_day).' - '.date('l d M Y',strtotime('+6 Days',$first_day));
		$html[] =  ' <a href="'.$this->get_url(array('time' => $next), true).'">&gt;&gt;</a> ';
		$html[] =  '</div>';
		$html[] = $calendar_table->toHtml();
		return implode("\n",$html);
	}
	function render_publication($publication)
	{
		$event = $publication->get_learning_object();
		$event_url = $this->get_url(array('pid'=>$publication->get_id()), true);
		return '<div class="event"><a href="'.$event_url.'">'.date('H:i',$event->get_start_date()).' '.htmlentities($event->get_title()).'</a></div>';
	}
}
?>