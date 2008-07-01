<?php
/**
 * @package repository
 */
require_once dirname(__FILE__).'/../../common/global.inc.php';
require_once Path :: get_home_path() . 'lib/home_manager/home_manager.class.php';
require_once Path :: get_home_path() . 'lib/home_data_manager.class.php';

$user_home_allowed = PlatformSetting :: get('allow_user_home', HomeManager :: APPLICATION_NAME);

if ($user_home_allowed && Authentication :: is_valid())
{	
	$user_id		= Session :: get_user_id();
	$column_data	= explode('_', $_POST['column']);
	$column_width	= $_POST['width'];
	
	$hdm = HomeDataManager :: get_instance();
	
	$column = $hdm->retrieve_home_column($column_data[1]);
	
	if ($column->get_user() == $user_id)
	{
		$column->set_width($column_width);
		$column->update();
	}
}
?>