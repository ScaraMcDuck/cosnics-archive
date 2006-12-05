<?php
/**
 * $Id: repositorydatamanager.class.php 9176 2006-08-30 09:08:17Z bmol $
 * @package repository
 * @subpackage datamanager
 */
require_once 'DB/Table.php';
/**
 * This class can be used to create a table in the database.
 */
class DatabaseTable extends DB_Table{
	/**
	 * The columns defined in this table
	 */
	public $col = array();
	/**
	 * The indexes defined in this table
	 */
	public $idx = array();
	/**
	 * Creates a new table.
	 * @param DatabaseRepositoryDataManager $datamanager
	 * @param string $table_name
	 * @param array $columns
	 * @param array $indexes
	 */
    function DatabaseTable($datamanager,$table_name,$columns,$indexes) {
    	$this->col = $columns;
    	$this->idx = $indexes;
    	parent::DB_Table($datamanager->get_connection(),$table_name,true);
    }
}
?>