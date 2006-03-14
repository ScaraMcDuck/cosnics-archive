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
		$query = 'SELECT * FROM '.$this->escape_table_name('learning_object_publication').' WHERE '.$this->escape_column_name('id').'=? LIMIT 1';
		$sth = $this->connection->prepare($query);
		$res = & $this->connection->execute($sth, $pid);
		$record = $res->fetchRow(DB_FETCHMODE_ASSOC);
		return $this->record_to_publication($record);
	}

	function retrieve_learning_object_publications($course = null, $user = null, $conditions = null, $orderBy = array (), $orderDesc = array (), $firstIndex = 0, $maxObjects = -1)
	{
		$query = 'SELECT * FROM (SELECT '
			. $this->escape_table_name('learning_object_publication')
			. '.*, '
			. 'NULL AS ' . $this->escape_column_name('user')
			. ' FROM '
			. $this->escape_table_name('learning_object_publication')
			. ' UNION '
			. 'SELECT '
			. $this->escape_table_name('learning_object_publication')
			. '.*, '
			. $this->escape_table_name('learning_object_publication_user')
			. '.' . $this->escape_column_name('user')
			. ' AS ' . $this->escape_column_name('user')
			. ' FROM '
			. $this->escape_table_name('learning_object_publication')
			. ' JOIN '
			. $this->escape_table_name('learning_object_publication_user')
			. ' ON '
			. $this->escape_table_name('learning_object_publication')
			. '.' . $this->escape_column_name('id')
			. '='
			. $this->escape_table_name('learning_object_publication_user')
			. '.' . $this->escape_column_name('publication')
			. ' UNION '
			. 'SELECT '
			. $this->escape_table_name('learning_object_publication')
			. '.*, '
			. $this->escape_table_name('user_group')
			. '.' . $this->escape_column_name('user')
			. ' AS ' . $this->escape_column_name('user')
			. ' FROM '
			. $this->escape_table_name('learning_object_publication')
			. ' JOIN '
			. $this->escape_table_name('learning_object_publication_group')
			. ' ON '
			. $this->escape_table_name('learning_object_publication')
			. '.' . $this->escape_column_name('id')
			. '='
			. $this->escape_table_name('learning_object_publication_group')
			. '.' . $this->escape_column_name('publication')
			. ' JOIN '
			. $this->escape_table_name('user_group')
			. ' ON '
			. $this->escape_table_name('learning_object_publication_group')
			. '.' . $this->escape_column_name('group')
			. '='
			. $this->escape_table_name('user_group')
			. '.' . $this->escape_column_name('group')
			. ')';
		$cond = array ();
		if (!is_null($course))
		{
			$cond[] = new EqualityCondition('course', $course);
		}
		if (!is_null($user))
		{
			$cond[] = new EqualityCondition('user', $user);
		}
		if (count($cond))
		{
			$c = new AndCondition($cond);
			$conditions = (is_null($conditions) ? $c : new AndCondition($c, $conditions));
		}
		$params = array ();
		if (isset ($conditions))
		{
			$query .= ' WHERE '.$this->translate_condition($conditions, & $params);
		}
		$sth = $this->connection->prepare($query);
		$res = & $this->connection->execute($sth, $params);
		$results = array ();
		while ($record = $res->fetchRow(DB_FETCHMODE_ASSOC))
		{
			$results[] = $this->record_to_publication($record);
		}
		return $results;
	}

	function count_learning_object_publications($course = null, $user = null, $conditions = null)
	{
		// TODO: Use SQL COUNT(*) etc.
		return count($this->retrieve_learning_object_publications($course, $user));
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
		return new LearningObjectPublication($record['id'], $obj, $record['course'], $record['target_users'], $record['target_groups'], self :: from_db_date($record['from_date']), self :: from_db_date($record['to_date']), $record['hidden'] != 0, $record['display_order']);
	}

	private function translate_condition($condition, & $params)
	{
		return $this->repoDM->translate_condition($condition, & $params);
	}

	private function escape_table_name($name)
	{
		return $this->repoDM->escape_table_name($name);
	}

	private function escape_column_name($name)
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