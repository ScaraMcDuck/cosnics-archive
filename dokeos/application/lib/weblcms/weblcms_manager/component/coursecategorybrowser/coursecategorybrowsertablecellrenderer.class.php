<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/coursecategorybrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../course/coursecategory_table/defaultcoursecategorytablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../course/coursecategory.class.php';
require_once dirname(__FILE__).'/../../weblcms.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class CourseCategoryBrowserTableCellRenderer extends DefaultCourseCategoryTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function CourseCategoryBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $coursecategory)
	{
		if ($column === CourseCategoryBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($coursecategory);
		}
		
		// Add special features here
		switch ($column->get_course_category_property())
		{
			// Exceptions that need post-processing go here ...
		}
		return parent :: render_cell($column, $coursecategory);
	}
	/**
	 * Gets the action links to display
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($coursecategory)
	{
		$toolbar_data = array();
		
		$toolbar_data[] = array(
			'href' => 'echo',
			'label' => get_lang('Update'),
			'confirm' => true,
			'img' => $this->browser->get_web_code_path().'img/edit.gif'
		);
		
		$toolbar_data[] = array(
			'href' => 'echo',
			'label' => get_lang('Update'),
			'confirm' => true,
			'img' => $this->browser->get_web_code_path().'img/delete.gif'
		);

		return RepositoryUtilities :: build_toolbar($toolbar_data);		
	}
}
?>