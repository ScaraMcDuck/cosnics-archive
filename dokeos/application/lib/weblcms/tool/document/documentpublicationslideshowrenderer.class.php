<?php
/**
 * $Id: documentpublicationlistrenderer.class.php 12389 2007-05-14 11:30:07Z bmol $
 * Document tool - slideshow renderer
 * @package application.weblcms.tool
 * @subpackage document
 */
class DocumentPublicationSlideshowRenderer extends ListLearningObjectPublicationListRenderer
{
	function as_html()
	{
		$publications = $this->get_publications();
		if(count($publications) == 0)
		{
			$html[] = Display::display_normal_message(get_lang('NoPublicationsAvailable'),true);
		}
		if(!isset($_GET['slideshow_index']))
		{
			$slideshow_index = 0;
		}
		else
		{
			$slideshow_index = $_GET['slideshow_index'];
		}
		$first = ($slideshow_index == 0);
		$last = ($slideshow_index == count($publications) - 1);
		$navigation[] = '<div style="text-align: center;">';
		$navigation[] = ($slideshow_index+1).' / '.count($publications);
		$navigation[] = '<div style="width=30%;text-align:left;float:left;">';
		if(!$first)
		{
			$navigation[] = '<a href="'.$this->get_url(array('slideshow_index'=>0)).'"><img src="'.api_get_path(WEB_CODE_PATH).'img/first.png" alt="'.get_lang('First').'"/></a>';
			$navigation[] = '<a href="'.$this->get_url(array('slideshow_index'=>$slideshow_index-1)).'"><img src="'.api_get_path(WEB_CODE_PATH).'img/prev.png" alt="'.get_lang('Previous').'"/></a>';
		}
		else
		{
			$navigation[] = '<img src="'.api_get_path(WEB_CODE_PATH).'img/first_na.png" alt="'.get_lang('First').'"/>';
			$navigation[] = '<img src="'.api_get_path(WEB_CODE_PATH).'img/prev_na.png" alt="'.get_lang('Previous').'"/>';
		}
		$navigation[] = '</div>';
		$navigation[] = '<div style="width=30%;text-align:right;float:right;">';
		if(!$last)
		{
			$navigation[] = '<a href="'.$this->get_url(array('slideshow_index'=>$slideshow_index+1)).'"><img src="'.api_get_path(WEB_CODE_PATH).'img/next.png" alt="'.get_lang('Next').'"/></a>';
			$navigation[] = '<a href="'.$this->get_url(array('slideshow_index'=>count($publications)-1)).'"><img src="'.api_get_path(WEB_CODE_PATH).'img/last.png" alt="'.get_lang('Last').'"/></a>';
		}
		else
		{
			$navigation[] = '<img src="'.api_get_path(WEB_CODE_PATH).'img/next_na.png" alt="'.get_lang('Next').'"/>';
			$navigation[] = '<img src="'.api_get_path(WEB_CODE_PATH).'img/last_na.png" alt="'.get_lang('Last').'"/>';

		}
		$navigation[] = '</div>';
		$navigation[] = '<div style="clear:both;"></div>';
		$navigation[] = '</div>';
		$html[] = implode("\n",$navigation);
		$html[] = $this->render_publication($publications[$slideshow_index]);
		$html[] = implode("\n",$navigation);
		return implode("\n", $html);
	}
	function render_publication($publication, $first = false, $last = false)
	{
		$document = $publication->get_learning_object();
		$url = $document->get_url();
		$html[] = '<div class="learning_object">';
		$html[] = '<div class="title'. ($publication->is_visible_for_target_users() ? '' : ' invisible').'">';
		$html[] = $this->render_title($publication);
		$html[] = '</div>';
		$html[] = '<div style="text-align: center;">';
		$html[] = '<img src="'.$url.'" alt="" style="border:1px solid black;padding:5px;"/>';
		$html[] = '<div class="description'. ($publication->is_visible_for_target_users() ? '' : ' invisible').'">';
		$html[] = $this->render_description($publication);
		$html[] = $this->render_attachments($publication);
		$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '</div>';
		return implode("\n",$html);
	}
}
?>