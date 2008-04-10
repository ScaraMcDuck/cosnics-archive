<?php
require_once dirname(__FILE__).'/../homemanager.class.php';
require_once dirname(__FILE__).'/../homemanagercomponent.class.php';

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
		
		$rows = $this->retrieve_home_rows();
		$row_number = 0;
		
		while ($row = $rows->next_result())
		{
			$row_number++;
			$html[] = '<div class="row" id="'. $row->get_title() .'" style="'.($row->get_height() > 10 ? 'height: '. $row->get_height() .'px; ' : '') . ($row_number < $rows->size() ? 'margin-bottom: 15px;' : '') .'">';
			
			$condition = new EqualityCondition(HomeColumn :: PROPERTY_ROW, $row->get_id());
		
			$columns = $this->retrieve_home_columns($condition);
			$column_number = 0;
			
			while ($column = $columns->next_result())
			{
				$column_number++;
				$html[] = '<div class="column" id="'. $column->get_title() .'" style="width: '. $column->get_width() .'px;'. ($column_number < $columns->size() ? 'margin-right: 15px;' : '') .'">';
				
				$condition = new EqualityCondition(HomeBlock :: PROPERTY_COLUMN, $column->get_id());
				
				$blocks = $this->retrieve_home_blocks($condition);
				
				while ($block = $blocks->next_result())
				{
					$component = explode('.', $block->get_component());
					//$app = new $component[0]($this->get_user());
					
					// TODO: Move code to seperate blocks for more freedom
					
					if ($component[0] != 'User')
					{
						$app = new $component[0]($this->get_user());
						$html[] = $app->render_block(strtolower($component[1]), $block);
					}
					else
					{
						$component = HomeManagerComponent :: factory($block->get_component(), $this->get_parent());
						$html[] = $component->render_as_html();
					}
				}
						
				$html[] = '</div>';
			}
		
		$html[] = '</div>';
		$html[] = '<div style="clear: both;"></div>';
		
		}
		
		$html[] = '<div style="clear: both;"></div>';
		
		return implode("\n", $html);
	}
}
?>