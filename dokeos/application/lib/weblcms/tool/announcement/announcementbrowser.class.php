<?php
/**
 * Announcement tool - browser
 * @package application.weblcms.tool
 * @subpackage announcement
 */
require_once dirname(__FILE__).'/../../weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/../../learningobjectpublicationbrowser.class.php';
require_once dirname(__FILE__).'/announcementpublicationlistrenderer.class.php';

class AnnouncementBrowser extends LearningObjectPublicationBrowser
{
	function AnnouncementBrowser($parent, $types)
	{
		parent :: __construct($parent, 'announcement');
		$renderer = new AnnouncementPublicationListRenderer($this);
		$this->set_publication_list_renderer($renderer);
	}

	function get_publications($from, $count, $column, $direction)
	{
		$datamanager = WeblcmsDataManager :: get_instance();
		$tool_condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'announcement');
		$condition = $tool_condition;
		$publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $this->get_user_id(), $this->get_groups(), $condition, false, array (Announcement :: PROPERTY_DISPLAY_ORDER_INDEX), array (SORT_DESC));
		$visible_publications = array ();
		foreach ($publications as $index => $publication)
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