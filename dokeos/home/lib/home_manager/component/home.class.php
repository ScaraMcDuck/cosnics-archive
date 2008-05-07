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
			$html[] = '<div class="row" id="r'. $row->get_id() .'_'. $row->get_title() .'" style="'.($row->get_height() > 10 ? 'height: '. $row->get_height() .'px; ' : '') . ($row_number < $rows->size() ? 'margin-bottom: 15px;' : '') .'">';
			
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
					
					if ($component[0] != 'User' && Session :: get_user_id())
					{
						$app = new $component[0]($this->get_user());
						$html[] = $app->render_block(strtolower($component[1]), $block);
					}
					elseif ($component[0] == 'User')
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
		$html[] = '	serial = $.SortSerialize(s);';
		$html[] = '	alert(serial.hash);';
		$html[] = '};';
		$html[] = '</script>';
		
		
		return implode("\n", $html);
	}
}
?>