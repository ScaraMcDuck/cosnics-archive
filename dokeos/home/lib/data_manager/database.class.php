<?php
/**
 * @package users
 * @subpackage datamanager
 */
require_once dirname(__FILE__) . '/database/database_home_tab_result_set.class.php';
require_once dirname(__FILE__) . '/database/database_home_row_result_set.class.php';
require_once dirname(__FILE__) . '/database/database_home_column_result_set.class.php';
require_once dirname(__FILE__) . '/database/database_home_block_result_set.class.php';
require_once dirname(__FILE__) . '/database/database_home_block_config_result_set.class.php';
require_once dirname(__FILE__) . '/../home_data_manager.class.php';
require_once dirname(__FILE__) . '/../home_tab.class.php';
require_once dirname(__FILE__) . '/../home_row.class.php';
require_once dirname(__FILE__) . '/../home_column.class.php';
require_once dirname(__FILE__) . '/../home_block.class.php';
require_once dirname(__FILE__) . '/../home_block_config.class.php';
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

class DatabaseHomeDataManager extends HomeDataManager
{
    const ALIAS_TAB_TABLE = 't';
    const ALIAS_ROW_TABLE = 'r';
    const ALIAS_COLUMN_TABLE = 'c';
    const ALIAS_BLOCK_TABLE = 'b';
    const ALIAS_BLOCK_CONFIG_TABLE = 'bc';
    const ALIAS_MAX_SORT = 'max_sort';

    /**
     * The database connection.
     */
    private $connection;

    /**
     * The table name prefix, if any.
     */
    private $prefix;

    function initialize()
    {
        $this->database = new Database();
        $this->database->set_prefix('home_');
    }

    private static function is_home_row_column($name)
    {
        return HomeRow :: is_default_property_name($name); //|| $name == User :: PROPERTY_TYPE || $name == User :: PROPERTY_DISPLAY_ORDER_INDEX || $name == User :: PROPERTY_USER_ID;
    }

    private static function is_home_column_column($name)
    {
        return HomeColumn :: is_default_property_name($name); //|| $name == User :: PROPERTY_TYPE || $name == User :: PROPERTY_DISPLAY_ORDER_INDEX || $name == User :: PROPERTY_USER_ID;
    }

    private static function is_home_block_column($name)
    {
        return HomeBlock :: is_default_property_name($name); //|| $name == User :: PROPERTY_TYPE || $name == User :: PROPERTY_DISPLAY_ORDER_INDEX || $name == User :: PROPERTY_USER_ID;
    }

    function get_next_home_row_id()
    {
        return $this->database->get_next_id(HomeRow :: get_table_name());
    }

    function get_next_home_tab_id()
    {
        return $this->database->get_next_id(HomeTab :: get_table_name());
    }

    function get_next_home_column_id()
    {
        return $this->database->get_next_id(HomeColumn :: get_table_name());
    }

    function get_next_home_block_id()
    {
        return $this->database->get_next_id(HomeBlock :: get_table_name());
    }

    function create_storage_unit($name, $properties, $indexes)
    {
        return $this->database->create_storage_unit($name, $properties, $indexes);
    }

    function count_home_rows($condition = null)
    {
        return $this->database->count_objects(HomeRow :: get_table_name(), $condition);
    }

    function count_home_columns($condition = null)
    {
        return $this->database->count_objects(HomeColumn :: get_table_name(), $condition);
    }

    function count_home_blocks($condition = null)
    {
        return $this->database->count_objects(HomeBlock :: get_table_name(), $condition);
    }

    function retrieve_home_rows($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
    {
        return $this->database->retrieve_objects(HomeRow :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
    }

    function retrieve_home_row($id)
    {
        $condition = new EqualityCondition(HomeRow :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(HomeRow :: get_table_name(), $condition);
    }

    function retrieve_home_tabs($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
    {
        return $this->database->retrieve_objects(HomeTab :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
    }

    function retrieve_home_tab($id)
    {
        $condition = new EqualityCondition(HomeTab :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(HomeTab :: get_table_name(), $condition);
    }

    function retrieve_home_tab_blocks($home_tab)
    {
        $query = 'SELECT * FROM ' . $this->database->escape_table_name(HomeBlock :: get_table_name()) . ' AS ' . self :: ALIAS_BLOCK_TABLE . ' WHERE ' . $this->database->escape_column_name(HomeBlock :: PROPERTY_COLUMN) . ' IN (SELECT ' . $this->database->escape_column_name(HomeColumn :: PROPERTY_ID) . ' FROM ' . $this->database->escape_table_name(HomeColumn :: get_table_name()) . ' AS ' . self :: ALIAS_COLUMN_TABLE . ' WHERE ' . $this->database->escape_column_name(HomeColumn :: PROPERTY_ROW) . ' IN (SELECT ' . $this->database->escape_column_name(HomeRow :: PROPERTY_ID) . ' FROM ' . $this->database->escape_table_name(HomeRow :: get_table_name()) . ' AS ' . self :: ALIAS_ROW_TABLE . ' WHERE ' . $this->database->escape_column_name(HomeRow :: PROPERTY_TAB) . ' = ?))';
        $statement = $this->database->get_connection()->prepare($query);
        $res = $statement->execute($home_tab->get_id());
        return new ObjectResultSet($this->database, $res, HomeBlock :: get_table_name());
    }

    function retrieve_home_columns($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
    {
        return $this->database->retrieve_objects(HomeColumn :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
    }

    function retrieve_home_column($id)
    {
        $condition = new EqualityCondition(HomeColumn :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(HomeColumn :: get_table_name(), $condition);
    }

    function retrieve_home_blocks($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
    {
        return $this->database->retrieve_objects(HomeBlock :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
    }

    function retrieve_home_block($id)
    {
        $condition = new EqualityCondition(HomeBlock :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(HomeBlock :: get_table_name(), $condition);
    }

    function retrieve_max_sort_value($table, $column, $condition = null)
    {
        return $this->database->retrieve_max_sort_value($table, $column, $condition);
    }

    function create_home_block($home_block)
    {
        return $this->database->create($home_block);
    }

    function create_home_block_config($home_block_config)
    {
        return $this->database->create($home_block_config);
    }

    function create_home_column($home_column)
    {
        return $this->database->create($home_column);
    }

    function create_home_row($home_row)
    {
        return $this->database->create($home_row);
    }

    function create_home_tab($home_tab)
    {
        return $this->database->create($home_tab);
    }

    function truncate_home($user_id)
    {
        $failures = 0;

        $condition = new EqualityCondition(HomeBlock :: PROPERTY_USER, $user_id);
        if (! $this->database->delete(HomeBlock :: get_table_name(), $condition))
        {
            $failures ++;
        }

        $condition = new EqualityCondition(HomeColumn :: PROPERTY_USER, $user_id);
        if (! $this->database->delete(HomeColumn :: get_table_name(), $condition))
        {
            $failures ++;
        }

        $condition = new EqualityCondition(HomeRow :: PROPERTY_USER, $user_id);
        if (! $this->database->delete(HomeRow :: get_table_name(), $condition))
        {
            $failures ++;
        }

        $condition = new EqualityCondition(HomeTab :: PROPERTY_USER, $user_id);
        if (! $this->database->delete(HomeTab :: get_table_name(), $condition))
        {
            $failures ++;
        }

        if ($failures == 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function update_home_block($home_block)
    {
        $old_home_block = $this->retrieve_home_block($home_block->get_id());

        if ($old_home_block->get_column() !== $home_block->get_column())
        {
            $condition = new EqualityCondition(HomeBlock :: PROPERTY_COLUMN, $home_block->get_column());
            $sort = $this->retrieve_max_sort_value(HomeBlock :: get_table_name(), HomeBlock :: PROPERTY_SORT, $condition);
            $home_block->set_sort($sort + 1);
        }

        $condition = new EqualityCondition(HomeBlock :: PROPERTY_ID, $home_block->get_id());
        $this->database->update($home_block, $condition);

        if ($old_home_block->get_column() !== $home_block->get_column())
        {
            $query = 'UPDATE ' . $this->database->escape_table_name(HomeBlock :: get_table_name()) . ' SET ' . $this->database->escape_column_name(HomeBlock :: PROPERTY_SORT) . ' = ' . $this->database->escape_column_name(HomeBlock :: PROPERTY_SORT) . ' - 1 WHERE ' . $this->database->escape_column_name(HomeBlock :: PROPERTY_SORT) . ' > ? AND ' . $this->database->escape_column_name(HomeBlock :: PROPERTY_COLUMN) . ' = ?;';

            $statement = $this->database->get_connection()->prepare($query);
            $statement->execute(array($old_home_block->get_sort(), $old_home_block->get_column()));
        }

        return true;
    }

    function update_home_block_config($home_block_config)
    {
        $where = $this->escape_column_name(HomeBlockConfig :: PROPERTY_BLOCK_ID) . '=' . $home_block_config->get_block_id() . ' AND ' . $this->escape_column_name(HomeBlockConfig :: PROPERTY_VARIABLE) . '= "' . $home_block_config->get_variable() . '"';
        $props = array();
        $props[HomeBlockConfig :: PROPERTY_VALUE] = $home_block_config->get_value();

        $this->connection->loadModule('Extended');
        $this->connection->extended->autoExecute($this->get_table_name('block_config'), $props, MDB2_AUTOQUERY_UPDATE, $where);
        return true;
    }

    function update_home_row($home_row)
    {
        $old_home_row = $this->retrieve_home_row($home_row->get_id());

        if ($old_home_row->get_tab() !== $home_row->get_tab())
        {
            $condition = new EqualityCondition(HomeRow :: PROPERTY_TAB, $home_row->get_tab());
            $sort = $this->retrieve_max_sort_value(HomeRow :: get_table_name(), HomeRow :: PROPERTY_SORT, $condition);
            $home_row->set_sort($sort + 1);
        }

        $condition = new EqualityCondition(HomeRow :: PROPERTY_ID, $home_row->get_id());
        $this->database->update($home_row, $condition);

        if ($old_home_row->get_tab() !== $home_row->get_tab())
        {
            $query = 'UPDATE ' . $this->database->escape_table_name(HomeRow :: get_table_name()) . ' SET ' . $this->database->escape_column_name(HomeRow :: PROPERTY_SORT) . ' = ' . $this->database->escape_column_name(HomeRow :: PROPERTY_SORT) . ' - 1 WHERE ' . $this->database->escape_column_name(HomeRow :: PROPERTY_SORT) . ' > ? AND ' . $this->database->escape_column_name(HomeRow :: PROPERTY_TAB) . ' = ?;';

            $statement = $this->database->get_connection()->prepare($query);
            $statement->execute(array($old_home_row->get_sort(), $old_home_row->get_tab()));
        }

        return true;
    }

    function update_home_column($home_column)
    {
        $old_home_column = $this->retrieve_home_column($home_column->get_id());

        if ($old_home_column->get_row() !== $home_column->get_row())
        {
            $condition = new EqualityCondition(HomeColumn :: PROPERTY_ROW, $home_column->get_row());
            $sort = $this->retrieve_max_sort_value(HomeColumn :: get_table_name(), HomeColumn :: PROPERTY_SORT, $condition);
            $home_column->set_sort($sort + 1);
        }

        $condition = new EqualityCondition(HomeColumn :: PROPERTY_ID, $home_column->get_id());
        $this->database->update($home_column, $condition);

        if ($old_home_column->get_row() !== $home_column->get_row())
        {
            $query = 'UPDATE ' . $this->database->escape_table_name(HomeColumn :: get_table_name()) . ' SET ' . $this->database->escape_column_name(HomeColumn :: PROPERTY_SORT) . ' = ' . $this->database->escape_column_name(HomeColumn :: PROPERTY_SORT) . ' - 1 WHERE ' . $this->database->escape_column_name(HomeColumn :: PROPERTY_SORT) . ' > ? AND ' . $this->database->escape_column_name(HomeColumn :: PROPERTY_ROW) . ' = ?;';

            $statement = $this->database->get_connection()->prepare($query);
            $statement->execute(array($old_home_column->get_sort(), $old_home_column->get_row()));
        }

        return true;
    }

    function update_home_tab($home_tab)
    {
        $condition = new EqualityCondition(HomeTab :: PROPERTY_ID, $home_tab->get_id());
        return $this->database->update($home_tab, $condition);
    }

    function retrieve_home_block_at_sort($parent, $sort, $direction)
    {
        $query = 'SELECT * FROM ' . $this->escape_table_name('block') . ' WHERE ' . $this->escape_column_name(HomeBlock :: PROPERTY_COLUMN) . '=?';
        if ($direction == 'up')
        {
            $query .= ' AND ' . $this->escape_column_name(HomeBlock :: PROPERTY_SORT) . '<? ORDER BY ' . $this->escape_column_name(HomeBlock :: PROPERTY_SORT) . 'DESC';
        }
        elseif ($direction == 'down')
        {
            $query .= ' AND ' . $this->escape_column_name(HomeBlock :: PROPERTY_SORT) . '>? ORDER BY ' . $this->escape_column_name(HomeBlock :: PROPERTY_SORT) . 'ASC';
        }
        $res = $this->limitQuery($query, 1, null, array($parent, $sort));
        $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
        return $this->record_to_home_block($record);
    }

    function retrieve_home_column_at_sort($parent, $sort, $direction)
    {
        $query = 'SELECT * FROM ' . $this->escape_table_name('column') . ' WHERE ' . $this->escape_column_name(HomeColumn :: PROPERTY_ROW) . '=?';
        if ($direction == 'up')
        {
            $query .= ' AND ' . $this->escape_column_name(HomeColumn :: PROPERTY_SORT) . '<? ORDER BY ' . $this->escape_column_name(HomeColumn :: PROPERTY_SORT) . 'DESC';
        }
        elseif ($direction == 'down')
        {
            $query .= ' AND ' . $this->escape_column_name(HomeColumn :: PROPERTY_SORT) . '>? ORDER BY ' . $this->escape_column_name(HomeColumn :: PROPERTY_SORT) . 'ASC';
        }
        $res = $this->limitQuery($query, 1, null, array($parent, $sort));
        $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
        return $this->record_to_home_column($record);
    }

    function retrieve_home_row_at_sort($sort, $direction)
    {
        $query = 'SELECT * FROM ' . $this->escape_table_name('row') . ' WHERE ';
        if ($direction == 'up')
        {
            $query .= $this->escape_column_name(HomeRow :: PROPERTY_SORT) . '<? ORDER BY ' . $this->escape_column_name(HomeRow :: PROPERTY_SORT) . 'DESC';
        }
        elseif ($direction == 'down')
        {
            $query .= $this->escape_column_name(HomeRow :: PROPERTY_SORT) . '>? ORDER BY ' . $this->escape_column_name(HomeRow :: PROPERTY_SORT) . 'ASC';
        }
        $res = $this->limitQuery($query, 1, null, array($sort));
        $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
        return $this->record_to_home_row($record);
    }

    function retrieve_home_tab_at_sort($sort, $direction)
    {
        $query = 'SELECT * FROM ' . $this->escape_table_name('tab') . ' WHERE ';
        if ($direction == 'up')
        {
            $query .= $this->escape_column_name(HomeTab :: PROPERTY_SORT) . '<? ORDER BY ' . $this->escape_column_name(HomeTab :: PROPERTY_SORT) . 'DESC';
        }
        elseif ($direction == 'down')
        {
            $query .= $this->escape_column_name(HomeTab :: PROPERTY_SORT) . '>? ORDER BY ' . $this->escape_column_name(HomeTab :: PROPERTY_SORT) . 'ASC';
        }
        $res = $this->limitQuery($query, 1, null, array($sort));
        $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
        return $this->record_to_home_tab($record);
    }

    private function limitQuery($query, $limit, $offset, $params, $is_manip = false)
    {
        $this->connection->setLimit($limit, $offset);
        $statement = $this->connection->prepare($query, null, ($is_manip ? MDB2_PREPARE_MANIP : null));
        $res = $statement->execute($params);
        return $res;
    }

    function delete_home_row($home_row)
    {
        $condition = new EqualityCondition(HomeColumn :: PROPERTY_ROW, $home_row->get_id());
        $columns = $this->retrieve_home_columns($condition);

        while ($column = $columns->next_result())
        {
            $this->delete_home_column($column);
        }

        $condition = new EqualityCondition(HomeRow :: PROPERTY_ID, $home_row->get_id());
        return $this->database->delete(HomeRow :: get_table_name(), $condition);
    }

    function delete_home_tab($home_tab)
    {
        $condition = new EqualityCondition(HomeRow :: PROPERTY_TAB, $home_tab->get_id());
        $rows = $this->retrieve_home_rows($condition);

        while ($row = $rows->next_result())
        {
            $this->delete_home_row($row);
        }

        $condition = new EqualityCondition(HomeTab :: PROPERTY_ID, $home_tab->get_id());
        return $this->database->delete(HomeTab :: get_table_name(), $condition);
    }

    function delete_home_column($home_column)
    {
        $condition = new EqualityCondition(HomeBlock :: PROPERTY_COLUMN, $home_column->get_id());
        $blocks = $this->retrieve_home_blocks($condition);

        while ($block = $blocks->next_result())
        {
            $this->delete_home_block($block);
        }

        $condition = new EqualityCondition(HomeColumn :: PROPERTY_ID, $home_column->get_id());
        return $this->database->delete(HomeColumn :: get_table_name(), $condition);
    }

    function delete_home_block($home_block)
    {
        if (! $this->delete_home_block_configs($home_block))
        {
            return false;
        }

        $condition = new EqualityCondition(HomeBlock :: PROPERTY_ID, $home_block->get_id());
        return $this->database->delete(HomeBlock :: get_table_name(), $condition);
    }

    function delete_home_block_config($home_block_config)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(HomeBlockConfig :: PROPERTY_BLOCK_ID, $home_block_config->get_block_id());
        $conditions[] = new EqualityCondition(HomeBlockConfig :: PROPERTY_VARIABLE, $home_block_config->get_variable());
        $condition = new AndCondition($conditions);

        return $this->database->delete(HomeBlockConfig :: get_table_name(), $condition);
    }

    function delete_home_block_configs($home_block)
    {
        $condition = new EqualityCondition(HomeBlockConfig :: PROPERTY_BLOCK_ID, $home_block->get_id());
        return $this->database->delete(HomeBlockConfig :: get_table_name(), $condition);
    }

    function retrieve_home_block_config($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
    {
        return $this->database->retrieve_objects(HomeBlockConfig :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
    }

    function count_home_block_config($condition = null)
    {
        return $this->database->count_objects(HomeBlockConfig :: get_table_name(), $condition);
    }
}
?>