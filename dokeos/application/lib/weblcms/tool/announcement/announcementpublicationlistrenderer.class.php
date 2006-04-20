<?php
/**
 * Announcement tool - list renderer
 * @package application.weblcms.tool
 * @subpackage announcement
 */
require_once dirname(__FILE__).'/../../browser/list_renderer/listlearningobjectpublicationlistrenderer.class.php';

class AnnouncementPublicationListRenderer extends ListLearningObjectPublicationListRenderer
{
	function render_publications($publications)
	{
		$all_publications = $this->get_parent()->get_publications();
		$visible_publications = array();
		foreach($all_publications as $index => $publication)
		{
			// If the publication is hidden and the user is not allowed to DELETE or EDIT, don't show this publication
			if(!$publication->is_visible_for_target_users() && !($this->is_allowed(DELETE_RIGHT) || $this->is_allowed(EDIT_RIGHT)))
			{
				continue;
			}
			$visible_publications[] = $publication;
		}
		return parent :: render_publications($visible_publications);
	}
	function render_up_action($publication,$first = false)
	{
		/*
		 * By default, the most recently published announcement, is displayed as first
		 * item in the list. So the actual display order is the reverse of the
		 * display order in the database. The up action in the announcement-tool
		 * should result in the down-action in the database.
		 */
		$up_link = parent::render_up_action($publication,$first);
		return str_replace('action=move_up','action=move_down',$up_link);
	}
	function render_down_action($publication,$last = false)
	{
		/*
		 * By default, the most recent published announcement, is displayed as first
		 * item in the list. So the actual display order is the reverse of the
		 * display order in the database. The down action in the announcement-tool
		 * should result in the up-action in the database.
		 */
		$down_link = parent::render_down_action($publication,$last);
		return str_replace('action=move_down','action=move_up',$down_link);
	}
}
?>