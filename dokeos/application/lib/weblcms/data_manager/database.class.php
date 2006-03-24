<?php
require_once dirname(__FILE__).'/../weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/../learningobjectpublication.class.php';
require_once dirname(__FILE__).'/../learningobjectpublicationcategory.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/data_manager/database.class.php';

class DatabaseWebLCMSDataManager extends WebLCMSDataManager
{
	private $connection;

	private $repoDM;

	function initialize()
	{
		$this->repoDM = & RepositoryDataManager :: get_instance();
		$this->connection = & $this->repoDM->get_connection();
	}

	function retrieve_learning_object_publication($pid)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('learning_object_publication').' WHERE '.$this->escape_column_name('id').'=?';
		$res = & $this->connection->limitQuery($query, 0, 1, array ($pid));
		$record = $res->fetchRow(DB_FETCHMODE_ASSOC);
		return $this->record_to_publication($record);
	}

	function retrieve_learning_object_publications($course = null, $categories = null, $users = null, $groups = null, $conditions = null, $orderBy = array (), $orderDesc = array (), $firstIndex = 0, $maxObjects = -1)
	{
		$query = 'SELECT p.* FROM '.$this->escape_table_name('learning_object_publication').' AS p LEFT JOIN '.$this->escape_table_name('learning_object_publication_group').' AS pg ON p.'.$this->escape_column_name('id').'=pg.'.$this->escape_column_name('publication').' LEFT JOIN '.$this->escape_table_name('learning_object_publication_user').' AS pu ON p.'.$this->escape_column_name('id').'=pu.'.$this->escape_column_name('publication');
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
		$accessConditions = array ();
		if (!is_null($users))
		{
			if (is_array($users))
			{
				$users[] = null;
			}
			else
			{
				$users = array ($users, null);
			}
			foreach ($users as $u)
			{
				$accessConditions[] = new EqualityCondition('user', $u);
			}
		}
		if (!is_null($groups))
		{
			if (is_array($groups))
			{
				$groups[] = null;
			}
			else
			{
				$groups = array ($groups, null);
			}
			foreach ($groups as $g)
			{
				$accessConditions[] = new EqualityCondition('group', $g);
			}
		}
		/*
		 * Add user/group conditions to global condition.
		 */
		if (count($accessConditions))
		{
			$cond[] = new OrCondition($accessConditions);
			if (!is_null($conditions))
			{
				$cond[] = $conditions;
			}
			$conditions = new AndCondition($cond);
		}
		/*
		 * Always respect display order as a last resort.
		 */
		$orderBy[] = 'display_order';
		$orderDesc[] = SORT_ASC;
		/*
		 * Build query.
		 */
		$params = array ();
		if (!is_null($conditions))
		{
			$query .= ' WHERE '.$this->translate_condition($conditions, & $params);
		}
		$query .= ' ORDER BY '.$this->escape_column_name($orderBy[0]).' '. ($orderDesc[0] == SORT_ASC ? 'ASC' : 'DESC');
		for ($i = 1; $i < count($orderBy); $i ++)
		{
			$query .= ','.$this->escape_column_name($orderBy[$i]).' '. ($orderDesc[$i] == SORT_ASC ? 'ASC' : 'DESC');
		}
		// XXX: Is this necessary?
		if ($maxObjects < 0)
		{
			$maxObjects = 999999999;
		}
		/*
		 * Get publications.
		 */
		$res = & $this->connection->limitQuery($query, intval($firstIndex), intval($maxObjects), $params);
		$results = array ();
		while ($record = $res->fetchRow(DB_FETCHMODE_ASSOC))
		{
			$results[] = $this->record_to_publication($record);
		}
		return $results;
	}

	function count_learning_object_publications($course = null, $categories = null, $users = null, $groups = null, $conditions = null)
	{
			// TODO: Use SQL COUNT(*) etc.
	return count($this->retrieve_learning_object_publications($course, $categories, $users, $groups, $conditions));
	}

	function create_learning_object_publication($publication)
	{
		$id = $this->connection->nextId($this->get_table_name('learning_object_publication'));
		$props = array ();
		$props['id'] = $id;
		$props['learning_object'] = $publication->get_learning_object()->get_id();
		$props['course'] = $publication->get_course_id();
		$props['tool'] = $publication->get_tool();
		$props['category'] = $publication->get_category_id();
		$props['from_date'] = $publication->get_from_date();
		$props['to_date'] = $publication->get_to_date();
		$props['hidden'] = $publication->is_hidden();
		$props['display_order'] = $publication->get_display_order_index();
		$this->connection->autoExecute($this->get_table_name('learning_object_publication'), $props, DB_AUTOQUERY_INSERT);
		return $id;
	}

	function update_learning_object_publication($publication)
	{
		$where = $this->escape_column_name('id').'='.$publication->get_id();
		$props = array();
		//TODO add other properties
		$props['hidden'] = $publication->is_hidden();
		return $this->connection->autoExecute($this->get_table_name('learning_object_publication'), $props, DB_AUTOQUERY_UPDATE, $where);
	}

	function delete_learning_object_publication($publication)
	{
		$query = 'DELETE FROM '.$this->escape_table_name('learning_object_publication').' WHERE id = ?';
		$statement = $this->connection->prepare($query);
		$parameters['id'] = $publication->get_id();
		return $this->connection->execute($statement, $parameters);
	}

	function retrieve_publication_categories($course, $types)
	{
		if (!is_array($types))
		{
			$types = array($types);
		}
		$query = 'SELECT * FROM '.$this->escape_table_name('learning_object_publication_category').' WHERE '.$this->escape_column_name('course').'=? AND '.$this->escape_column_name('tool').' IN ('.str_repeat('?,',count($types)-1).'?)';
		$sth = $this->connection->prepare($query);
		$params = $types;
		array_unshift ($params, $course);
		$res = & $this->connection->execute($sth, $params);
		$cats = array ();
		while ($record = $res->fetchRow(DB_FETCHMODE_ASSOC))
		{
			$parent = $record['parent'];
			$cat = $this->record_to_publication_category($record);
			$siblings = & $cats[$parent];
			$siblings[] = $cat;
		}
		return $this->get_publication_category_tree(0, & $cats);
	}

	function move_learning_object_publication($publication, $places)
	{
		// TODO: Optimize.
		if ($places < 0)
		{
			$places = abs($places);
			for ($i = 0; $i < $places; $i++)
			{
				if (!$this->move_learning_object_publication_up($publication))
				{
					return $i;
				}
			}
		}
		else
		{
			for ($i = 0; $i < $places; $i++)
			{
				if (!$this->move_learning_object_publication_down($publication))
				{
					return $i;
				}
			}
		}
		return $places;
	}

	private function move_learning_object_publication_up($publication)
	{
		// TODO: Escape table and column names, use limitQuery.
		$sql = 'SELECT co1.id AS id1, co2.id AS id2, co1.display_order AS display_order1, co2.display_order AS display_order2 FROM '.$this->escape_table_name('learning_object_publication').' co1, '.$this->escape_table_name('learning_object_publication').' co2 WHERE co1.course = co2.course AND co1.tool = co2.tool AND co1.category = co2.category AND co1.id = ? AND co1.id <> co2.id AND co2.display_order < co1.display_order ORDER BY co2.display_order DESC LIMIT 1';
		$statement = $this->connection->prepare($sql);
		$result =& $this->connection->execute($statement, array ($publication->get_id()));
		if ($result->numRows() == 0)
		{
			return false;
		}
		$obj = $result->fetchRow(DB_FETCHMODE_OBJECT);
		$sql = 'UPDATE '.$this->escape_table_name('learning_object_publication').' SET display_order = ? WHERE id = ?';
		$statement = $this->connection->prepare($sql);
		$this->connection->execute($statement, array ($obj->display_order1, $obj->id2));
		$this->connection->execute($statement, array ($obj->display_order2, $obj->id1));
		return true;
	}

	private function move_learning_object_publication_down($publication)
	{
		// TODO: Escape table and column names, use limitQuery.
		$sql = 'SELECT co1.id AS id1, co2.id AS id2, co1.display_order AS display_order1, co2.display_order AS display_order2 FROM '.$this->escape_table_name('learning_object_publication').' co1, '.$this->escape_table_name('learning_object_publication').' co2 WHERE co1.course = co2.course AND co1.tool = co2.tool AND co1.category = co2.category AND co1.id = ? AND co1.id <> co2.id AND co2.display_order > co1.display_order ORDER BY co2.display_order ASC LIMIT 1';
		$statement = $this->connection->prepare($sql);
		$result =& $this->connection->execute($statement, array ($publication->get_id()));
		if ($result->numRows() == 0)
		{
			return false;
		}
		$obj = $result->fetchRow(DB_FETCHMODE_OBJECT);
		$sql = 'UPDATE '.$this->escape_table_name('learning_object_publication').' SET display_order = ? WHERE id = ?';
		$statement = $this->connection->prepare($sql);
		$this->connection->execute($statement, array ($obj->display_order1, $obj->id2));
		$this->connection->execute($statement, array ($obj->display_order2, $obj->id1));
		return true;
	}

	function get_next_learning_object_publication_display_order_index($course,$tool,$category)
	{
		// TODO: Escape table and column names, limitQuery.
		$query = 'SELECT MAX(display_order)+1 AS new_display_order FROM '.$this->escape_table_name('learning_object_publication').' WHERE '.$this->escape_column_name('course').'=? AND '.$this->escape_column_name('tool').'=?  AND '.$this->escape_column_name('category').'=?';
		$statement = $this->connection->prepare($query);
		$params['course'] = $course;
		$params['tool'] = $tool;
		$params['category'] = $category;
		$res = & $this->connection->execute($statement, $params);
		$record = $res->fetchRow(DB_FETCHMODE_ASSOC);
		$new_display_order = $record['new_display_order'];
		if(!is_null($new_display_order))
		{
			return $new_display_order;
		}
		return 1;
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
		return new LearningObjectPublicationCategory($record['id'], $record['title'], $record['course'], $record['tool'], $record['parent']);
	}

	private function record_to_publication($record)
	{
		$obj = $this->repoDM->retrieve_learning_object($record['learning_object']);
		return new LearningObjectPublication($record['id'], $obj, $record['course'], $record['tool'], $record['category'], $record['target_users'], $record['target_groups'], self :: from_db_date($record['from_date']), self :: from_db_date($record['to_date']), $record['hidden'] != 0, $record['display_order']);
	}

	private function translate_condition($condition, & $params)
	{
		return $this->repoDM->translate_condition($condition, & $params);
	}

	private function get_table_name($name)
	{
		return $this->repoDM->get_table_name($name);
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