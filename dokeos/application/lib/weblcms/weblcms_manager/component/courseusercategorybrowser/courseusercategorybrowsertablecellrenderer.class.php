<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/courseusercategorybrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../course/courseusercategory_table/defaultcourseusercategorytablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../course/courseusercategory.class.php';
require_once dirname(__FILE__).'/../../weblcms.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class CourseUserCategoryBrowserTableCellRenderer extends DefaultCourseUserCategoryTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;
	/**
	 * Constructor
* @param WeblcmsBrowserComponent $browser
	 */
	function CourseUserCategoryBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $courseusercategory)
	{
		if ($column === CourseUserCategoryBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($courseusercategory);
		}
		
		// Add special features here
		switch ($column->get_course_user_category_property())
		{
			// Exceptions that need post-processing go here ...
		}
		return parent :: render_cell($column, $courseusercategory);
	}
	/**
	 * Gets the action links to display
	 * @param CourseUserCategory $courseusercategory The course user category for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($courseusercategory)
	{
		$toolbar_data = array();
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_course_user_category_edit_url($courseusercategory),
			'label' => Translation :: get('Edit'),
			'img' => Theme :: get_common_img_path().'edit.gif'
		);
			
		$toolbar_data[] = array(
			'href' => $this->browser->get_course_user_category_delete_url($courseusercategory),
			'label' => Translation :: get('Delete'),
			'confirm' => true,
			'img' => Theme :: get_common_img_path().'delete.gif'
		);
			
		return RepositoryUtilities :: build_toolbar($toolbar_data);		
	}
}
?>