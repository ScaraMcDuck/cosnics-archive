<?php
/**
 * $Id$
 * Announcement tool - list renderer
 * @package application.weblcms.tool
 * @subpackage announcement
 */
require_once dirname(__FILE__).'/../../../../browser/list_renderer/list_learning_object_publication_list_renderer.class.php';
/**
 * Renderer to display a list of announcement publications.
 */
class AnnouncementPublicationListRenderer extends ListLearningObjectPublicationListRenderer
{
	/*
	 * Overriding default function
	 */
	function render_up_action($publication, $first = false)
	{
		/*
		 * By default, the most recently published announcement, is displayed as first
		 * item in the list. So the actual display order is the reverse of the
		 * display order in the database. The up action in the announcement-tool
		 * should result in the down-action in the database.
		 */
		if (!$first)
		{
			$up_img = 'action_up.png';
			$up_url = $this->get_url(array (RepositoryTool :: PARAM_ACTION => RepositoryTool :: ACTION_MOVE_DOWN, RepositoryTool :: PARAM_PUBLICATION_ID => $publication->get_id()), true);
			$up_link = '<a href="'.$up_url.'"><img src="'.Theme :: get_common_img_path().$up_img.'" alt=""/></a>';
		}
		else
		{
			$up_link = '<img src="'.Theme :: get_common_img_path().'action_up_na.png"  alt=""/>';
		}
		return $up_link;
	}
	/*
	 * Overriding default function
	 */
	function render_down_action($publication, $last = false)
	{
		/*
		 * By default, the most recent published announcement, is displayed as first
		 * item in the list. So the actual display order is the reverse of the
		 * display order in the database. The down action in the announcement-tool
		 * should result in the up-action in the database.
		 */
		if (!$last)
		{
			$down_img = 'action_down.png';
			$down_url = $this->get_url(array (RepositoryTool :: PARAM_ACTION => RepositoryTool :: ACTION_MOVE_UP, RepositoryTool :: PARAM_PUBLICATION_ID => $publication->get_id()), true);
			$down_link = '<a href="'.$down_url.'"><img src="'.Theme :: get_common_img_path().$down_img.'"  alt=""/></a>';
		}
		else
		{
			$down_link = '<img src="'.Theme :: get_common_img_path().'action_down_na.png"  alt=""/>';
		}
		return $down_link;
	}
	/*
	 * Overriding default function
	 */
	function render_move_to_category_action($publication)
	{
		return '';
	}
	
	/**
	 * Overriding default function
	 */
	function as_html()
	{
		$publications = $this->get_publications();
		if(count($publications) == 0)
		{
			$html_bottom[] = Display::display_normal_message(Translation :: get('NoPublicationsAvailable'),true);
		}
		$html_top[] = '<div style="width:19%; float: left;">';
		if(count($publications) >= 1)
		{
			$html_top[] = '<div style="border-bottom: 1px solid grey; padding: 5px;">';
			//$delete_url = $this->get_url(array (RepositoryTool :: PARAM_ACTION => RepositoryTool :: ACTION_DELETE, RepositoryTool :: PARAM_PUBLICATION_ID => $publication->get_id()), true);
			$html_top[] = '<a href="'.$delete_url.'" onclick="return confirm(\''.addslashes(htmlentities(Translation :: get('ConfirmYourChoice'))).'\');"><img src="'.Theme :: get_common_img_path().'action_delete.png"  alt="' . Translation :: get('Clear_list_of_announcements') . '"/> ' . Translation :: get('Clear_list_of_announcements') . '</a>';
			$html_top[] = '</div>';
		}
		
		$html_bottom[] = '<div style="width:79%; padding-left: 1%; float:right; border-left: 1px solid grey;">';
		$html_top[] = '<div style="padding: 5px;">';
		
		foreach ($publications as $index => $publication)
		{
			$first = ($index == 0);
			$last = ($index == count($publications) - 1);
			$html_bottom[] = '<a name="' . $index . '"></a>';
			$html_bottom[] = $this->render_publication($publication, $first, $last);
			$html_bottom[] = '<a href="#top">' . Translation :: get('Go_To_Top') . '</a><br /><br />';
			$html_top[] = '<a href="#' . $index . '">' . $this->render_title($publication) . '</a><br />';
		}
		
		$html_bottom[] = '</div>';
		$html_top[] = '</div></div>';
		
		$str .= implode("\n", $html_top);
		$str .= implode("\n", $html_bottom);
		return $str;
	}
}
?>