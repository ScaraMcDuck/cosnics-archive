<?php
/**
 * $Id: documentpublicationlistrenderer.class.php 12389 2007-05-14 11:30:07Z bmol $
 * Document tool - slideshow renderer
 * @package application.weblcms.tool
 * @subpackage document
 */
require_once(dirname(__FILE__).'/../../../../../common/imagemanipulation/imagemanipulation.class.php');
class DocumentPublicationSlideshowRenderer extends ListLearningObjectPublicationListRenderer
{
	function as_html()
	{
		$publications = $this->get_publications();
		if (count($publications) == 0)
		{
			$html[] = Display :: display_normal_message(Translation :: get_lang('NoPublicationsAvailable'), true);
			return implode("\n", $html);
		}
		if (!isset ($_GET['slideshow_index']))
		{
			$slideshow_index = 0;
		}
		else
		{
			$slideshow_index = $_GET['slideshow_index'];
		}
		if (isset ($_GET['thumbnails']))
		{
			$toolbar_data[] = array(
				'img'=>$this->browser->get_path(WEB_IMG_PATH).'slideshow.gif',
				'label'=>Translation :: get_lang('Slideshow'),
				'href' => $this->get_url(array('thumbnails'=>null)),
				'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
			$html[] = RepositoryUtilities::build_toolbar($toolbar_data);
			$html[] = $this->render_thumbnails($publications);
		}
		else
		{
			$first = ($slideshow_index == 0);
			$last = ($slideshow_index == count($publications) - 1);
			$toolbar_data[] = array(
				'img'=>$this->browser->get_path(WEB_IMG_PATH).'slideshow_thumbnails.gif',
				'label'=>Translation :: get_lang('Thumbnails'),
				'href' => $this->get_url(array('thumbnails'=>1)),
				'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
			$html[] = RepositoryUtilities::build_toolbar($toolbar_data);
			$navigation[] = '<div style="text-align: center;">';
			$navigation[] = ($slideshow_index +1).' / '.count($publications);
			$navigation[] = '<div style="width=30%;text-align:left;float:left;">';
			if (!$first)
			{
				$navigation[] = '<a href="'.$this->get_url(array ('slideshow_index' => 0)).'"><img src="'.$this->browser->get_path(WEB_IMG_PATH).'first.png" alt="'.Translation :: get_lang('First').'"/></a>';
				$navigation[] = '<a href="'.$this->get_url(array ('slideshow_index' => $slideshow_index -1)).'"><img src="'.$this->browser->get_path(WEB_IMG_PATH).'prev.png" alt="'.Translation :: get_lang('Previous').'"/></a>';
			}
			else
			{
				$navigation[] = '<img src="'.$this->browser->get_path(WEB_IMG_PATH).'first_na.png" alt="'.Translation :: get_lang('First').'"/>';
				$navigation[] = '<img src="'.$this->browser->get_path(WEB_IMG_PATH).'prev_na.png" alt="'.Translation :: get_lang('Previous').'"/>';
			}
			$navigation[] = '</div>';
			$navigation[] = '<div style="width=30%;text-align:right;float:right;">';
			if (!$last)
			{
				$navigation[] = '<a href="'.$this->get_url(array ('slideshow_index' => $slideshow_index +1)).'"><img src="'.$this->browser->get_path(WEB_IMG_PATH).'next.png" alt="'.Translation :: get_lang('Next').'"/></a>';
				$navigation[] = '<a href="'.$this->get_url(array ('slideshow_index' => count($publications) - 1)).'"><img src="'.$this->browser->get_path(WEB_IMG_PATH).'last.png" alt="'.Translation :: get_lang('Last').'"/></a>';
			}
			else
			{
				$navigation[] = '<img src="'.$this->browser->get_path(WEB_IMG_PATH).'next_na.png" alt="'.Translation :: get_lang('Next').'"/>';
				$navigation[] = '<img src="'.$this->browser->get_path(WEB_IMG_PATH).'last_na.png" alt="'.Translation :: get_lang('Last').'"/>';

			}
			$navigation[] = '</div>';
			$navigation[] = '<div style="clear:both;"></div>';
			$navigation[] = '</div>';
			$html[] = implode("\n", $navigation);
			$html[] = $this->render_publication($publications[$slideshow_index]);
			$html[] = implode("\n", $navigation);
		}
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
		return implode("\n", $html);
	}
	function render_thumbnails($publications)
	{
		foreach ($publications as $index => $publication)
		{
			$document = $publication->get_learning_object();
			$path = $document->get_full_path();
			$thumbnail_path = $this->get_thumbnail_path($path);
			$thumbnail_url = $this->browser->get_path(WEB_TEMP_PATH).basename($thumbnail_path);
			$html[] = '<a href="'.$this->get_url(array ('slideshow_index' => $index)).'" style="border:1px solid #F0F0F0;margin: 2px;text-align: center;width:110px;height:110px;padding:5px;float:left;">';
			$html[] = '<img src="'.$thumbnail_url.'" style="margin: 5px;"/>';
			$html[] = '</a>';
		}
		return implode("\n", $html);
	}
	private function get_thumbnail_path($image_path)
	{
		$thumbnail_path = $this->browser->get_path(SYS_TEMP_PATH).md5($image_path).basename($image_path);
		if(!is_file($thumbnail_path))
		{
			$thumbnail_creator = ImageManipulation::factory($image_path);
			$thumbnail_creator->create_thumbnail(100);
			$thumbnail_creator->write_to_file($thumbnail_path);
		}
		return $thumbnail_path;
	}
}
?>