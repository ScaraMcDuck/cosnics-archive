<?php
/**
 * Learning Path tool - list renderer
 * @package application.weblcms.tool
 * @subpackage learning_path
 */
require_once dirname(__FILE__).'/../../weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/../../learningobjectpublicationbrowser.class.php';
require_once dirname(__FILE__).'/learning_pathpublicationlistrenderer.class.php';
/**
 * This class allows the end user to browse through published learning paths.
 */
class LearningPathBrowser extends LearningObjectPublicationBrowser
{
	/**
	 * Constructor
	 */
	function LearningPathBrowser($parent, $types)
	{
		parent :: __construct($parent, 'wiki');
		$renderer = new LearningPathPublicationListRenderer($this);
		$this->set_publication_list_renderer($renderer);
	}
	/*
	 * Inherited.
	 */
	function get_publications($from, $count, $column, $direction)
	{
		$datamanager = WeblcmsDataManager :: get_instance();
		$tool_condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'learning_path');
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
		$publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $user_id, $groups, $condition, false, array (Description :: PROPERTY_DISPLAY_ORDER_INDEX), array (SORT_DESC));
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
	/*
	 * Inherited.
	 */
	function get_publication_count()
	{
		return count($this->get_publications());
	}
}
?>