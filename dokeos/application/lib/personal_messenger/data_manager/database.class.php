<?php
require_once dirname(__FILE__).'/../personalmessagedatamanager.class.php';
require_once 'MDB2.php';

class DatabasePersonalMessageDataManager extends PersonalMessageDataManager {

	private $prefix;
	private $repoDM;
	
	function initialize()
	{
		$this->repoDM = & RepositoryDataManager :: get_instance();
		$conf = Configuration :: get_instance();
		$this->connection = MDB2 :: connect($conf->get_parameter('database', 'connection_string_personal_messenger'),array('debug'=>3,'debug_handler'=>array('PersonalMessengerDatamanager','debug')));
		$this->prefix = $conf->get_parameter('database', 'table_name_prefix');
		$this->connection->query('SET NAMES utf8');
	}
	
	/**
	 * Escapes a column name
	 * @param string $name
	 */
	private function escape_column_name($name)
	{
		list($table, $column) = explode('.', $name, 2);
		$prefix = '';

		if (isset($column))
		{
			$prefix = $table.'.';
			$name = $column;
		}
		return $prefix.$this->connection->quoteIdentifier($name);
	}
	
	/**
	 * Gets the full name of a given table (by adding the database name and a
	 * prefix if required)
	 * @param string $name
	 */
	private function get_table_name($name)
	{
		global $personal_messenger_database;
		return $personal_messenger_database.'.'.$this->prefix.$name;
	}
	
	function get_next_personal_message_publication_id()
	{
		return $this->connection->nextID($this->get_table_name('personal_messenger_publication'));
	}
}
?>