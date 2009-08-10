<?php
/**
 * @package repository.usertable
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../item.class.php';
require_once Path :: get_user_path() . 'lib/user_data_manager.class.php';
/**
 * TODO: Add comment
 */
class DefaultCategoryTableCellRenderer implements ObjectTableCellRenderer
{

	/**
	 * Constructor
	 */
	function DefaultCategoryTableCellRenderer($browser)
	{
		
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $category)
	{
		if ($property = $column->get_object_property())
		{
			switch ($property)
			{
				case Category :: PROPERTY_ID :
					return $category->get_id();
				case Category :: PROPERTY_NAME : 
					$url = $this->browser->get_browse_categories_url($category->get_id());
					return '<a href="' . $url . '" alt="' . $category->get_name() . '">' . $category->get_name() . '</a>';
				case Category :: PROPERTY_POOL : 
					if($category->use_as_pool())
						return Translation :: get('Yes');
					else
						return Translation :: get('No');
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
}
?>