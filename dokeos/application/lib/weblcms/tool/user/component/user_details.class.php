<?php

require_once dirname(__FILE__) . '/../user_tool.class.php';
require_once dirname(__FILE__) . '/../user_tool_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__) . '/../../../../../common/user_details.class.php';

class UserToolDetailsComponent extends UserToolComponent
{

	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		$trail = new BreadcrumbTrail();
		$trail->add_help('courses user');

        if(Request :: get('users') != null)
        {
            $user = DatabaseUserDataManager :: get_instance()->retrieve_user(Request :: get('users'));
            $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => 'user_details', 'users' => Request :: get('users'))), $user->get_firstname().' '.$user->get_lastname()));
        }
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => 'user_details', 'users' => Request :: get('users'))), Translation :: get('Details')));
		$this->display_header($trail, true);

		$udm = UserDataManager::get_instance();
		if(isset($_GET[WeblcmsManager :: PARAM_USERS]))
		{
			$user = $udm->retrieve_user($_GET[WeblcmsManager :: PARAM_USERS]);
			$details = new UserDetails($user);
			echo $details->toHtml();
		}
		if(isset($_POST['user_id']))
		{
			foreach($_POST['user_id'] as $index => $user_id)
			{
				$user = $udm->retrieve_user($user_id);
				$details = new UserDetails($user);
				echo $details->toHtml();
			}
		}

		$this->display_footer();
	}

}
?>