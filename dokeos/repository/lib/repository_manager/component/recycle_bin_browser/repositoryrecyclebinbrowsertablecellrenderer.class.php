<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/repositoryrecyclebinbrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../learning_object_table/defaultlearningobjecttablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../learningobject.class.php';

class RepositoryRecycleBinBrowserTableCellRenderer extends DefaultLearningObjectTableCellRenderer
{
	private $browser;
	
	private $parent_title_cache;

	function RepositoryRecycleBinBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
		$this->parent_title_cache = array();
	}

	function render_cell($column, $learning_object)
	{
		if ($column === RepositoryRecycleBinBrowserTableColumnModel :: get_restore_column())
		{
			return $this->get_restore_link($learning_object);
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
					$this->parent_title_cache[$pid] = htmlentities($this->browser->retrieve_learning_object($pid)->get_title());
				}
				return $this->parent_title_cache[$pid];
		}
		return parent :: render_cell($column, $learning_object);
	}

	private function get_restore_link($learning_object)
	{
		return '<a href="'.$this->browser->get_learning_object_restoration_url($learning_object).'" title="'.get_lang('Restore').'"><img src="'.$this->browser->get_web_code_path().'img/restore.gif" alt="'.get_lang('Restore').'"/></a>';
	}
}
?>