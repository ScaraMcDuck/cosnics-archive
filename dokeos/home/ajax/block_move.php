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
	$user_id	= Session :: get_user_id();
	$column_id	= $_POST['column'];
	$block_id	= $_POST['block'];
	
	$hdm = HomeDataManager :: get_instance();
	 
	$block = $hdm->retrieve_home_block($block_id);
	$block->set_column($column_id);
	if ($block->update())
	{
		$json_result['success'] = '1';
		$json_result['message'] = Translation :: get('BlockMovedToTab');
	}
	else
	{
		$json_result['success'] = '0';
		$json_result['message'] = Translation :: get('BlockNotMovedToTab');
	}
}
else
{
	$json_result['success'] = '0';
	$json_result['message'] = Translation :: get('NotAuthorized');
}

// Return a JSON object
echo json_encode($json_result);
?>