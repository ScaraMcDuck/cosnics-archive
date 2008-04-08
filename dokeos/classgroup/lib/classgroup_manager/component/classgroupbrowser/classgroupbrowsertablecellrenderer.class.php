<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/classgroupbrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../classgroup_table/defaultclassgrouptablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../classgroup.class.php';
require_once dirname(__FILE__).'/../../classgroupmanager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class ClassGroupBrowserTableCellRenderer extends DefaultClassGroupTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function ClassGroupBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $classgroup)
	{
		if ($column === ClassGroupBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($classgroup);
		}
		
		// Add special features here
		switch ($column->get_classgroup_property())
		{
			// Exceptions that need post-processing go here ...
			case ClassGroup :: PROPERTY_NAME :
				$title = parent :: render_cell($column, $classgroup);
				$title_short = $title;
				if(strlen($title_short) > 53)
				{
					$title_short = mb_substr($title_short,0,50).'&hellip;';
				}
				return '<a href="'.htmlentities($this->browser->get_classgroup_viewing_url($classgroup)).'" title="'.$title.'">'.$title_short.'</a>';
			case ClassGroup :: PROPERTY_DESCRIPTION :
				$description = strip_tags(parent :: render_cell($column, $classgroup));
				if(strlen($description) > 175)
				{
					$description = mb_substr($description,0,170).'&hellip;';
				}
				return $description;
		}
		return parent :: render_cell($column, $classgroup);
	}
	/**
	 * Gets the action links to display
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($classgroup)
	{
		$toolbar_data = array();
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_classgroup_editing_url($classgroup),
			'label' => Translation :: get('Edit'),
			'img' => $this->browser->get_web_code_path().'edit.gif'
		);
		
		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
}
?>