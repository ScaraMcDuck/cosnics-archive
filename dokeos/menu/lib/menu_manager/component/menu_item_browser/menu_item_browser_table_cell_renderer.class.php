<?php
/**
 * @package application.lib.menu.menu_manager.component.menupublicationbrowser
 */
require_once dirname(__FILE__).'/menu_item_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../menu_item_table/default_menu_item_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../menu_manager.class.php';
/**
 * Cell renderer for the learning object browser table
 */
class MenuItemBrowserTableCellRenderer extends DefaultMenuItemTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;
	/**
	 * Constructor
	 * @param MenuManagerManagerBrowserComponent $browser
	 */
	function MenuItemBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $menu)
	{
		if ($column === MenuItemBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($menu);
		}
		return parent :: render_cell($column, $menu);
	}
	/**
	 * Gets the action links to display
	 * @param Object $menu The menu object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($menu)
	{
		// TODO: Add functionality to menu item so it "knows" whether it's the first or the last item
		$index = 'middle';
		
		$toolbar_data = array();
		$edit_url = $this->browser->get_menu_item_editing_url($menu);
		$toolbar_data[] = array(
			'href' => $edit_url,
			'label' => Translation :: get('Edit'),
			'img' => Theme :: get_common_img_path().'action_edit.png'
		);
		
		if ($index == 'first' || $index == 'single')
		{
			$toolbar_data[] = array(
				'label' => Translation :: get('MoveUp'),
				'img' => Theme :: get_common_img_path().'action_up_na.png'
			);
		}
		else
		{
			$move_url = $this->browser->get_menu_item_moving_url($menu, 'up');
			$toolbar_data[] = array(
				'href' => $move_url,
				'label' => Translation :: get('MoveUp'),
				'img' => Theme :: get_common_img_path().'action_up.png'
			);
		}
		
		if ($index == 'last' || $index == 'single')
		{
			$toolbar_data[] = array(
				'label' => Translation :: get('MoveDown'),
				'img' => Theme :: get_common_img_path().'action_down_na.png'
			);
		}
		else
		{
			$move_url = $this->browser->get_menu_item_moving_url($menu, 'down');
			$toolbar_data[] = array(
				'href' => $move_url,
				'label' => Translation :: get('MoveDown'),
				'img' => Theme :: get_common_img_path().'action_down.png'
			);
		}

		$delete_url = $this->browser->get_menu_item_deleting_url($menu);
		$toolbar_data[] = array(
			'href' => $delete_url,
			'label' => Translation :: get('Delete'),
			'confirm' => true,
			'img' => Theme :: get_common_img_path().'action_delete.png'
		);
	
		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>