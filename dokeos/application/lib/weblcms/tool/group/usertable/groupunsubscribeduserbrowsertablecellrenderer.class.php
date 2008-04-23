<?php
/**
 * $Id$
 * Group tool
 * @package application.weblcms.tool
 * @subpackage group
 */
class GroupUnsubscribedUserBrowserTableCellRenderer extends DefaultUserTableCellRenderer
{
		private $browser;
    function GroupUnsubscribedUserBrowserTableCellRenderer($browser) {
    	parent :: __construct();
		$this->browser = $browser;
    }
	// Inherited
	function render_cell($column, $user)
	{
		if ($column === GroupUnsubscribedUserBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($user);
		}

		// Add special features here
		switch ($column->get_user_property())
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
				$parameters[Weblcms :: PARAM_USERS] = $user->get_user_id();
				$parameters[GroupTool :: PARAM_GROUP_ACTION] = GroupTool::ACTION_SUBSCRIBE;
				$unsubscribe_url = $this->browser->get_url($parameters);
				$toolbar_data[] = array(
					'href' => $unsubscribe_url,
					'label' => Translation :: get('Subscribe'),
					'img' => Theme :: get_common_img_path().'subscribe.png'
				);
			$parameters = array();
			$parameters[Weblcms::PARAM_USER_ACTION] = UserTool::USER_DETAILS;
			$parameters[Weblcms :: PARAM_USERS] = $user->get_user_id();
			$unsubscribe_url = $this->browser->get_url($parameters);
			$toolbar_data[] = array(
				'href' => $unsubscribe_url,
				'label' => Translation :: get('Details'),
				'img' => Theme :: get_common_img_path().'profile.gif'
			);
		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
}
?>