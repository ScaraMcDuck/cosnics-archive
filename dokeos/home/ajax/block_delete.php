<?php
/**
 * @package repository
 */
require_once dirname(__FILE__) . '/../../common/global.inc.php';
require_once Path :: get_home_path() . 'lib/home_manager/home_manager.class.php';
require_once Path :: get_home_path() . 'lib/home_data_manager.class.php';

$user_home_allowed = PlatformSetting :: get('allow_user_home', HomeManager :: APPLICATION_NAME);

if ($user_home_allowed && Authentication :: is_valid())
{
    $user_id = Session :: get_user_id();
    $block_data = explode('_', $_POST['block']);
    
    $hdm = HomeDataManager :: get_instance();
    
    $block = $hdm->retrieve_home_block($block_data[1]);
    
    if ($block->get_user() == $user_id)
    {
        $block->delete();
    }
}
?>