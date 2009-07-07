<?php
/**
 * @package application.weblcms
 * @subpackage datamanager.database
 */
require_once dirname(__FILE__) . '/../../../../common/database/result_set.class.php';
/**
 * This class represents a resultset which represents a set of courses.
 */
class DatabaseHomeBlockConfigResultSet extends ResultSet
{
    /**
     * The datamanager used to retrieve objects from the repository
     */
    private $data_manager;
    /**
     * An instance of DB_result
     */
    private $handle;
    
    private $current;

    /**
     * Create a new resultset for handling a set of learning objects
     * @param RepositoryDataManager $data_manager The datamanager used to
     * retrieve objects from the repository
     * @param DB_result $handle The handle to retrieve records from a database
     * resultset
     */
    function DatabaseHomeBlockConfigResultSet($data_manager, $handle)
    {
        $this->data_manager = $data_manager;
        $this->handle = $handle;
    }

    /*
	 * Inherited
	 */
    function next_result()
    {
        if ($record = $this->handle->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $this->current ++;
            return $this->data_manager->record_to_home_block_config($record);
        }
        return null;
    }

    /*
	 * Inherited
	 */
    function size()
    {
        return $this->handle->numRows();
    }

    /*
	 * Inherited
	 */
    function skip($count)
    {
        for($i = 0; $i < $count; $i ++)
        {
            $this->handle->fetchRow();
        }
    }

    function current()
    {
        return $this->current;
    }

    function position()
    {
        $current = $this->current();
        $size = $this->size();
        
        if ($current == 1 && $size == 1)
        {
            return 'single';
        }
        elseif ($size > 1 && $current == $size)
        {
            return 'last';
        }
        elseif ($size > 1 && $current == 1)
        {
            return 'first';
        }
        else
        {
            return 'middle';
        }
    }
}
?>