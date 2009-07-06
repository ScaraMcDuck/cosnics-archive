<?php
/**
 * @package application.lib.menu.menu_publication_table
 */

// TODO: Add functionality to menu item so it "knows" whether it's the first or the last item


require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../navigation_item.class.php';

class DefaultNavigationItemTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultNavigationItemTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param MenuManagerTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $menu_publication The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $navigation_item)
    {
        if ($property = $column->get_object_property())
        {
            switch ($property)
            {
                case NavigationItem :: PROPERTY_TITLE :
                    return $navigation_item->get_title();
            }
        }
        
        if ($title = $column->get_title())
        {
            switch ($title)
            {
                case Translation :: get(ucfirst(NavigationItem :: PROPERTY_TITLE)) :
                    return $navigation_item->get_title();
            }
        }
        
        return '&nbsp;';
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>