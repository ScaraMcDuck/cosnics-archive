<?php
require_once dirname(__FILE__).'/../home_manager.class.php';
require_once dirname(__FILE__).'/../home_manager_component.class.php';

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
			$html[] = '<div class="row" id="r'. $row->get_id() .'_'. $row->get_title() .'" style="'.($row->get_height() > 10 ? 'height: '. $row->get_height() .'%; ' : '') . ($row_number < $rows->size() ? 'margin-bottom: 1%;' : '') .'">';
			
			$condition = new EqualityCondition(HomeColumn :: PROPERTY_ROW, $row->get_id());
			
			$columns = $this->retrieve_home_columns($condition);
			$column_number = 0;
			
			while ($column = $columns->next_result())
			{
				$column_number++;
				$html[] = '<div class="column" id="'. $column->get_title() .'" style="width: '. $column->get_width() .'%;'. ($column_number < $columns->size() ? 'margin-right: 1%;' : '') .'">';
				
				$condition = new EqualityCondition(HomeBlock :: PROPERTY_COLUMN, $column->get_id());
				
				$blocks = $this->retrieve_home_blocks($condition);
				
				while ($block = $blocks->next_result())
				{
					$application = $block->get_application();
					$application_class = Application :: application_to_class($application);
					
					$app = new $application_class($this->get_user());
					$html[] = $app->render_block($block);
				}
						
				$html[] = '</div>';
			}
		
		$html[] = '</div>';
		$html[] = '<div style="clear: both;"></div>';
		
		}
		$html[] = '<div style="clear: both;"></div>';
		
		
		$html[] = '<script type="text/javascript">';
		$html[] = '$(document).ready(';
		$html[] = '	function () {';
		$html[] = '		$(\'a.closeEl\').bind(\'click\', toggleContent);';
		$html[] = '		$(\'div.column\').Sortable(';
		$html[] = '			{';
		$html[] = '				accept: \'block\',';
		$html[] = '				helperclass: \'sortHelper\',';
		$html[] = '				activeclass : 	\'sortableactive\',';
		$html[] = '				hoverclass : 	\'sortablehover\',';
		$html[] = '				handle: \'div.title\',';
		$html[] = '				tolerance: \'pointer\',';
		$html[] = '				onChange : function(ser)';
		$html[] = '				{';
		$html[] = '				},';
		$html[] = '				onStart : function()';
		$html[] = '				{';
		$html[] = '					$.iAutoscroller.start(this, document.getElementsByTagName(\'body\'));';
		$html[] = '				},';
		$html[] = '				onStop : function()';
		$html[] = '				{';
		$html[] = '					$.iAutoscroller.stop();';
		$html[] = '				}';
		$html[] = '			}';
		$html[] = '		);';
		$html[] = '	}';
		$html[] = ');';
		$html[] = 'var toggleContent = function(e)';
		$html[] = '{';
		$html[] = '	var targetContent = $(\'div.description\', this.parentNode.parentNode);';
		$html[] = '	if (targetContent.css(\'display\') == \'none\') {';
		$html[] = '		targetContent.slideDown(300);';
		$html[] = '		$(this).html(\'[-]\');';
		$html[] = '	} else {';
		$html[] = '		targetContent.slideUp(300);';
		$html[] = '		$(this).html(\'[+]\');';
		$html[] = '	}';
		$html[] = '	return false;';
		$html[] = '};';
		$html[] = 'function serialize(s)';
		$html[] = '{';
		$html[] = '	serial = $.SortSerialize(\'Various\');';
		$html[] = '	alert(serial.hash);';
		$html[] = '};';
		$html[] = '</script>';
		
		$html[] = '<a href="#" onClick="serialize(); return false;" >serialize all lists</a>';
		
		
		return implode("\n", $html);
	}
}
?>