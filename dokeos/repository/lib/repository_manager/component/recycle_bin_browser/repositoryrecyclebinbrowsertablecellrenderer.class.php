<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/repositoryrecyclebinbrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../learning_object_table/defaultlearningobjecttablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../learningobject.class.php';
require_once dirname(__FILE__).'/../../../repositoryutilities.class.php';
/**
 * Cell renderer for the recycle bin browser table
 */
class RepositoryRecycleBinBrowserTableCellRenderer extends DefaultLearningObjectTableCellRenderer
{
	/**
	 * The recycle bin browser component in which the learning objects will be
	 * displayed.
	 */
	private $browser;
	/**
	 * Array acting as a cache for learning object titles
	 */
	private $parent_title_cache;
	/**
	 * Constructor
	 * @param RepositoryManagerRecycleBinBrowserComponent $browser
	 */
	function RepositoryRecycleBinBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
		$this->parent_title_cache = array();
	}
	// Inherited
	function render_cell($column, $learning_object)
	{
		if ($column === RepositoryRecycleBinBrowserTableColumnModel :: get_action_column())
		{
			return $this->get_action_links($learning_object);
		}
		switch ($column->get_learning_object_property())
		{
			case LearningObject :: PROPERTY_TITLE :
				$title = parent :: render_cell($column, $learning_object);
				$title_short = $title;
				if(strlen($title_short) > 53)
				{
					$title_short = substr($title_short,0,50).'&hellip;';
				}
				return '<a href="'.htmlentities($this->browser->get_learning_object_viewing_url($learning_object)).'" title="'.$title.'">'.$title_short.'</a>';
			case LearningObject :: PROPERTY_PARENT_ID :
				$pid = $learning_object->get_parent_id();
				if (!isset($this->parent_title_cache[$pid]))
				{
					$this->parent_title_cache[$pid] = '<a href="'.htmlentities($this->browser->get_learning_object_viewing_url($learning_object)).'" title="'.htmlentities(Translation :: get('BrowseThisCategory')).'">'.htmlentities($this->browser->retrieve_learning_object($pid)->get_title()).'</a>';
				}
				return $this->parent_title_cache[$pid];
		}
		return parent :: render_cell($column, $learning_object);
	}
	/**
	 * Gets the action links to display
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_action_links($learning_object)
	{
		$toolbar_data = array();
		$toolbar_data[] = array(
			'href' => $this->browser->get_learning_object_restoring_url($learning_object),
			'img' => Theme :: get_common_img_path().'action-restore.png',
			'label' => Translation :: get('Restore')
		);
		$toolbar_data[] = array(
			'href' => $this->browser->get_learning_object_deletion_url($learning_object),
			'img' => Theme :: get_common_img_path().'action-delete.png',
			'label' => Translation :: get('Delete'),
			'confirm' => true
		);
		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
}
?>