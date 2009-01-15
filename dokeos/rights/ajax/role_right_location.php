<?php
$this_section = 'rights';
 
require_once dirname(__FILE__).'/../../common/global.inc.php';
require_once Path :: get_rights_path() . 'lib/rights_data_manager.class.php';
require_once Path :: get_rights_path() . 'lib/rights_utilities.class.php';
require_once Path :: get_user_path() . 'lib/user_data_manager.class.php';

Translation :: set_application($this_section);
Theme :: set_application($this_section);

if (!Authentication :: is_valid())
{
	return 0;
}

$user = UserDataManager :: get_instance()->retrieve_user(Session :: get_user_id());
// TODO: User real roles'n'rights here
if (!$user->is_platform_admin())
{
	echo 0;
}

$rights = $_POST['rights'];
$rights = explode('_', $rights);

$right = $rights['1'];
$role = $rights['2'];
$location = $rights['3'];

if (isset($role) && isset($right) && isset($location))
{
	$rdm = RightsDataManager :: get_instance();
	
	$result = $rdm->retrieve_role_right_location($right, $role, $location);
	$result->invert();
	
	if (!$result->update())
	{
		echo 0;
	}
	else
	{
		echo 1;
	}
}
else
{
	echo 0;
}
?>
