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
// TODO: User real right_users'n'rights here
if (!$user->is_platform_admin())
{
	echo 0;
}

$rights = $_POST['rights'];
$rights = explode('_', $rights);

$right = $rights['1'];
$right_user = $rights['2'];
$location = $rights['3'];

if (isset($right_user) && isset($right) && isset($location))
{
    $success = RightsUtilities :: invert_user_right_location($right, $right_user, $location);
	if (!$success)
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
