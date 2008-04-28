<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/unsubscribebrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../course/course_table/defaultcoursetablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../course/course.class.php';
require_once dirname(__FILE__).'/../../weblcms.class.php';
/**
 * Cell rendere for the unsubscribe browser table
 */
class UnsubscribeBrowserTableCellRenderer extends DefaultCourseTableCellRenderer
{
	/**
	 * The weblcms browser component
	 */
	private $browser;
	/**
	 * Constructor
	 * @param WeblcmsBrowserComponent $browser
	 */
	function UnsubscribeBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $course)
	{
		if ($column === UnsubscribeBrowserTableColumnModel :: get_modification_column())
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
	 * @param Course $course The course for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($course)
	{
		$toolbar_data = array();
		
		$course_unsubscription_url = $this->browser->get_course_unsubscription_url($course);
		
		if ($course_unsubscription_url)
		{	
			$toolbar_data[] = array(
				'href' => $course_unsubscription_url,
				'label' => Translation :: get('Unsubscribe'),
				'confirm' => true,
				'img' => Theme :: get_common_img_path().'action_delete.png'
			);
			
			return RepositoryUtilities :: build_toolbar($toolbar_data);
		}
		else
		{
			if ($course->is_course_admin($this->browser->get_user_id()))
			{
				return '<span class="info_message">'.Translation :: get('UnsubscriptionAdmin').'</span>';
			}
			else
			{
				return Translation :: get('UnsubscriptionNotAllowed');
			}
		}
		
	}
}
?>