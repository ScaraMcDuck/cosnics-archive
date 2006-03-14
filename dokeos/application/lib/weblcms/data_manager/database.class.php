<?php
require_once dirname(__FILE__).'/../weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/../learningobjectpublication.class.php';
require_once dirname(__FILE__).'/../learningobjectpublicationcategory.class.php';
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

	function retrieve_learning_object_publications($course = null, $categories = null, $users = null, $groups = null, $conditions = null, $orderBy = array (), $orderDesc = array (), $firstIndex = 0, $maxObjects = -1)
	{
		$query = 'SELECT p.* FROM '
			.$this->escape_table_name('learning_object_publication')
			.' AS p'.' LEFT JOIN '
			.$this->escape_table_name('learning_object_publication_group')
			.' AS pg ON p.'.$this->escape_column_name('id')
			.'=pg.'.$this->escape_column_name('publication')
			.' LEFT JOIN '
			.$this->escape_table_name('learning_object_publication_user')
			.' AS pu ON p.'.$this->escape_column_name('id')
			.'=pu.'.$this->escape_column_name('publication')
			. ' JOIN '
			.$this->escape_table_name('learning_object_publication_category_link')
			.' AS pcl ON p.'.$this->escape_column_name('id')
			.'=pcl.'.$this->escape_column_name('publication');
		$cond = array ();
		if (!is_null($course))
		{
			$cond[] = new EqualityCondition('course', $course);
		}
		if (!is_null($categories))
		{
			if (is_array($categories))
			{
				$cc = array ();
				foreach ($categories as $cat)
				{
					$cc[] = new EqualityCondition('category', $cat);
				}
				$cond[] = new OrCondition($cc);
			}
			else
			{
				$cond[] = new EqualityCondition('category', $categories);
			}
		}
		/*
		 * Add 0 to allowed users and groups: for records without restriction.
		 */
		if (is_null($users))
		{
			$users = array (0);
		}
		elseif (is_array($users))
		{
			$users[] = 0;
		}
		else
		{
			$users = array ($users, 0);
		}
		if (is_null($groups))
		{
			$groups = array (0);
		}
		elseif (is_array($groups))
		{
			$groups[] = 0;
		}
		else
		{
			$groups = array ($groups, 0);
		}
		/*
		 * Condition for allowed users.
		 */
		$c = array ();
		foreach ($users as $u)
		{
			$c[] = new EqualityCondition('user', $u);
		}
		/*
		 * Condition for allowed groups.
		 */
		foreach ($groups as $g)
		{
			$c[] = new EqualityCondition('group', $g);
		}
		/*
		 * Add user/group conditions to global condition.
		 */
		$cond[] = new OrCondition($c);
		$conditions = (is_null($conditions) ? new AndCondition($cond) : new AndCondition($cond, $conditions));
		/*
		 * Always respect display order as a last resort.
		 */
		$orderBy[] = 'display_order';
		$orderDesc[] = SORT_ASC;
		/*
		 * Build query.
		 */
		$params = array ();
		$query .= ' WHERE '.$this->translate_condition($conditions, & $params);
		$query .= ' ORDER BY '.$this->escape_column_name($orderBy[0]).' '. ($orderDesc[0] == SORT_ASC ? 'ASC' : 'DESC');
		for ($i = 1; $i < count($orderBy); $i ++)
		{
			$query .= ','.$this->escape_column_name($orderBy[$i]).' '. ($orderDesc[$i] == SORT_ASC ? 'ASC' : 'DESC');
		}
		if ($maxObjects > 0)
		{
			if ($firstIndex > 0)
			{
				$query .= ' LIMIT '.$firstIndex.','.$maxObjects;
			}
			else
			{
				$query .= ' LIMIT '.$maxObjects;
			}
		}
		elseif ($firstIndex > 0)
		{
			$query .= ' LIMIT '.$firstIndex.',999999999999';
		}
		/*
		 * Get publications.
		 */
		$sth = $this->connection->prepare($query);
		$res = & $this->connection->execute($sth, $params);
		$results = array ();
		while ($record = $res->fetchRow(DB_FETCHMODE_ASSOC))
		{
			$results[] = $this->record_to_publication($record);
		}
		return $results;
	}

	function count_learning_object_publications($course = null, $categories = null, $user = null, $groups = null, $conditions = null)
	{
			// TODO: Use SQL COUNT(*) etc.
	return count($this->retrieve_learning_object_publications($course, $categories, $user, $groups));
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

	function retrieve_publication_categories($course, $type)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('learning_object_publication_category').' WHERE '.$this->escape_column_name('course').'=? AND '.$this->escape_column_name('type').'=?';
		$sth = $this->connection->prepare($query);
		$res = & $this->connection->execute($sth, array ($course, $type));
		$cats = array ();
		while ($record = $res->fetchRow(DB_FETCHMODE_ASSOC))
		{
			$parent = $record['parent'];
			$cat = $this->record_to_publication_category($record);
			$a = & $cats[$parent];
			$a[] = $cat;
		}
		return $this->get_publication_category_tree(0, & $cats);
	}

	private function get_publication_category_tree($parent, & $categories)
	{
		$subtree = array ();
		foreach ($categories[$parent] as $child)
		{
			$id = $child->get_id();
			$ar = array ();
			$ar['obj'] = $child;
			$ar['sub'] = $this->get_publication_category_tree($id, & $categories);
			$subtree[$id] = $ar;
		}
		return $subtree;
	}

	private static function record_to_publication_category($record)
	{
		return new LearningObjectPublicationCategory($record['id'], $record['title'], $record['course'], $record['type'], $record['parent']);
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