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
		$user_home_allowed = PlatformSetting :: get('allow_user_home', HomeManager :: APPLICATION_NAME);
		
		// TODO: Implement some kind of system which sets the homepage user according to some general things:
		// 1. User is logged in or not (anonymous leads to query problems)
		// 2. User can configure his own homepage
		
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
			$user_id = '0';
			$rows_condition = new EqualityCondition(HomeRow :: PROPERTY_USER, $user_id);
			$rows = $this->retrieve_home_rows($rows_condition);
			$html[] = '<div class="row" style="margin-bottom: 1%;">';
			$html[] = '<div class="column" style="width: 100%;">';
			$html[] = '<div class="block" style="background-image: url('. Theme :: get_common_img_path() .'status_warning.png);">';
			$html[] = '<div class="title">'. Translation :: get('PersonalHomepage') .'<a href="#" class="closeEl">[-]</a></div>';
			$html[] = '<div class="description">';
			$html[] = Translation :: get('PersonalHomepageWarning');
			$html[] = '<div style="clear: both;"></div>';
			$html[] = '</div>';
			$html[] = '</div>';
			$html[] = '</div>';
			$html[] = '</div>';
			$html[] = '<div style="clear: both;"></div>';
		}
		
		while ($row = $rows->next_result())
		{
			$rows_position = $rows->position();
			$html[] = '<div class="row" id="r_'. $row->get_id() .'_'. $row->get_title() .'" style="'.($row->get_height() > 10 ? 'height: '. $row->get_height() .'%; ' : '') . ($rows_position != 'last' ? 'margin-bottom: 1%;' : '') .'">';
			
			$conditions = array();
			$conditions[] = new EqualityCondition(HomeColumn :: PROPERTY_ROW, $row->get_id());
			$conditions[] = new EqualityCondition(HomeColumn :: PROPERTY_USER, $user_id);
			$condition = new AndCondition($conditions);
			
			// Get the user or platform columns
			$columns = $this->retrieve_home_columns($condition);
			
			while ($column = $columns->next_result())
			{
				$columns_position = $columns->position();
				$html[] = '<div class="column" id="c_'. $column->get_id() .'" style="width: '. $column->get_width() .'%;'. ($columns_position != 'last' ? ' margin-right: 1%;' : '') .'">';
				
				$conditions = array();
				$conditions[] = new EqualityCondition(HomeBlock :: PROPERTY_COLUMN, $column->get_id());
				$conditions[] = new EqualityCondition(HomeBlock :: PROPERTY_USER, $user_id);
				$condition = new AndCondition($conditions);
				
				$blocks = $this->retrieve_home_blocks($condition);
				
				while ($block = $blocks->next_result())
				{
					$application = $block->get_application();
					$application_class = Application :: application_to_class($application);
					
					if(!Application :: is_application($application))
					{
						$application_class .= 'Manager';
					}
					
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
						
				$html[] = '</div>';
			}
		
			$html[] = '</div>';
			$html[] = '<div style="clear: both;"></div>';
		
		}
		$html[] = '<div style="clear: both;"></div>';
		
		$html[] = '<script type="text/javascript">';
		$html[] = '
$(".title").click(function()
{
	$(this).next(".description").slideToggle(300);
});
					';
//		$html[] = '$(document).ready(';
//		$html[] = '	function () {';
//		$html[] = '		$(\'a.closeEl\').bind(\'click\', toggleContent);';
//		
//		$html[] = '		$(\'div.column\').Sortable(';
//		$html[] = '			{';
//		$html[] = '				accept: \'block\',';
//		$html[] = '				helperclass: \'sortHelper\',';
//		$html[] = '				activeclass : 	\'sortableactive\',';
//		$html[] = '				hoverclass : 	\'sortablehover\',';
//		$html[] = '				handle: \'div.title\',';
//		$html[] = '				tolerance: \'pointer\',';
//		$html[] = '				onChange : function(ser)';
//		$html[] = '				{';
//		$html[] = '				},';
//		$html[] = '				onStart : function()';
//		$html[] = '				{';
//		$html[] = '					$.iAutoscroller.start(this, document.getElementsByTagName(\'body\'));';
//		$html[] = '				},';
//		$html[] = '				onStop : function()';
//		$html[] = '				{';
//		$html[] = '					$.iAutoscroller.stop();';
//		$html[] = '				}';
//		$html[] = '			}';
//		$html[] = '		);';
//
//		$html[] = '	}';
//		$html[] = ');';
//		$html[] = 'var toggleContent = function(e)';
//		$html[] = '{';
//		$html[] = '	var targetContent = $(\'div.description\', this.parentNode.parentNode);';
//		$html[] = '	if (targetContent.css(\'display\') == \'none\') {';
//		$html[] = '		targetContent.slideDown(300);';
//		$html[] = '		$(this).html(\'[-]\');';
//		$html[] = '	} else {';
//		$html[] = '		targetContent.slideUp(300);';
//		$html[] = '		$(this).html(\'[+]\');';
//		$html[] = '	}';
//		$html[] = '	return false;';
//		$html[] = '};';
//		$html[] = 'function serialize(s)';
//		$html[] = '{';
//		$html[] = '	serial = $.SortSerialize(s);';
//		$html[] = '	alert(serial.hash);';
//		$html[] = '};';
		$html[] = '</script>';
//		
//		$html[] = '<a href="#" onClick="serialize(); return false;" >serialize all lists</a>';
		
		
		return implode("\n", $html);
	}
}
?>