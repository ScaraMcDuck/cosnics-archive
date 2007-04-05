<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/admincoursebrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../course/course_table/defaultcoursetablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../course/course.class.php';
require_once dirname(__FILE__).'/../../weblcms.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class AdminCourseBrowserTableCellRenderer extends DefaultCourseTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function AdminCourseBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $course)
	{
		if ($column === AdminCourseBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($course);
		}
		
		// Add special features here
		switch ($column->get_course_property())
		{
			// Exceptions that need post-processing go here ...
		}
		return parent :: render_cell($column, $course);
	}
	/**
	 * Gets the action links to display
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($course)
	{
		$toolbar_data = array();
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_course_viewing_url($course),
			'label' => get_lang('View'),
			'img' => $this->browser->get_web_code_path().'img/home_small.gif'
		);
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_course_editing_url($course),
			'label' => get_lang('Edit'),
			'img' => $this->browser->get_web_code_path().'img/edit.gif'
		);
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_course_maintenance_url($course),
			'label' => get_lang('Maintenance'),
			'img' => $this->browser->get_web_code_path().'img/maintenance_tool_small.gif'
		);
		
		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
}
?>