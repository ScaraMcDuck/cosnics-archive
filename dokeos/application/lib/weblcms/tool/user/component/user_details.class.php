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
			Display :: display_not_allowed();
			return;
		}
		
		$trail = new BreadcrumbTrail();
		$this->display_header($trail);
		
		$udm = UserDataManager::get_instance();
		if(isset($_GET[Weblcms::PARAM_USERS]))
		{
			$user = $udm->retrieve_user($_GET[Weblcms::PARAM_USERS]);
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