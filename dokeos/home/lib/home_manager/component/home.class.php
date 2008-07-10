<?php
require_once dirname(__FILE__).'/../home_manager.class.php';
require_once dirname(__FILE__).'/../home_manager_component.class.php';
require_once dirname(__FILE__).'/../../home_row.class.php';

class HomeManagerHomeComponent extends HomeManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$output = $this->get_home_html();
		
		$this->display_header();
		echo $output;
		$this->display_footer();
	}
	
	function get_home_html()
	{
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
		
		$rows_condition = new EqualityCondition(HomeRow :: PROPERTY_USER, $user_id);
		$rows = $this->retrieve_home_rows($rows_condition);
		
		// If the homepage can be personalised but we have no rows, get the
		// default (to prevent lockouts) and display a warning / notification
		// which tells the user he can personalise his homepage
		if ($user_home_allowed && Authentication :: is_valid() && $rows->size() == 0)
		{
			$this->create_user_home();
			
			$rows_condition = new EqualityCondition(HomeRow :: PROPERTY_USER, $user->get_id());
			$rows = $this->retrieve_home_rows($rows_condition);
		}
		
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
						$application_class .= 'Manager';
						
						if (!is_null($this->get_user()))
						{
							$app = new $application_class($this->get_user());
							$html[] = $app->render_block($block);
						}
						elseif($application == 'user' && $block->get_component() == 'login')
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
		
		$html[] = '<div style="clear: both; height: 0px; line-height: 0px;">&nbsp;</div>';
		
		if ($user_home_allowed && Authentication :: is_valid())
		{
			$html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/home_ajax.js' .'"></script>';
		}
		
		$html[] = '<a class="addEl" href="#">[ Add Content ]</a>';
		
		return implode("\n", $html);
	}
	
	function create_user_home()
	{
		$user = $this->get_user();
		
		$rows_condition = new EqualityCondition(HomeRow :: PROPERTY_USER, '0');
		$rows = $this->retrieve_home_rows($rows_condition);
		
		while ($row = $rows->next_result())
		{
			$old_row_id = $row->get_id();
			$row->set_user($user->get_id());
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
?>