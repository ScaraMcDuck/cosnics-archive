<?php
require_once dirname(__FILE__).'/../weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/../learningobjectpublication.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/data_manager/database.class.php';

class DatabaseWebLCMSDataManager extends WebLCMSDataManager
{
	private $connection;
	
	private $prefix;
	
	private $repoDM;

	function initialize()
	{
		$this->repoDM = & RepositoryDataManager :: get_instance();
		$this->connection = & $this->repoDM->get_connection();
		$this->prefix = & $this->repoDM->get_table_name_prefix();
	}
	
	function retrieve_learning_object_publication($pid)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('learning_object_publication')
			.' WHERE '.$this->escape_column_name('id').'=? LIMIT 1';
		$sth = $this->connection->prepare($query);
		$res = & $this->connection->execute($sth, $pid);
		$record = $res->fetchRow(DB_FETCHMODE_ASSOC);
		return $this->record_to_publication($record);
	}

	function retrieve_learning_object_publications($conditions = null, $orderBy = array (), $orderDesc = array (), $firstIndex = 0, $maxObjects = -1)
	{
		$query  = 'SELECT lop.* FROM '
			.$this->escape_table_name('learning_object_publication_user').' AS lopu'
			.' JOIN '.$this->escape_table_name('learning_object_publication').' AS lop ON lop.'.$this->escape_column_name('id').'=lopu.'.$this->escape_column_name('publication')
			.' JOIN '.$this->escape_table_name('learning_object').' AS lo ON lop.'.$this->escape_column_name('learning_object').'=lo.'.$this->escape_column_name('id');
		$params = array();
		if (isset($conditions)) {
			$query .= ' WHERE '.$this->translate_condition($conditions, & $params);
		}
		$sth = $this->connection->prepare($query);
		$res = & $this->connection->execute($sth, $params);
		$results = array();
		while ($record = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
			$results[] = $this->record_to_publication($record);
		}
		return $results;
	}

	function count_learning_object_publications($conditions = null)
	{
		$query  = 'SELECT COUNT(*) FROM '
			.$this->escape_table_name('learning_object_publication_user').' AS lopu'
			.' JOIN '.$this->escape_table_name('learning_object_publication').' AS lop ON lop.'.$this->escape_column_name('id').'=lopu.'.$this->escape_column_name('publication')
			.' JOIN '.$this->escape_table_name('learning_object').' AS lo ON lop.'.$this->escape_column_name('learning_object').'=lo.'.$this->escape_column_name('id');
		$params = array();
		if (isset($conditions)) {
			$query .= ' WHERE '.$this->translate_condition($conditions, & $params);
		}
		$sth = $this->connection->prepare($query);
		$res = & $this->connection->execute($sth, $params);
		$record = $res->fetchRow(DB_FETCHMODE_ORDERED);
		return $record[0];
	}

	function create_learning_object_publication($publication)
	{
		// TODO
	}
	
	function update_learning_object_publication($publication)
	{
		// TODO
	}
	
	function delete_learning_object_publication($publication)
	{
		// TODO
	}
	
	private function record_to_publication($record)
	{
		$obj = $this->repoDM->retrieve_learning_object($record['learning_object']);
		return new LearningObjectPublication($record['id'],
			$obj,
			$record['course'],
			$record['target_users'],
			$record['target_groups'],
			self :: from_db_date($record['from_date']),
			self :: from_db_date($record['to_date']),
			$record['hidden'] != 0,
			$record['display_order']);
	}
	
	private function translate_condition($condition, & $params)
	{
		return $this->repoDM->translate_condition($condition, & $params);
	}
	
	private function escape_table_name ($name)
	{
		return $this->repoDM->escape_table_name($name);
	}

	private function escape_column_name ($name)
	{
		return $this->repoDM->escape_column_name($name);
	}
	
	private static function from_db_date($date)
	{
		return DatabaseRepositoryDataManager :: from_db_date($date);
	}
	
	private static function to_db_date($date)
	{
		return DatabaseRepositoryDataManager :: to_db_date($date);
	}
}
?>