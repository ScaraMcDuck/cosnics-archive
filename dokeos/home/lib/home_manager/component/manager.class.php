<?php
/**
 * @package groups.groupsmanager
 */
require_once dirname(__FILE__).'/../homemanager.class.php';
require_once dirname(__FILE__).'/../homemanagercomponent.class.php';
require_once dirname(__FILE__).'/../../homedatamanager.class.php';
require_once dirname(__FILE__).'/wizards/buildwizard.class.php';

class HomeManagerManagerComponent extends HomeManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		global $this_section;
		$this_section='platform_admin';
			
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(HomeManager :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME)), Translation :: get('Home')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('HomeManager')));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}
		
		$this->display_header($trail);
		echo Translation :: get('HomeManagerIntro') . '<br /><br />';
		echo $this->get_manager_modification_links();
		echo $this->get_preview_html();
		
		$this->display_footer();
	}
	
	function get_preview_html()
	{
		$rows = $this->retrieve_home_rows();
		$values = $this->values;
		$row_amount = $values['rowsamount'];
		
		$html = array();
		
		$html[] = '<div style="border: 1px solid #000000; margin-top: 5px; padding: 15px;width: 500px;">';
		
		while ($row = $rows->next_result())
		{
			$html[] = '<div class="row" style="'.($rows->position() != 'last' && $rows->position() != 'single' ? 'margin-bottom: 15px;' : '') .'padding: 10px; text-align: center; line-height: 20px; font-size: 20pt; background-color: #9a9a9a; color: #FFFFFF;">';
			$html[] = Translation :: get('Row') . ':&nbsp;' . $row->get_title();
			$html[] = $this->get_row_modification_links($row, $rows->position());
			$html[] = '<br />';
			
			$condition = new EqualityCondition(HomeColumn :: PROPERTY_ROW, $row->get_id());
			$columns = $this->retrieve_home_columns($condition);
			
			while ($column = $columns->next_result())
			{								
				$column_width = floor((480 - ($columns->size() - 1) * 10) / $columns->size()) - 20;
				$html[] = '<div class="column" style="'.($columns->position() != 'last' && $columns->position() != 'single' ? 'margin-right: 10px;' : '') .'padding: 10px; text-align: center; width: '. $column_width .'px; font-size: 10pt;background-color: #E8E8E8; color: #000000;">';
				$html[] = Translation :: get('Column') . ':&nbsp;' . $column->get_title();
				$html[] = $this->get_column_modification_links($column, $columns->position());
				$html[] = '<br />';

				$condition = new EqualityCondition(HomeBlock :: PROPERTY_COLUMN, $column->get_id());
				$blocks = $this->retrieve_home_blocks($condition);
				
				while ($block = $blocks->next_result())
				{
					$html[] = '<div style="'.($blocks->position() != 'last' && $blocks->position() != 'single' ? 'margin-bottom: 10px;' : '') .'padding: 10px; text-align: center; width: '. ($column_width - 20) .'px; height: 40px; line-height: 20px; font-size: 8pt;background-color: #B8B8B8; color: #2F2F2F;">';
					$html[] = Translation :: get('Block') . ':&nbsp;' . $block->get_title();
					$html[] = $this->get_block_modification_links($block, $blocks->position());
					$html[] = '</div>';
					$html[] = '<div style="clear: both;"></div>';
				}
				$html[] = '</div>';			
			}
			$html[] = '<div style="clear: both;"></div>';
			$html[] = '</div>';
		}
		
		$html[] = '<div style="clear: both;"></div>';
		$html[] = '</div>';
		
		return implode("\n", $html);
	}
	
	private function get_row_modification_links($home_row, $index)
	{
		$toolbar_data = array();
		
		$edit_url = $this->get_home_row_editing_url($home_row);
		$toolbar_data[] = array(
			'href' => $edit_url,
			'label' => Translation :: get('Edit'),
			'confirm' => false,
			'img' => Theme :: get_common_img_path().'action-edit.png'
		);
		
		$edit_url = $this->get_home_row_deleting_url($home_row);
		$toolbar_data[] = array(
			'href' => $edit_url,
			'label' => Translation :: get('Delete'),
			'confirm' => true,
			'img' => Theme :: get_common_img_path().'action-delete.png'
		);
		
		if ($index == 'first' || $index == 'single')
		{
			$toolbar_data[] = array(
				'label' => Translation :: get('MoveUp'),
				'img' => Theme :: get_common_img_path().'action-up-na.png'
			);
		}
		else
		{
			$move_url = $this->get_home_row_moving_url($home_row, 'up');
			$toolbar_data[] = array(
				'href' => $move_url,
				'label' => Translation :: get('MoveUp'),
				'img' => Theme :: get_common_img_path().'action-up.png'
			);
		}
		
		if ($index == 'last' || $index == 'single')
		{
			$toolbar_data[] = array(
				'label' => Translation :: get('MoveDown'),
				'img' => Theme :: get_common_img_path().'action-down-na.png'
			);
		}
		else
		{
			$move_url = $this->get_home_row_moving_url($home_row, 'down');
			$toolbar_data[] = array(
				'href' => $move_url,
				'label' => Translation :: get('MoveDown'),
				'img' => Theme :: get_common_img_path().'action-down.png'
			);
		}
	
		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
	
	private function get_column_modification_links($home_column, $index)
	{
		$toolbar_data = array();
		
		$edit_url = $this->get_home_column_editing_url($home_column);
		$toolbar_data[] = array(
			'href' => $edit_url,
			'label' => Translation :: get('Edit'),
			'confirm' => false,
			'img' => Theme :: get_common_img_path().'action-edit.png'
		);
		
		$edit_url = $this->get_home_column_deleting_url($home_column);
		$toolbar_data[] = array(
			'href' => $edit_url,
			'label' => Translation :: get('Delete'),
			'confirm' => true,
			'img' => Theme :: get_common_img_path().'action-delete.png'
		);
		
		if ($index == 'first' || $index == 'single')
		{
			$toolbar_data[] = array(
				'label' => Translation :: get('MoveLeft'),
				'img' => Theme :: get_common_img_path().'action-left-na.png'
			);
		}
		else
		{
			$move_url = $this->get_home_column_moving_url($home_column, 'up');
			$toolbar_data[] = array(
				'href' => $move_url,
				'label' => Translation :: get('MoveLeft'),
				'img' => Theme :: get_common_img_path().'action-left.png'
			);
		}
		
		if ($index == 'last' || $index == 'single')
		{
			$toolbar_data[] = array(
				'label' => Translation :: get('MoveRight'),
				'img' => Theme :: get_common_img_path().'action-right-na.png'
			);
		}
		else
		{
			$move_url = $this->get_home_column_moving_url($home_column, 'down');
			$toolbar_data[] = array(
				'href' => $move_url,
				'label' => Translation :: get('MoveRight'),
				'img' => Theme :: get_common_img_path().'action-right.png'
			);
		}
	
		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
	
	private function get_block_modification_links($home_block, $index)
	{
		$toolbar_data = array();
		
		$edit_url = $this->get_home_block_editing_url($home_block);
		$toolbar_data[] = array(
			'href' => $edit_url,
			'label' => Translation :: get('Edit'),
			'img' => Theme :: get_common_img_path().'action-edit.png'
		);
		
		$configure_url = $this->get_home_block_configuring_url($home_block);
		$toolbar_data[] = array(
			'href' => $configure_url,
			'label' => Translation :: get('Configure'),
			'img' => Theme :: get_common_img_path().'action-config.png'
		);
		
		$edit_url = $this->get_home_block_deleting_url($home_block);
		$toolbar_data[] = array(
			'href' => $edit_url,
			'label' => Translation :: get('Delete'),
			'confirm' => true,
			'img' => Theme :: get_common_img_path().'action-delete.png'
		);
		
		if ($index == 'first' || $index == 'single')
		{
			$toolbar_data[] = array(
				'label' => Translation :: get('MoveUp'),
				'img' => Theme :: get_common_img_path().'action-up-na.png'
			);
		}
		else
		{
			$move_url = $this->get_home_block_moving_url($home_block, 'up');
			$toolbar_data[] = array(
				'href' => $move_url,
				'label' => Translation :: get('MoveUp'),
				'img' => Theme :: get_common_img_path().'action-up.png'
			);
		}
		
		if ($index == 'last' || $index == 'single')
		{
			$toolbar_data[] = array(
				'label' => Translation :: get('MoveDown'),
				'img' => Theme :: get_common_img_path().'action-down-na.png'
			);
		}
		else
		{
			$move_url = $this->get_home_block_moving_url($home_block, 'down');
			$toolbar_data[] = array(
				'href' => $move_url,
				'label' => Translation :: get('MoveDown'),
				'img' => Theme :: get_common_img_path().'action-down.png'
			);
		}
	
		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
	
	function get_manager_modification_links()
	{
		$toolbar_data = array();
		
		$toolbar_data[] = array(
			'href' => $this->get_home_row_creation_url(),
			'label' => Translation :: get('AddRow'),
			'img' => Theme :: get_common_img_path().'action-add.png',
			'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
		);
		
		$toolbar_data[] = array(
			'href' => $this->get_home_column_creation_url(),
			'label' => Translation :: get('AddColumn'),
			'img' => Theme :: get_common_img_path().'action-add.png',
			'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
		);
		
		$toolbar_data[] = array(
			'href' => $this->get_home_block_creation_url(),
			'label' => Translation :: get('AddBlock'),
			'img' => Theme :: get_common_img_path().'action-add.png',
			'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
		);
		
		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
}
?>