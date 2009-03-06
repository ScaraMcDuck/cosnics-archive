<?php
/**
 * $Id: documentpublicationlistrenderer.class.php 12389 2007-05-14 11:30:07Z bmol $
 * Document tool - slideshow renderer
 * @package application.weblcms.tool
 * @subpackage document
 */
require_once(Path :: get_library_path().'image_manipulation/image_manipulation.class.php');
require_once dirname(__FILE__).'/../../../../browser/list_renderer/list_learning_object_publication_list_renderer.class.php';

class DocumentPublicationSlideshowRenderer extends ListLearningObjectPublicationListRenderer
{
	function as_html()
	{
		$publications = $this->get_publications();
		if (count($publications) == 0)
		{
			$html[] = Display :: normal_message(Translation :: get('NoPublicationsAvailable'), true);
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
			/*$toolbar_data[] = array(
				'img'=>Theme :: get_common_image_path().'action_slideshow.png',
				'label'=>Translation :: get('Slideshow'),
				'href' => $this->get_url(array('tool_action' => 'slideshow', 'thumbnails'=>null)),
				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
			$html[] = DokeosUtilities::build_toolbar($toolbar_data) . '<br /><br />';*/
			$html[] = $this->render_thumbnails($publications);
		}
		else
		{
			$first = ($slideshow_index == 0);
			$last = ($slideshow_index == count($publications) - 1);
			/*$toolbar_data[] = array(
				'img'=>Theme :: get_common_image_path().'action_slideshow_thumbnail.png',
				'label'=>Translation :: get('Thumbnails'),
				'href' => $this->get_url(array('tool_action' => 'slideshow','thumbnails'=>1)),
				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
			$html[] = DokeosUtilities::build_toolbar($toolbar_data) . '<br /><br />';*/
			
			$navigation[] = '<div style="text-align: center;">';
			$navigation[] = ($slideshow_index +1).' / '.count($publications);
			$navigation[] = '<div style="width=30%;text-align:left;float:left;">';
			if (!$first)
			{
				$navigation[] = '<a href="'.$this->get_url(array ('tool_action' => 'slideshow', 'slideshow_index' => 0)).'"><img src="'.Theme :: get_common_image_path().'action_first.png" alt="'.Translation :: get('First').'"/></a>';
				$navigation[] = '<a href="'.$this->get_url(array ('tool_action' => 'slideshow', 'slideshow_index' => $slideshow_index -1)).'"><img src="'.Theme :: get_common_image_path().'action_prev.png" alt="'.Translation :: get('Previous').'"/></a>';
			}
			else
			{
				$navigation[] = '<img src="'.Theme :: get_common_image_path().'action_first_na.png" alt="'.Translation :: get('First').'"/>';
				$navigation[] = '<img src="'.Theme :: get_common_image_path().'action_prev_na.png" alt="'.Translation :: get('Previous').'"/>';
			}
			$navigation[] = '</div>';
			$navigation[] = '<div style="width=30%;text-align:right;float:right;">';
			if (!$last)
			{
				$navigation[] = '<a href="'.$this->get_url(array ('tool_action' => 'slideshow','slideshow_index' => $slideshow_index +1)).'"><img src="'.Theme :: get_common_image_path().'action_next.png" alt="'.Translation :: get('Next').'"/></a>';
				$navigation[] = '<a href="'.$this->get_url(array ('tool_action' => 'slideshow','slideshow_index' => count($publications) - 1)).'"><img src="'.Theme :: get_common_image_path().'action_last.png" alt="'.Translation :: get('Last').'"/></a>';
			}
			else
			{
				$navigation[] = '<img src="'.Theme :: get_common_image_path().'action_next_na.png" alt="'.Translation :: get('Next').'"/>';
				$navigation[] = '<img src="'.Theme :: get_common_image_path().'action_last_na.png" alt="'.Translation :: get('Last').'"/>';

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
		$html[] = '<a href="' . $url . '" target="about:blank"><img src="'.$url.'" alt="" style="max-width: 800px; border:1px solid black;padding:5px;"/></a>';
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
			$html[] = '<a href="'.$this->get_url(array ('tool_action' => 'slideshow','slideshow_index' => $index)).'" style="border:1px solid #F0F0F0;margin: 2px;text-align: center;width:110px;height:110px;padding:5px;float:left;">';
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