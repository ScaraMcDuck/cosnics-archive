<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/coursebrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../course/course_table/defaultcoursetablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../course/course.class.php';
require_once dirname(__FILE__).'/../../weblcms.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class CourseBrowserTableCellRenderer extends DefaultCourseTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function CourseBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $course)
	{
		if ($column === CourseBrowserTableColumnModel :: get_modification_column())
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
		
		if ($url = $this->browser->get_course_subscription_url($course))
		{	
			$toolbar_data[] = array(
				'href' => 'add course to users course list ....',
				'label' => get_lang('Update'),
				'confirm' => true,
				'img' => $this->browser->get_web_code_path().'img/enroll.gif'
			);
			
			return RepositoryUtilities :: build_toolbar($toolbar_data);
		}
		elseif ($this->browser->is_subscribed($course))
		{
			return get_lang('AlreadySubscribed');
		}
		else
		{
			return get_lang('SubscriptionNotAllowed');
		}
		
	}
}
?>