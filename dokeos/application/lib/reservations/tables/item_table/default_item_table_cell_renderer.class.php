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
class DefaultItemTableCellRenderer implements ObjectTableCellRenderer
{

	/**
	 * Constructor
	 */
	function DefaultItemTableCellRenderer($browser)
	{
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $item)
	{
		if ($name = $column->get_name())
		{
			switch ($name)
			{
				case Item :: PROPERTY_ID :
					return $item->get_id();
				case Item :: PROPERTY_NAME : 
					return $item->get_name();
				case Item :: PROPERTY_DESCRIPTION :
					//if(get_class($item) == 'Category') return null;
					$description = strip_tags($item->get_description());
					if(strlen($description) > 175)
					{
						$description = mb_substr($description,0,170).'&hellip;';
					}
					return  '<div style="word-wrap: break-word; max-width: 250px;" >' . $description . '</div>';
				case Item :: PROPERTY_RESPONSIBLE :
					//$user = UserDataManager :: get_instance()->retrieve_user($item->get_responsible());
					//return $user->get_fullname();
					return $item->get_responsible();
				case Item :: PROPERTY_CREDITS :
					//if(get_class($item) == 'Category') return null;
					return $item->get_credits() . ' ' . Translation :: get('per_hour');
			}
		}
		
		$title = $column->get_title();
		if($title == '')
		{
			$img = Theme :: get_common_image_path() . 'treemenu_types/document.png';
					return '<img src="' . $img . '"alt="' . get_class($item) . '" />';
		}
			
		return '&nbsp;';
	}
	
	function render_id_cell($item)
	{
		return $item->get_id();
	}
}
?>