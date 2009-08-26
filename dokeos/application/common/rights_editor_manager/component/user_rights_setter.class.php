<?php
require_once Path :: get_rights_path() . 'lib/rights_utilities.class.php';

class RightsEditorManagerUserRightsSetterComponent extends RightsEditorManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$user = Request :: get('user_id');
		$right = Request :: get('right_id');
		$location = $this->get_location();

		if (isset($user) && isset($right) && isset($location))
		{
		    $success = RightsUtilities :: invert_user_right_location($right, $user, $location->get_id());

			$this->redirect(Translation :: get($success == true ? 'RightUpdated' : 'RightUpdateFailed'), !$success, array_merge($this->get_parameters(), array(RightsEditorManager :: PARAM_RIGHTS_EDITOR_ACTION => RightsEditorManager :: ACTION_BROWSE_RIGHTS)));
		}
		else
		{
		    $this->display_error_page(htmlentities(Translation :: get('NoLocationSelected')));
		}
	}
}
?>