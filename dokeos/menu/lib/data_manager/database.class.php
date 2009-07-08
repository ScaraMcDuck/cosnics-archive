<?php
/**
 * @package users
 * @subpackage datamanager
 */
require_once dirname(__FILE__) . '/../menu_data_manager.class.php';
require_once dirname(__FILE__) . '/../navigation_item.class.php';
require_once Path :: get_library_path() . 'condition/condition_translator.class.php';
require_once 'MDB2.php';

/**
==============================================================================
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *	@author Tim De Pauw
 *	@author Bart Mollet
==============================================================================
 */

class DatabaseMenuDataManager extends MenuDataManager
{
    const ALIAS_CATEGORY_TABLE = 'c';
    const ALIAS_ITEM_TABLE = 'i';
    const ALIAS_MAX_SORT = 'max_sort';

    /**
     * The database connection.
     */
    private $database;

    function initialize()
    {
        $this->database = new Database();
        $this->database->set_prefix('menu_');
    }

    function get_database()
    {
        return $this->database;
    }

    function get_next_navigation_item_id()
    {
        $id = $this->database->get_next_id(NavigationItem :: get_table_name());
        return $id;
    }

    function create_storage_unit($name, $properties, $indexes)
    {
        return $this->database->create_storage_unit($name, $properties, $indexes);
    }

    function count_navigation_items($condition = null)
    {
        return $this->database->count_objects(NavigationItem :: get_table_name(), $condition);
    }

    function retrieve_navigation_items($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
    {
        return $this->database->retrieve_objects(NavigationItem :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
    }

    function retrieve_navigation_item($id)
    {
        $condition = new EqualityCondition(NavigationItem :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(NavigationItem :: get_table_name(), $condition);
    }

    function retrieve_navigation_item_at_sort($parent, $sort, $direction)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(NavigationItem :: PROPERTY_CATEGORY, $parent);

        if ($direction == 'up')
        {
            $conditions[] = new InequalityCondition(NavigationItem :: PROPERTY_SORT, InequalityCondition :: LESS_THAN, $sort);
            $order_direction = array(SORT_DESC);
        }
        elseif ($direction == 'down')
        {
            $conditions[] = new InequalityCondition(NavigationItem :: PROPERTY_SORT, InequalityCondition :: GREATER_THAN, $sort);
            $order_direction = array(SORT_ASC);
        }

        $condition = new AndCondition($conditions);

        return $this->database->retrieve_object(NavigationItem :: get_table_name(), $condition, array(NavigationItem :: PROPERTY_SORT), $order_direction);
    }

    function update_navigation_item($navigation_item)
    {
        $old_navigation_item = $this->retrieve_navigation_item($navigation_item->get_id());

        if ($old_navigation_item->get_category() !== $navigation_item->get_category())
        {
            $condition = new EqualityCondition(NavigationItem :: PROPERTY_CATEGORY, $navigation_item->get_category());
            $sort = $this->retrieve_max_sort_value(NavigationItem :: get_table_name(), NavigationItem :: PROPERTY_SORT, $condition);

            $navigation_item->set_sort($sort + 1);
        }

        $condition = new EqualityCondition(NavigationItem :: PROPERTY_ID, $navigation_item->get_id());
        $this->database->update($navigation_item, $condition);

        if ($old_navigation_item->get_category() !== $navigation_item->get_category())
        {
            $query = 'UPDATE ' . $this->database->escape_table_name(NavigationItem :: get_table_name()) . ' SET ' . $this->database->escape_column_name(NavigationItem :: PROPERTY_SORT) . ' = ' . $this->database->escape_column_name(NavigationItem :: PROPERTY_SORT) . ' - 1 WHERE ' . $this->database->escape_column_name(NavigationItem :: PROPERTY_SORT) . ' > ? AND ' . $this->database->escape_column_name(NavigationItem :: PROPERTY_CATEGORY) . ' = ?;';

            $statement = $this->database->get_connection()->prepare($query);
            $statement->execute(array($old_navigation_item->get_sort(), $old_navigation_item->get_category()));
        }

        return true;
    }

    function delete_navigation_item($navigation_item)
    {
        $condition = new EqualityCondition(NavigationItem :: PROPERTY_ID, $navigation_item->get_id());
        return $this->database->delete(NavigationItem :: get_table_name(), $condition);
    }

    function retrieve_max_sort_value($table, $column, $condition = null)
    {
        return $this->database->retrieve_max_sort_value($table, $column, $condition);
    }

    function create_navigation_item($navigation_item)
    {
        return $this->database->create($navigation_item);
    }

    function delete_navigation_items($condition = null)
    {
        return $this->database->delete_objects(NavigationItem :: get_table_name(), $condition);
    }
}
?>