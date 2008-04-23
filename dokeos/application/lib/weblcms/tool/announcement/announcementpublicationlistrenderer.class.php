<?php
/**
 * $Id$
 * Announcement tool - list renderer
 * @package application.weblcms.tool
 * @subpackage announcement
 */
require_once dirname(__FILE__).'/../../browser/list_renderer/listlearningobjectpublicationlistrenderer.class.php';
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
			$up_img = 'up.png';
			$up_url = $this->get_url(array (RepositoryTool :: PARAM_ACTION => RepositoryTool :: ACTION_MOVE_DOWN, RepositoryTool :: PARAM_PUBLICATION_ID => $publication->get_id()), true);
			$up_link = '<a href="'.$up_url.'"><img src="'.Theme :: get_common_img_path().$up_img.'" alt=""/></a>';
		}
		else
		{
			$up_link = '<img src="'.Theme :: get_common_img_path().'up_na.png"  alt=""/>';
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
			$down_img = 'down.png';
			$down_url = $this->get_url(array (RepositoryTool :: PARAM_ACTION => RepositoryTool :: ACTION_MOVE_UP, RepositoryTool :: PARAM_PUBLICATION_ID => $publication->get_id()), true);
			$down_link = '<a href="'.$down_url.'"><img src="'.Theme :: get_common_img_path().$down_img.'"  alt=""/></a>';
		}
		else
		{
			$down_link = '<img src="'.Theme :: get_common_img_path().'down_na.png"  alt=""/>';
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
}
?>