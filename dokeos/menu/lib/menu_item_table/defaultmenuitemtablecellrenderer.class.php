<?php
/**
 * @package application.lib.menu.menu_publication_table
 */

require_once dirname(__FILE__).'/menuitemtablecellrenderer.class.php';
require_once dirname(__FILE__).'/../menuitem.class.php';

class DefaultMenuItemTableCellRenderer implements MenuItemTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultMenuItemTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param MenuManagerTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $menu_publication The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $menu_item, $index)
	{
		if ($property = $column->get_menu_property())
		{
			switch ($property)
			{
				case MenuItem :: PROPERTY_TITLE :
					return $menu_item->get_title();
			}
		}
		return '&nbsp;';
	}
}
?>