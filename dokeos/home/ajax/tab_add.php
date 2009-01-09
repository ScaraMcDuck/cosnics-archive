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

$user_home_allowed = PlatformSetting :: get('allow_user_home', HomeManager :: APPLICATION_NAME);

if ($user_home_allowed && Authentication :: is_valid())
{
	$user_id		= Session :: get_user_id();
	
	$tab = new HomeTab();
	$tab->set_title(Translation :: get('NewTab'));
	$tab->set_user($user_id);
	$tab->create();
	
	$row = new HomeRow();
	$row->set_title(Translation :: get('NewRow'));
	$row->set_tab($tab->get_id());
	$row->set_user($user_id);
	if (!$row->create())
	{
		exit;
	}
	
	$column = new HomeColumn();
	$column->set_row($row->get_id());
	$column->set_title(Translation :: get('NewColumn'));
	$column->set_sort('1');
	$column->set_width('100');
	$column->set_user($user_id);
	if (!$column->create())
	{
		exit;
	}
	
	$block = new HomeBlock();
	$block->set_column($column->get_id());
	$block->set_title(Translation :: get('DummyBlock'));
	$block->set_application('repository');
	$block->set_component('dummy');
	$block->set_visibility('1');
	$block->set_user($user_id);
	if (!$block->create())
	{
		exit;
	}
	
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
	
	$html[] = '<div class="tab" id="tab_'. $tab->get_id() .'" style="display: none;">';
	$html[] = '<div class="row" id="row_'. $row->get_id() .'">';
	$html[] = '<div class="column" id="column_'. $column->get_id() .'" style="width: '. $column->get_width() .'%;">';
	$html[] = $app->render_block($block);
	$html[] = '</div>';
	$html[] = '</div>';
	$html[] = '</div>';
	
	echo implode("\n", $html);
}
?>