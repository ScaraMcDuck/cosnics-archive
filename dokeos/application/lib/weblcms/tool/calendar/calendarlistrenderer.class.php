<?php
/**
 * Calendar tool - list renderer
 * @package application.weblcms.tool
 * @subpackage calendar
 */
require_once dirname(__FILE__).'/../../browser/list_renderer/listlearningobjectpublicationlistrenderer.class.php';
/**
 * A renderer to display a list view of a calendar
 */
class CalendarListRenderer extends ListLearningObjectPublicationListRenderer
{
	function as_html()
	{
		$publications = $this->get_publications();
		foreach ($publications as $index => $publication)
		{
			$first = $index == 0;
			$last = $index == count($publications) - 1;
			$rendered_publications[$publication->get_learning_object()->get_start_date()][] = $this->render_publication($publication, $first, $last);
		}
		ksort($rendered_publications);
		$current_month = 0;
		foreach($rendered_publications as $start_time => $rendered_publication_start_time)
		{
			if(date('Ym',$start_time) != $current_month)
			{
				$current_month = date('Ym',$start_time);
				//TODO: date i8n
				$html[] = '<h3>'.date('F Y',$start_time).'</h3>';
			}
			$html[] = implode("\n",$rendered_publication_start_time);
		}
		return implode("\n", $html);
	}
	/**
	 * Render the description of the calendar event publication
	 */
	function render_description($publication)
	{
		$event = $publication->get_learning_object();
		$html[] = '<em>';
		//TODO: date formatting
		$html[] = get_lang('From').': '.date('r',$event->get_start_date());
		$html[] = '<br />';
		//TODO: date formatting
		$html[] = get_lang('To').': '.date('r',$event->get_end_date());
		$html[] = '</em>';
		$html[] = '<br />';
		$html[] = $event->get_description();
		return implode("\n",$html);
	}
	/**
	 * Calendar events are sorted chronologically. So the up-action is not
	 * available here.
	 * @return empty string
	 */
	function render_up_action()
	{
		return '';
	}
	/**
	 * Calendar events are sorted chronologically. So the down-action is not
	 * available here.
	 * @return empty string
	 */
	function render_down_action()
	{
		return '';
	}
}
?>