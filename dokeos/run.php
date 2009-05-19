<?php
/**
 * $Id: application.class.php 12019 2007-04-13 12:57:10Z Scara84 $
 * @package application
 *
 * This script will load the requested application and call its run() function.
 */
require_once dirname(__FILE__).'/common/global.inc.php';
require_once Path :: get_user_path(). 'lib/user_manager/user_manager.class.php';
require_once Path :: get_application_path() . 'lib/web_application.class.php';

$application_name = Request :: get('application');
$this_section = $application_name;

// If application path doesn't exist, block the user
if(!WebApplication :: is_active($application_name))
{
	Display :: not_allowed();
}

//require_once Path ::get_application_path().'lib/weblcms/tool/assessment/assessment_tool.class.php';

Translation :: set_application($this_section);
Theme :: set_application($this_section);

if (!Authentication :: is_valid() && !isset($_GET[AssessmentTool :: PARAM_INVITATION_ID]))
{
	Display :: not_allowed();
}

// Load the current user
$user_manager = new UserManager(Session :: get_user_id());
$user = $user_manager->retrieve_user(Session :: get_user_id());

// Load & run the application
$application = WebApplication :: factory($application_name, $user);
$application->set_parameter('application', $application_name);
$application->run();
?>