<?php
/**
 * Announcement tool - list renderer
 * @package application.weblcms.tool
 * @subpackage announcement
 */
require_once dirname(__FILE__).'/../learningobjectpublicationlistrenderer.class.php';
/**
 * A renderer to display a list view of announcements
 */
class AnnouncementListRenderer extends LearningObjectPublicationListRenderer
{
	/**
	 * Renders the up-action.
	 *
	 * By default, the most recent published announcement, is displayed as first
	 * item in the list. So the actual display order is the reverse of the
	 * display order in the database. The up action in the announcement-tool
	 * should result in the down-action in the database.
	 * @return string Empty string
	 */
	function render_up_action($publication,$first = false)
	{
		$up_link = parent::render_up_action($publication,$first);
		return str_replace('action=move_up','action=move_down',$up_link);
	}
	/**
	 * Renders the down-action.
	 *
	 * By default, the most recent published announcement, is displayed as first
	 * item in the list. So the actual display order is the reverse of the
	 * display order in the database. The down action in the announcement-tool
	 * should result in the up-action in the database.
	 * @return string Empty string
	 */
	function render_down_action($publication,$last = false)
	{
		$down_link = parent::render_down_action($publication,$last);
		return str_replace('action=move_down','action=move_up',$down_link);
	}
}
?>