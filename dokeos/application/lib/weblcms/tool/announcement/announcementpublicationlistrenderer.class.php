<?php
/**
 * Announcement tool - list renderer
 * @package application.weblcms.tool
 * @subpackage announcement
 */
require_once dirname(__FILE__).'/../../browser/list_renderer/listlearningobjectpublicationlistrenderer.class.php';

class AnnouncementPublicationListRenderer extends ListLearningObjectPublicationListRenderer
{
	function render_up_action($publication, $first = false)
	{
		/*
		 * By default, the most recently published announcement, is displayed as first
		 * item in the list. So the actual display order is the reverse of the
		 * display order in the database. The up action in the announcement-tool
		 * should result in the down-action in the database.
		 */
		// TODO: Fix this. It's horribly broken.
		//$up_link = parent :: render_up_action($publication, $first);
		//return str_replace(RepositoryTool :: PARAM_ACTION.'='.RepositoryTool :: ACTION_MOVE_UP, RepositoryTool :: PARAM_ACTION.'='.RepositoryTool :: ACTION_MOVE_DOWN, $up_link);
		return parent :: render_down_action($publication, $first);
	}
	function render_down_action($publication, $last = false)
	{
		/*
		 * By default, the most recent published announcement, is displayed as first
		 * item in the list. So the actual display order is the reverse of the
		 * display order in the database. The down action in the announcement-tool
		 * should result in the up-action in the database.
		 */
		// TODO: Fix this. It's horribly broken.
		//$down_link = parent :: render_down_action($publication, $last);
		//return str_replace(RepositoryTool :: PARAM_ACTION.'='.RepositoryTool :: ACTION_MOVE_DOWN, RepositoryTool :: PARAM_ACTION.'='.RepositoryTool :: ACTION_MOVE_UP, $down_link);
		return parent :: render_up_action($publication, $last);
	}
}
?>