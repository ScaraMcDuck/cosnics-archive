<?php
/**
 * $Id$
 * Dropbox tool - browser
 * @package application.weblcms.tool
 * @subpackage dropbox
 */
require_once dirname(__FILE__).'/../../weblcms_data_manager.class.php';
require_once dirname(__FILE__).'/../../learning_object_publication_browser.class.php';
require_once dirname(__FILE__).'/dropbox_publication_list_renderer.class.php';
require_once dirname(__FILE__).'/../../browser/list_renderer/learning_object_publication_details_renderer.class.php';

class DropboxBrowser extends LearningObjectPublicationBrowser
{
	function DropboxBrowser($parent, $types)
	{
		parent :: __construct($parent, 'dropbox');
		if(isset($_GET['pid']))
		{
			$this->set_publication_id($_GET['pid']);
			$renderer = new LearningObjectPublicationDetailsRenderer($this);
		}
		else
		{
			$renderer = new DropboxPublicationListRenderer($this);
		}
		$this->set_publication_list_renderer($renderer);
	}

	function get_publications($from, $count, $column, $direction)
	{
		$datamanager = WeblcmsDataManager :: get_instance();
		$tool_condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'dropbox');
		$condition = $tool_condition;
		$user_id = $this->get_user_id();
		$course_groups = $this->get_course_groups();
		$publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $user_id, $course_groups, $condition, false, array (LearningObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX), array (SORT_DESC));
		$visible_publications = array ();
		while ($publication = $publications->next_result())
		{
			// If the publication is hidden and the user is not allowed to DELETE or EDIT, don't show this publication
			if (!$publication->is_visible_for_target_users() && !($this->is_allowed(DELETE_RIGHT) || $this->is_allowed(EDIT_RIGHT)))
			{
				continue;
			}
			$visible_publications[] = $publication;
		}
		return $visible_publications;
	}

	function get_publication_count()
	{
		return count($this->get_publications());
	}
}
?>