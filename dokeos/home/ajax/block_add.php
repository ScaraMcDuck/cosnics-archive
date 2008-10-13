<?php
/**
 * @package repository
 */
 $this_section = 'home';
 
require_once dirname(__FILE__).'/../../common/global.inc.php';
require_once Path :: get_home_path() . 'lib/home_manager/home_manager.class.php';
require_once Path :: get_home_path() . 'lib/home_data_manager.class.php';
require_once Path :: get_user_path() . 'lib/user_manager/user_manager.class.php';

Translation :: set_application('home');
Theme :: set_application($this_section);

function unserialize_jquery($jquery)
{
	$block_data = explode('&', $jquery);
	$blocks = array();
	
	foreach($block_data as $block)
	{
		$block_split = explode('=', $block);
		$blocks[] = $block_split[1];
	}
	
	return $blocks;
}

$user_home_allowed = PlatformSetting :: get('allow_user_home', HomeManager :: APPLICATION_NAME);

if ($user_home_allowed && Authentication :: is_valid())
{
	$user_id		= Session :: get_user_id();
	$component		= explode('.', $_POST['component']);
	$column_data	= explode('_', $_POST['column']);
	$blocks			= unserialize_jquery($_POST['order']);
	
	/*
	 * TODO: Make this accept input from the jQuery script, should automatically add the correct block to the homepage
	 */	
	
	$block = new HomeBlock();
	$block->set_column($column_data[1]);
	$block->set_title(DokeosUtilities :: underscores_to_camelcase($component[1]));
	$block->set_application($component[0]);
	$block->set_component($component[1]);
	$block->set_visibility('1');
	$block->set_user($user_id);
	
	$block->create();
	
	$usermgr = new UserManager($user_id);
	$user = $usermgr->get_user();
	
	$application = $block->get_application();
	$application_class = Application :: application_to_class($application);
	
	if(!Application :: is_application($application))
	{
		$path = Path :: get(SYS_PATH) . $application . '/lib/' . $application . '_manager' . '/' . $application . '_manager.class.php';
		require_once $path;
		$application_class .= 'Manager';
		$app = new $application_class($user);		
	}
	else
	{
		$path = Path :: get_application_path() . 'lib' . '/' . $application . '/' . $application . '_manager' . '/' . $application . '.class.php';
		require_once $path;
		$app = Application :: factory($application, $user);
	}
	
	echo $app->render_block($block);
}
?>