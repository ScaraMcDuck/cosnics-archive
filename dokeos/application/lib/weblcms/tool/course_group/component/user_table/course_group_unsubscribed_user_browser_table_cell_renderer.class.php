<?php
/**
 * $Id$
 * CourseGroup tool
 * @package application.weblcms.tool
 * @subpackage course_group
 */
require_once Path :: get_user_path(). 'lib/user_table/default_user_table_cell_renderer.class.php';
 
class CourseGroupUnsubscribedUserBrowserTableCellRenderer extends DefaultUserTableCellRenderer
{
		private $browser;
    function CourseGroupUnsubscribedUserBrowserTableCellRenderer($browser) {
    	parent :: __construct();
		$this->browser = $browser;
    }
	// Inherited
	function render_cell($column, $user)
	{
		if ($column === CourseGroupUnsubscribedUserBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($user);
		}

		// Add special features here
		switch ($column->get_object_property())
		{
			// Exceptions that need post-processing go here ...
			case User :: PROPERTY_EMAIL:
				return '<a href="mailto:'.$user->get_email().'">'.$user->get_email().'</a>';
		}
		return parent :: render_cell($column, $user);
	}
	/**
	 * Gets the action links to display
	 * @param User $user The user for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($user)
	{
		$toolbar_data = array();
		$parameters = array();
				$parameters[Weblcms :: PARAM_USERS] = $user->get_id();
				$parameters[CourseGroupTool :: PARAM_COURSE_GROUP_ACTION] = CourseGroupTool::ACTION_SUBSCRIBE;
				$unsubscribe_url = $this->browser->get_url($parameters);
				$toolbar_data[] = array(
					'href' => $unsubscribe_url,
					'label' => Translation :: get('Subscribe'),
					'img' => Theme :: get_common_img_path().'action_subscribe.png'
				);
			$parameters = array();
			$parameters[Weblcms::PARAM_TOOL_ACTION] = UserTool::USER_DETAILS;
			$parameters[Weblcms :: PARAM_USERS] = $user->get_id();
			$unsubscribe_url = $this->browser->get_url($parameters);
			$toolbar_data[] = array(
				'href' => $unsubscribe_url,
				'label' => Translation :: get('Details'),
				'img' => Theme :: get_common_img_path().'action_details.png'
			);
		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>