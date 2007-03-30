<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/unsubscribebrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../course/course_table/defaultcoursetablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../course/course.class.php';
require_once dirname(__FILE__).'/../../weblcms.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class UnsubscribeBrowserTableCellRenderer extends DefaultCourseTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
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
	 * @param LearningObject $learning_object The learning object for which the
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
				'label' => get_lang('Delete'),
				'confirm' => true,
				'img' => $this->browser->get_web_code_path().'img/delete.gif'
			);
			
			return RepositoryUtilities :: build_toolbar($toolbar_data);
		}
		else
		{
			$location_id = RolesRights::get_course_location_id($course->get_id());
			$role_id = RolesRights:: get_local_user_role_id_from_location_id(api_get_user_id(), $location_id);
			if ($role_id == COURSE_ADMIN)
			{
				return '<span class="info_message">'.get_lang('UnsubscriptionAdmin').'</span>';
			}
			else
			{
				return get_lang('UnsubscriptionNotAllowed');
			}
		}
		
	}
}
?>