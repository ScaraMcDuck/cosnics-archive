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
		$props[$this->escape_column_name('id')] = $id;
		$props[$this->escape_column_name('learning_object')] = $publication->get_learning_object()->get_id();
		$props[$this->escape_column_name('course')] = $publication->get_course_id();
		$props[$this->escape_column_name('tool')] = $publication->get_tool();
		$props[$this->escape_column_name('category')] = $publication->get_category_id();
		$props[$this->escape_column_name('from_date')] = $publication->get_from_date();
		$props[$this->escape_column_name('to_date')] = $publication->get_to_date();
		$props[$this->escape_column_name('hidden')] = $publication->is_hidden();
		$props[$this->escape_column_name('display_order')] = $publication->get_display_order_index();
		$this->connection->autoExecute($this->get_table_name('learning_object_publication'), $props, DB_AUTOQUERY_INSERT);
		$users = $publication->get_target_users();
		foreach($users as $index => $user_id)
		{
			$props = array();
			$props[$this->escape_column_name('publication')] = $id;
			$props[$this->escape_column_name('user')] = $user_id;
			$this->connection->autoExecute($this->get_table_name('learning_object_publication_user'), $props, DB_AUTOQUERY_INSERT);
		}
		$groups = $publication->get_target_groups();
		foreach($users as $index => $group_id)
		{
			$props = array();
			$props[$this->escape_column_name('publication')] = $id;
			$props[$this->escape_column_name('group')] = $group_id;
			$this->connection->autoExecute($this->get_table_name('learning_object_publication_group'), $props, DB_AUTOQUERY_INSERT);
		}
		return $id;
	}

	function update_learning_object_publication($publication)
	{
		// Delete target users and groups
		$parameters['id'] = $publication->get_id();
		$query = 'DELETE FROM '.$this->escape_table_name('learning_object_publication_user').' WHERE publication = ?';
		$statement = $this->connection->prepare($query);
		$this->connection->execute($statement, $parameters);
		$query = 'DELETE FROM '.$this->escape_table_name('learning_object_publication_group').' WHERE publication = ?';
		$statement = $this->connection->prepare($query);
		$this->connection->execute($statement, $parameters);
		// Add updated target users and groups
		$users = $publication->get_target_users();
		foreach($users as $index => $user_id)
		{
			$props = array();
			$props[$this->escape_column_name('publication')] = $publication->get_id();
			$props[$this->escape_column_name('user')] = $user_id;
			$this->connection->autoExecute($this->get_table_name('learning_object_publication_user'), $props, DB_AUTOQUERY_INSERT);
		}
		$groups = $publication->get_target_groups();
		foreach($users as $index => $group_id)
		{
			$props = array();
			$props[$this->escape_column_name('publication')] = $publication->get_id();
			$props[$this->escape_column_name('group')] = $group_id;
			$this->connection->autoExecute($this->get_table_name('learning_object_publication_group'), $props, DB_AUTOQUERY_INSERT);
		}
		// Update publication properties
		$where = $this->escape_column_name('id').'='.$publication->get_id();
		$props = array();
		$props[$this->escape_column_name('course')] = $publication->get_course_id();
		$props[$this->escape_column_name('tool')] = $publication->get_tool();
		$props[$this->escape_column_name('category')] = $publication->get_category_id();
		$props[$this->escape_column_name('from_date')] = $publication->get_from_date();
		$props[$this->escape_column_name('to_date')] = $publication->get_to_date();
		$props[$this->escape_column_name('hidden')] = $publication->is_hidden();
		$props[$this->escape_column_name('display_order')] = $publication->get_display_order_index();
		return $this->connection->autoExecute($this->get_table_name('learning_object_publication'), $props, DB_AUTOQUERY_UPDATE, $where);
	}

	function delete_learning_object_publication($publication)
	{
		$parameters['id'] = $publication->get_id();
		$query = 'DELETE FROM '.$this->escape_table_name('learning_object_publication_user').' WHERE publication = ?';
		$statement = $this->connection->prepare($query);
		$this->connection->execute($statement, $parameters);
		$query = 'DELETE FROM '.$this->escape_table_name('learning_object_publication_group').' WHERE publication = ?';
		$statement = $this->connection->prepare($query);
		$this->connection->execute($statement, $parameters);
		$query = 'UPDATE '.$this->escape_table_name('learning_object_publication').' SET '.$this->escape_column_name('display_order').'='.$this->escape_column_name('display_order').'-1 WHERE '.$this->escape_column_name('display_order').'>?';
		$statement = $this->connection->prepare($query);
		$this->connection->execute($statement, array($publication->get_display_order_index()));
		$query = 'DELETE FROM '.$this->escape_table_name('learning_object_publication').' WHERE id = ?';
		return $this->connection->limitQuery($query, 0, 1, $parameters);
	}

	function retrieve_learning_object_publication_categories($course, $types)
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

	function retrieve_learning_object_publication_category($id)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('learning_object_publication_category').' WHERE '.$this->escape_column_name('id').'=?';
		$res = & $this->connection->limitQuery($query, 0, 1, array($id));
		$record = $res->fetchRow(DB_FETCHMODE_ASSOC);
		return $this->record_to_publication_category($record);
	}
	
	function create_learning_object_publication_category($category)
	{
		$id = $this->connection->nextId($this->get_table_name('learning_object_publication_category'));
		$props = array();
		$props['id'] = $id;
		$props['title'] = $category->get_title();
		$props['parent'] = $category->get_parent();
		$props['course'] = $category->get_course();
		$props['tool'] = $category->get_tool();
		$this->connection->autoExecute($this->get_table_name('learning_object_publication_category'), $props, DB_AUTOQUERY_INSERT);
		$category->set_id($id);
	}
	
	function update_learning_object_publication_category($category)
	{
		$where = $this->escape_column_name('id').'='.$category->get_id();
		$props = array();
		$props['title'] = $category->get_title();
		$props['parent'] = $category->get_parent();
		/*
		 * XXX: Will course and tool ever change?
		 */ 
		$props['course'] = $category->get_course();
		$props['tool'] = $category->get_tool();
		$this->connection->autoExecute($this->get_table_name('learning_object_publication_category'), $props, DB_AUTOQUERY_UPDATE, $where);		
	}
	
	function delete_learning_object_publication_category($category)
	{
		/*
		 * TODO: Delete child categories and publications.
		 */
		$query = 'DELETE FROM '.$this->escape_table_name('learning_object_publication_category').' WHERE '.$this->escape_column_name('id').'=?';
		$this->connection->limitQuery($query, 0, 1, array($category->get_id()));
	}

	function move_learning_object_publication($publication, $places)
	{
		if ($places < 0)
		{
			return $this->move_learning_object_publication_up($publication, abs($places));
		}
		else
		{
			return $this->move_learning_object_publication_down($publication, $places);
		}
	}

	private function move_learning_object_publication_up($publication, $places)
	{
		$oldIndex = $publication->get_display_order_index();
		$query = 'UPDATE '.$this->escape_table_name('learning_object_publication').' SET '.$this->escape_column_name('display_order').'='.$this->escape_column_name('display_order').'+1 WHERE '.$this->escape_column_name('course').'=? AND '.$this->escape_column_name('tool').'=? AND '.$this->escape_column_name('category').'=? AND '.$this->escape_column_name('display_order').'<? ORDER BY '.$this->escape_column_name('display_order').' DESC';
		$this->connection->limitQuery($query, 0, $places, array($publication->get_course_id(), $publication->get_tool(), $publication->get_category_id(), $oldIndex));
		$rowsMoved = $this->connection->affectedRows();
		$query = 'UPDATE '.$this->escape_table_name('learning_object_publication').' SET '.$this->escape_column_name('display_order').'=? WHERE '.$this->escape_column_name('id').'=?';
		$this->connection->limitQuery($query, 0, 1, array($oldIndex - $places, $publication->get_id()));
		return $rowsMoved;
	}

	private function move_learning_object_publication_down($publication, $places)
	{
		$oldIndex = $publication->get_display_order_index();
		$query = 'UPDATE '.$this->escape_table_name('learning_object_publication').' SET '.$this->escape_column_name('display_order').'='.$this->escape_column_name('display_order').'-1 WHERE '.$this->escape_column_name('course').'=? AND '.$this->escape_column_name('tool').'=? AND '.$this->escape_column_name('category').'=? AND '.$this->escape_column_name('display_order').'>? ORDER BY '.$this->escape_column_name('display_order').' ASC';
		$this->connection->limitQuery($query, 0, $places, array($publication->get_course_id(), $publication->get_tool(), $publication->get_category_id(), $oldIndex));
		$rowsMoved = $this->connection->affectedRows();
		$query = 'UPDATE '.$this->escape_table_name('learning_object_publication').' SET '.$this->escape_column_name('display_order').'=? WHERE '.$this->escape_column_name('id').'=?';
		$this->connection->limitQuery($query, 0, 1, array($oldIndex + $places, $publication->get_id()));
		return $rowsMoved;
	}

	function get_next_learning_object_publication_display_order_index($course, $tool, $category)
	{
		$query = 'SELECT MAX('.$this->escape_column_name('display_order').') AS h FROM '.$this->escape_table_name('learning_object_publication').' WHERE '.$this->escape_column_name('course').'=? AND '.$this->escape_column_name('tool').'=?  AND '.$this->escape_column_name('category').'=?';
		$statement = $this->connection->prepare($query);
		$res = & $this->connection->execute($statement, array ($course, $tool, $category));
		$record = $res->fetchRow(DB_FETCHMODE_ASSOC);
		$highest_index = $record['h'];
		if (!is_null($highest_index))
		{
			return $highest_index +1;
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