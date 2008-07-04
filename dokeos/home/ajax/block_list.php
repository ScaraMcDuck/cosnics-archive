<?php
/**
 * @package repository
 */
 $this_section = 'home';
 
require_once dirname(__FILE__).'/../../common/global.inc.php';
require_once dirname(__FILE__).'/../../common/block.class.php';
require_once Path :: get_home_path() . 'lib/home_manager/home_manager.class.php';
require_once Path :: get_home_path() . 'lib/home_data_manager.class.php';

Translation :: set_application('home');
Theme :: set_application($this_section);

$user_home_allowed = PlatformSetting :: get('allow_user_home', HomeManager :: APPLICATION_NAME);

if ($user_home_allowed && Authentication :: is_valid())
{
	$user_id	= Session :: get_user_id();
	
	$blocks			= Block :: get_platform_blocks();
	$applications	= $blocks['applications'];
	$components		= $blocks['components'];
	
	echo '<div id="addBlock" class="block" style="margin-bottom: 1%; display: none; background-color: #F6F6F6; padding: 15px; -moz-border-radius: 10px;">';
	echo '<div class="title">';
	echo Translation :: get('AddNewBlocks');
	echo '</div>';
	
	echo '<div style="clear: both;">';
	foreach ($applications as $application_key => $application_value)
	{
		$application_components = array(); 
		foreach($components[$application_key] as $component_key => $component_value)
		{
			$component_title = $application_value . ' > ' . $component_value;
			$component_id = $application_key . '.' . $component_key;
			
			echo '<div class="component" id="'. $component_id .'" style="float: left; background-color: white; margin-right: 5px; margin-bottom: 5px; height: 75px; width: 100px; overflow: hidden; text-align: center; font-size: 75%; font-weight: bolder;">';
			echo '<img style="margin: 5px;" src="'. Theme :: get_img_path('admin') . 'place_' . $application_key .'.png" alt="'. $component_title .'" title="'. $component_title .'"/>';
			echo '<br />';
			echo $component_value;
//			echo '';
//			echo '';
//			echo '';
//			echo '';
			echo '</div>';
			
			$application_components[] = Translation :: get($component_value);
		}
	}
	echo '<div class="clear">&nbsp;</div>';
	echo '</div>';
	
	echo '<div style="position: relative; bottom: -15px; background: url('. Theme :: get_img_path() .'background_ajax_add.png) no-repeat center; padding: 5px 0px 5px 0px; margin: 0px -15px 0px -15px; text-align: center;">';
	echo '<a class="closeScreen" href="#"><img src="'. Theme :: get_img_path() .'action_ajax_add.png" alt="'. Translation :: get('close') .'" title="'. Translation :: get('close') .'" /></a>';
	echo '</div>';
	echo '</div>';
}
?>