<?php
/**
 * $Id$
 * Announcement tool - browser
 * @package application.weblcms.tool
 * @subpackage announcement
 */
require_once dirname(__FILE__).'/../../weblcms_data_manager.class.php';
require_once dirname(__FILE__).'/../../learning_object_publication_browser.class.php';
require_once dirname(__FILE__).'/announcementpublicationlistrenderer.class.php';
require_once dirname(__FILE__).'/../../browser/list_renderer/learningobjectpublicationdetailsrenderer.class.php';
/**
 * Browser to allow the user to view the published announcements
 */
class AnnouncementBrowser extends LearningObjectPublicationBrowser
{
	/**
	 * @see LearningObjectPublicationBrowser::LearningObjectPublicationBrowser()
	 */
	function AnnouncementBrowser($parent)
	{
		parent :: __construct($parent, 'announcement');
		if(isset($_GET['pid']))
		{
			$this->set_publication_id($_GET['pid']);
			$renderer = new LearningObjectPublicationDetailsRenderer($this);
		}
		else
		{
			$renderer = new AnnouncementPublicationListRenderer($this);
		}
		$this->set_publication_list_renderer($renderer);
	}
	/**
	 * Retrieves the publications
	 * @return array An array of LearningObjectPublication objects
	 */
	function get_publications($from, $count, $column, $direction)
	{
		$datamanager = WeblcmsDataManager :: get_instance();
		$tool_condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'announcement');
		$condition = $tool_condition;
		if($this->is_allowed(EDIT_RIGHT))
		{
			$user_id = null;
			$groups = null;
		}
		else
		{
			$user_id = $this->get_user_id();
			$groups = $this->get_groups();
		}
		$publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $user_id, $groups, $condition, false, array (Announcement :: PROPERTY_DISPLAY_ORDER_INDEX), array (SORT_DESC));
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
	/**
	 * Retrieves the number of published annoucements
	 * @return int
	 */
	function get_publication_count()
	{
		return count($this->get_publications());
	}
}
?>