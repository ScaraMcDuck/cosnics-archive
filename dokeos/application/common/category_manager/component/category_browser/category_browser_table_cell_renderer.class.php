<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/category_browser_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once Path :: get_user_path() . 'lib/users_data_manager.class.php';
require_once dirname(__FILE__).'/../../category.class.php';
require_once dirname(__FILE__).'/../../category_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class CategoryBrowserTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	protected $browser;
	private $count;
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function CategoryBrowserTableCellRenderer($browser)
	{
		//parent :: __construct();
		$this->browser = $browser;
		$this->count = $browser->count_categories($browser->get_condition());
	}
	// Inherited
	function render_cell($column, $category)
	{
		if ($column === CategoryBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($category);
		}
		
		if ($property = $column->get_object_property())
		{
			switch ($property)
			{
				case Category :: PROPERTY_ID :
					return $category->get_id();
				case Category :: PROPERTY_NAME : 
					$url = $this->browser->get_browse_categories_url($category->get_id());
					return '<a href="' . $url . '" alt="' . $category->get_name() . '">' . $category->get_name() . '</a>';
			}

		}
		
		$title = $column->get_title();
		if($title == '')
		{
			$img = Theme :: get_theme_path() . 'treemenu_types/category.png';
			return '<img src="' . $img . '"alt="category" />';
		}
			
		return '&nbsp;';
		
	}
	/**
	 * Gets the action links to display
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($category)
	{
		$toolbar_data = array();
		
		$toolbar_data[] = array(
				'href' => $this->browser->get_update_category_url($category->get_id()),
				'label' => Translation :: get('Edit'),
				'img' => Theme :: get_common_img_path() . 'action_edit.png'
		);
		
		$toolbar_data[] = array(
				'href' => $this->browser->get_delete_category_url($category->get_id()),
				'label' => Translation :: get('Delete'),
				'img' => Theme :: get_common_img_path() . 'action_delete.png',
				'confirm' => true
		);
		
		if($category->get_display_order() > 1)
		{
			$toolbar_data[] = array(
					'href' => $this->browser->get_move_category_url($category->get_id(), -1),
					'label' => Translation :: get('MoveUp'),
					'img' => Theme :: get_common_img_path() . 'action_up.png',
			);
		}
		else
		{
			$toolbar_data[] = array(
					'label' => Translation :: get('MoveUpNA'),
					'img' => Theme :: get_common_img_path() . 'action_up_na.png',
			);
		}
		
		if($category->get_display_order() < $this->count)
		{
			$toolbar_data[] = array(
					'href' => $this->browser->get_move_category_url($category->get_id(), 1),
					'label' => Translation :: get('MoveDown'),
					'img' => Theme :: get_common_img_path() . 'action_down.png',
			);
		}
		else
		{
			$toolbar_data[] = array(
					'label' => Translation :: get('MoveDownNA'),
					'img' => Theme :: get_common_img_path() . 'action_down_na.png',
			);
		}
		
		return Utilities :: build_toolbar($toolbar_data);
	}
}
?>