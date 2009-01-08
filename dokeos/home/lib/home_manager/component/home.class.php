<?php
require_once dirname(__FILE__).'/../home_manager.class.php';
require_once dirname(__FILE__).'/../home_manager_component.class.php';
require_once dirname(__FILE__).'/../../home_tab.class.php';
require_once dirname(__FILE__).'/../../home_row.class.php';

class HomeManagerHomeComponent extends HomeManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->truncate();
		
		$output = $this->get_home_html();
		
		$this->display_portal_header($trail);
		echo $output;
		$this->display_footer();
	}
	
	function get_home_html()
	{
		$current_tab = Request :: get('tab');
		
		$html = array();
		
		$user = $this->get_user();		
		$user_home_allowed = $this->get_platform_setting('allow_user_home');
		
		// Get user id
		if ($user_home_allowed && Authentication :: is_valid())
		{
			$user_id = $user->get_id();
		}
		else
		{
			$user_id = '0';
		}
		
		$tabs_condition = new EqualityCondition(HomeTab :: PROPERTY_USER, $user_id);
		$tabs = $this->retrieve_home_tabs($tabs_condition);
		
		// If the homepage can be personalised but we have no rows, get the
		// default (to prevent lockouts) and display a warning / notification
		// which tells the user he can personalise his homepage
		if ($user_home_allowed && Authentication :: is_valid() && $tabs->size() == 0)
		{
			$this->create_user_home();
			
			$tabs_condition = new EqualityCondition(HomeTab :: PROPERTY_USER, $user->get_id());
			$tabs = $this->retrieve_home_tabs($tabs_condition);
		}
		
		if ($tabs->size() > 1)
		{			
			$html[] = '<div id="tab_menu"><ul>';
			while ($tab = $tabs->next_result())
			{
				$tab_id = $tab->get_id();
				
				if (($tab_id == $current_tab) || ($tabs->position() == 'single') || (!isset($current_tab) && $tabs->position() == 'first'))
				{
					$class = 'current';
				}
				else
				{
					$class = 'normal';
				}
				
				$html[] = '<li class="'. $class .'" id="tab_select_'. $tab->get_id() .'"><strong>'. $tab->get_title() .'</strong></li>';
			}
			$html[] = '</ul>';
			$html[] = '<div id="tab_actions"><a href="" class="addTab"><img src="'. Theme :: get_common_image_path() .'action_add.png" style="vertical-align: middle;" />&nbsp;'. Translation :: get('AddTab') .'</a></div>';
			$html[] = '</div>';
			$html[] = '<div style="clear: both; height: 0px; line-height: 0px;">&nbsp;</div>';
		}
		
		$tabs = $this->retrieve_home_tabs($tabs_condition);
		
		while ($tab = $tabs->next_result())
		{
			$html[] = '<div class="tab" id="tab_'. $tab->get_id() .'" style="display: '. (($tabs->position() == 'first' || $tabs->position() == 'single' || $current_tab == $tab->get_id()) ? 'block' : 'none' ) . ';">';
			
			$rows_conditions = array();
			$rows_conditions[] = new EqualityCondition(HomeRow :: PROPERTY_TAB, $tab->get_id());
			$rows_conditions[] = new EqualityCondition(HomeRow :: PROPERTY_USER, $user_id);
			$rows_condition = new AndCondition($rows_conditions);		
			$rows = $this->retrieve_home_rows($rows_condition);
			
			while ($row = $rows->next_result())
			{ 
				$rows_position = $rows->position();
				$html[] = '<div class="row" id="row_'. $row->get_id() .'" style="'.($rows_position != 'last' ? 'margin-bottom: 1%;' : '') .'">';
				
				$conditions = array();
				$conditions[] = new EqualityCondition(HomeColumn :: PROPERTY_ROW, $row->get_id());
				$conditions[] = new EqualityCondition(HomeColumn :: PROPERTY_USER, $user_id);
				$condition = new AndCondition($conditions);
				
				// Get the user or platform columns
				$columns = $this->retrieve_home_columns($condition);
				
				while ($column = $columns->next_result())
				{
					$columns_position = $columns->position();
					
					$html[] = '<div class="column" id="column_'. $column->get_id() .'" style="width: '. $column->get_width() .'%;'. ($columns_position != 'last' ? ' margin-right: 1%;' : '') .'">';
					
					$conditions = array();
					$conditions[] = new EqualityCondition(HomeBlock :: PROPERTY_COLUMN, $column->get_id());
					$conditions[] = new EqualityCondition(HomeBlock :: PROPERTY_USER, $user_id);
					$condition = new AndCondition($conditions);
					
					$blocks = $this->retrieve_home_blocks($condition);
					
					$path = Path :: get_application_path() . 'lib';
					
					while ($block = $blocks->next_result())
					{					
						$application = $block->get_application();
						$application_class = Application :: application_to_class($application);
						
						if(!Application :: is_application($application))
						{
							$sys_app_path = Path :: get(SYS_PATH) . $application . '/lib/' . $application . '_manager' . '/' . $application . '_manager.class.php';
							require_once $sys_app_path;
							
							$application_class .= 'Manager';
							
							if (!is_null($this->get_user()))
							{
								$app = new $application_class($this->get_user());
								$html[] = $app->render_block($block);
							}
							elseif(($application == 'user' && $block->get_component() == 'login') ||
								   ($application == 'admin' && $block->get_component() == 'portal_home'))
							{
								$app = new $application_class($this->get_user());
								$html[] = $app->render_block($block);
							}
						}
						else
						{
							$toolPath = $path . '/' . $application . '/' . $application . '_manager';
							require_once $toolPath . '/' . $application . '.class.php';
							
							if (!is_null($this->get_user()))
							{
								$app = Application :: factory($application, $this->get_user());
								$html[] = $app->render_block($block);
							}
						}
					}
							
					$html[] = '</div>';
				}
			
				$html[] = '</div>';
				$html[] = '<div style="clear: both; height: 0px; line-height: 0px;">&nbsp;</div>';
			}
			
			$html[] = '</div>';
		}
		
		$html[] = '<div style="clear: both; height: 0px; line-height: 0px;">&nbsp;</div>';
		
		if ($user_home_allowed && Authentication :: is_valid())
		{
			$html[] = '<a class="addEl" style="display: none;" href="#">[ Add Content ]</a>';
			$html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/home_ajax.js' .'"></script>';
		}
		
		return implode("\n", $html);
	}
	
	function create_user_home()
	{
		$user = $this->get_user();
		
		$tabs_condition = new EqualityCondition(HomeTab :: PROPERTY_USER, '0');
		$tabs = $this->retrieve_home_tabs($tabs_condition);
		
		while ($tab = $tabs->next_result())
		{
			$old_tab_id = $tab->get_id();
			$tab->set_user($user->get_id());
			$tab->create();
			
			$rows_conditions = array();
			$rows_conditions[] = new EqualityCondition(HomeRow :: PROPERTY_TAB, $old_tab_id);
			$rows_conditions[] = new EqualityCondition(HomeRow :: PROPERTY_USER, '0');
			$rows_condition = new AndCondition($rows_conditions);
			$rows = $this->retrieve_home_rows($rows_condition);
			
			while ($row = $rows->next_result())
			{
				$old_row_id = $row->get_id();
				$row->set_user($user->get_id());
				$row->set_tab($tab->get_id());
				$row->create();
				
				$conditions = array();
				$conditions[] = new EqualityCondition(HomeColumn :: PROPERTY_ROW, $old_row_id);
				$conditions[] = new EqualityCondition(HomeColumn :: PROPERTY_USER, '0');
				$condition = new AndCondition($conditions);
				
				$columns = $this->retrieve_home_columns($condition);
				
				while ($column = $columns->next_result())
				{
					$old_column_id = $column->get_id();
					$column->set_user($user->get_id());
					$column->set_row($row->get_id());
					$column->create();
					
					$conditions = array();
					$conditions[] = new EqualityCondition(HomeBlock :: PROPERTY_COLUMN, $old_column_id);
					$conditions[] = new EqualityCondition(HomeBlock :: PROPERTY_USER, '0');
					$condition = new AndCondition($conditions);
					
					$blocks = $this->retrieve_home_blocks($condition);
					
					while ($block = $blocks->next_result())
					{
						$block->set_user($user->get_id());
						$block->set_column($column->get_id());
						$block->create();
					}				
				}
			}
		}
	}
}
?>