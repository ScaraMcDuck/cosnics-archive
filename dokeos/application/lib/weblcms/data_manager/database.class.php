<?php
/**
 * $Id: database.class.php 10251 2006-11-29 15:03:20Z bmol $
 * @package application.weblcms
 * @subpackage datamanager
 */
require_once dirname(__FILE__).'/database/databaselearningobjectpublicationresultset.class.php';
require_once dirname(__FILE__).'/../weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/../learningobjectpublication.class.php';
require_once dirname(__FILE__).'/../learningobjectpublicationcategory.class.php';

class DatabaseWeblcmsDataManager extends WeblcmsDataManager
{

	const ALIAS_LEARNING_OBJECT_TABLE = 'lo';
	const ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE = 'lop';

	private $connection;
	private $repoDM;


	function initialize()
	{
		$this->repoDM = & RepositoryDataManager :: get_instance();
		$this->connection = $this->repoDM->get_connection();
	}

	/**
	 * Executes a query
	 * @param string $query The query (which will be used in a prepare-
	 * statement)
	 * @param int $limit The number of rows
	 * @param int $offset The offset
	 * @param array $params The parameters to replace the placeholders in the
	 * query
	 * @param boolean $is_manip Is the query a manipulation query
	 */
	private function limitQuery($query,$limit,$offset,$params,$is_manip = false)
	{
		$this->connection->setLimit($limit,$offset);
		$statement = $this->connection->prepare($query,null,($is_manip ? MDB2_PREPARE_MANIP : null));
		$res = $statement->execute($params);
		return $res;
	}

	function retrieve_learning_object_publication($pid)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('learning_object_publication').' WHERE '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_ID).'=?';
		$res = $this->limitQuery($query, 1, null, array ($pid));
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		return $this->record_to_publication($record);
	}

	function learning_object_is_published($object_id)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('learning_object_publication').' WHERE '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_LEARNING_OBJECT_ID).'=?';
		$res = $this->limitQuery($query, 1,null, array ($object_id));
		return $res->numRows() == 1;
	}

	function any_learning_object_is_published($object_ids)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('learning_object_publication').' WHERE '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_LEARNING_OBJECT_ID).' IN (?'.str_repeat(',?', count($object_ids) - 1).')';
		$res = $this->limitQuery($query, 1, null,$object_ids);
		return $res->numRows() == 1;
	}

	function get_learning_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		if (isset($type))
		{
			if ($type == 'user')
			{
				$query  = 'SELECT '.self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE.'.*, '. self :: ALIAS_LEARNING_OBJECT_TABLE .'.'. $this->escape_column_name('title') .' FROM '.$this->escape_table_name('learning_object_publication').' AS '. self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE .' JOIN '.$this->escape_table_name('learning_object').' AS '. self :: ALIAS_LEARNING_OBJECT_TABLE .' ON '. self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE .'.`learning_object` = '. self :: ALIAS_LEARNING_OBJECT_TABLE .'.`id`';
				$query .= ' WHERE '.self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE. '.'.$this->escape_column_name(LearningObjectPublication :: PROPERTY_PUBLISHER_ID).'=?';

				$order = array ();
				for ($i = 0; $i < count($order_property); $i ++)
				{
					if ($order_property[$i] == 'application')
					{
					}
					elseif($order_property[$i] == 'location')
					{
						$order[] = self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE. '.' .$this->escape_column_name(LearningObjectPublication :: PROPERTY_COURSE_ID).' '. ($order_direction[$i] == SORT_DESC ? 'DESC' : 'ASC');
						$order[] = self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE. '.' .$this->escape_column_name(LearningObjectPublication :: PROPERTY_TOOL).' '. ($order_direction[$i] == SORT_DESC ? 'DESC' : 'ASC');
					}
					elseif($order_property[$i] == 'title')
					{
						$order[] = self :: ALIAS_LEARNING_OBJECT_TABLE. '.' .$this->escape_column_name('title').' '. ($order_direction[$i] == SORT_DESC ? 'DESC' : 'ASC');
					}
					else
					{
						$order[] = self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE. '.' .$this->escape_column_name($order_property[$i], true).' '. ($order_direction[$i] == SORT_DESC ? 'DESC' : 'ASC');
						$order[] = self :: ALIAS_LEARNING_OBJECT_TABLE. '.' .$this->escape_column_name('title').' '. ($order_direction[$i] == SORT_DESC ? 'DESC' : 'ASC');
					}
				}
				if (count($order))
				{
					$query .= ' ORDER BY '.implode(', ', $order);
				}

				$statement = $this->connection->prepare($query);
				$param = api_get_user_id();
			}
		}
		else
		{
			$query = 'SELECT * FROM '.$this->escape_table_name('learning_object_publication').' WHERE '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_LEARNING_OBJECT_ID).'=?';
			$statement = $this->connection->prepare($query);
			$param = $object_id;
		}

		$res = $statement->execute($param);

		$publication_attr = array();
		while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$info = new LearningObjectPublicationAttributes();
			$info->set_id($record[LearningObjectPublication :: PROPERTY_ID]);
			$info->set_publisher_user_id($record[LearningObjectPublication :: PROPERTY_PUBLISHER_ID]);
			$info->set_publication_date($record[LearningObjectPublication :: PROPERTY_PUBLICATION_DATE]);
			$info->set_application('weblcms');
			//TODO: i8n location string
			$info->set_location($record[LearningObjectPublication :: PROPERTY_COURSE_ID].' &gt; '.$record[LearningObjectPublication :: PROPERTY_TOOL]);
			//TODO: set correct URL
			$info->set_url('index_lcms.php?tool='.$record[LearningObjectPublication :: PROPERTY_TOOL].'&amp;cidReq='.$record[LearningObjectPublication :: PROPERTY_COURSE_ID]);
			$info->set_publication_object_id($record[LearningObjectPublication :: PROPERTY_LEARNING_OBJECT_ID]);

			$publication_attr[] = $info;
		}
		return $publication_attr;
	}

	function count_publication_attributes($type = null, $condition = null)
	{
		$params = array ();
		$query = 'SELECT COUNT('.$this->escape_column_name(LearningObjectPublication :: PROPERTY_ID).') FROM '.$this->escape_table_name('learning_object_publication').' WHERE '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_PUBLISHER_ID).'=?';;

		$sth = $this->connection->prepare($query);
		$res = $sth->execute(api_get_user_id());
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		return $record[0];
	}

	function retrieve_learning_object_publications($course = null, $categories = null, $users = null, $groups = null, $condition = null, $allowDuplicates = false, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		$params = array ();
		$query = 'SELECT '.($allowDuplicates ? '' : 'DISTINCT ').'p.* FROM '.$this->escape_table_name('learning_object_publication').' AS p LEFT JOIN '.$this->escape_table_name('learning_object_publication_group').' AS pg ON p.'.$this->escape_column_name(LearningObjectPublication :: PROPERTY_ID).'=pg.'.$this->escape_column_name('publication').' LEFT JOIN '.$this->escape_table_name('learning_object_publication_user').' AS pu ON p.'.$this->escape_column_name(LearningObjectPublication :: PROPERTY_ID).'=pu.'.$this->escape_column_name('publication');
		/*
		 * Add WHERE clause (also extends $params).
		 */
		$query .= ' ' . $this->get_publication_retrieval_where_clause($course, $categories, $users, $groups, $condition, & $params);
		/*
		 * Always respect display order as a last resort.
		 */
		$orderBy[] = LearningObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX;
		$orderDir[] = SORT_ASC;
		/*
		 * Add ORDER clause.
		 */
		$query .= ' ORDER BY '.$this->escape_column_name($orderBy[0]).' '. ($orderDir[0] == SORT_ASC ? 'ASC' : 'DESC');
		for ($i = 1; $i < count($orderBy); $i ++)
		{
			$query .= ','.$this->escape_column_name($orderBy[$i]).' '. ($orderDir[$i] == SORT_ASC ? 'ASC' : 'DESC');
		}
		// XXX: Is this necessary?
		if ($maxObjects < 0)
		{
			$maxObjects = null;
		}
		/*
		 * Get publications.
		 */
		$res = $this->limitQuery($query,  intval($maxObjects),intval($offset), $params);
		return new DatabaseLearningObjectPublicationResultSet($this, $res);
	}

	function count_learning_object_publications($course = null, $categories = null, $users = null, $groups = null, $condition = null, $allowDuplicates = false)
	{
		$params = array ();
		$query = 'SELECT COUNT('.($allowDuplicates ? '*' : 'DISTINCT p.'.$this->escape_column_name(LearningObjectPublication :: PROPERTY_ID)).') FROM '.$this->escape_table_name('learning_object_publication').' AS p LEFT JOIN '.$this->escape_table_name('learning_object_publication_group').' AS pg ON p.'.$this->escape_column_name(LearningObjectPublication :: PROPERTY_ID).'=pg.'.$this->escape_column_name('publication').' LEFT JOIN '.$this->escape_table_name('learning_object_publication_user').' AS pu ON p.'.$this->escape_column_name(LearningObjectPublication :: PROPERTY_ID).'=pu.'.$this->escape_column_name('publication');
		$query .= ' ' . $this->get_publication_retrieval_where_clause($course, $categories, $users, $groups, $condition, & $params);
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		return $record[0];
	}

	private function get_publication_retrieval_where_clause ($course, $categories, $users, $groups, $condition, & $params)
	{
		$cond = array ();
		if (!is_null($course))
		{
			$cond[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_COURSE_ID, $course);
		}
		if (!is_null($categories))
		{
			if (is_array($categories))
			{
				$cc = array ();
				foreach ($categories as $cat)
				{
					$cc[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_CATEGORY_ID, $cat);
				}
				$cond[] = new OrCondition($cc);
			}
			else
			{
				$cond[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_CATEGORY_ID, $categories);
			}
		}
		$accessConditions = array ();
		// Add condition to retrieve publications for given users (user=id and group=null)
		if (!is_null($users))
		{
			if (!is_array($users))
			{
				$users = array ($users);
			}
			$userConditions = array();
			foreach ($users as $u)
			{
				$userConditions[] = new EqualityCondition('user', $u);
			}
			$accessConditions[] = new AndCondition(new EqualityCondition('group_id',null),new OrCondition($userConditions));
		}
		// Add condition to retrieve publications for given groups (user=null and group=id)
		if (!is_null($groups))
		{
			if (!is_array($groups))
			{
				$groups = array ($groups);
			}
			$groupConditions = array();
			foreach ($groups as $g)
			{
				$groupConditions[] = new EqualityCondition('group_id', $g);
			}
			$accessConditions[] = new AndCondition(new EqualityCondition('user',null),new OrCondition($groupConditions));
		}
		if(!is_null($groups) || !is_null($users))
		{
			// Add condition to retrieve publications for everybody (user=null and group=null)
			$accessConditions[] = new AndCondition(new EqualityCondition('user',null),new EqualityCondition('group_id',null));
		}

		/*
		 * Add user/group conditions to global condition.
		 */
		if (count($accessConditions))
		{
			$cond[] = new OrCondition($accessConditions);
		}
		if (!is_null($condition))
		{
			$cond[] = $condition;
		}
		$condition = new AndCondition($cond);
		$where_clause = (is_null($condition) ? '' : 'WHERE '.$this->translate_condition($condition, & $params));
		return $where_clause;
	}

	function get_next_learning_object_publication_id()
	{
		return $this->connection->nextID($this->get_table_name('learning_object_publication'));
	}

	function create_learning_object_publication($publication)
	{
		$props = array ();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_ID)] = $publication->get_id();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_LEARNING_OBJECT_ID)] = $publication->get_learning_object()->get_id();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_COURSE_ID)] = $publication->get_course_id();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_TOOL)] = $publication->get_tool();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_CATEGORY_ID)] = $publication->get_category_id();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_FROM_DATE)] = $publication->get_from_date();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_TO_DATE)] = $publication->get_to_date();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_PUBLISHER_ID)] = $publication->get_publisher_id();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_PUBLICATION_DATE)] = $publication->get_publication_date();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_HIDDEN)] = $publication->is_hidden();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX)] = $publication->get_display_order_index();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_EMAIL_SENT)] = $publication->is_email_sent();
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('learning_object_publication'), $props, MDB2_AUTOQUERY_INSERT);
		$users = $publication->get_target_users();
		foreach($users as $index => $user_id)
		{
			$props = array();
			$props[$this->escape_column_name('publication')] = $publication->get_id();
			$props[$this->escape_column_name('user')] = $user_id;
			$this->connection->extended->autoExecute($this->get_table_name('learning_object_publication_user'), $props, MDB2_AUTOQUERY_INSERT);
		}
		$groups = $publication->get_target_groups();
		foreach($groups as $index => $group_id)
		{
			$props = array();
			$props[$this->escape_column_name('publication')] = $publication->get_id();
			$props[$this->escape_column_name('group_id')] = $group_id;
			$this->connection->extended->autoExecute($this->get_table_name('learning_object_publication_group'), $props, MDB2_AUTOQUERY_INSERT);
		}
		return true;
	}

	function update_learning_object_publication($publication)
	{
		// Delete target users and groups
		$parameters['id'] = $publication->get_id();
		$query = 'DELETE FROM '.$this->escape_table_name('learning_object_publication_user').' WHERE publication = ?';
		$statement = $this->connection->prepare($query);
		$statement->execute($parameters['id']);
		$query = 'DELETE FROM '.$this->escape_table_name('learning_object_publication_group').' WHERE publication = ?';
		$statement = $this->connection->prepare($query);
		$statement->execute($parameters['id']);
		// Add updated target users and groups
		$users = $publication->get_target_users();
		$this->connection->loadModule('Extended');
		foreach($users as $index => $user_id)
		{
			$props = array();
			$props[$this->escape_column_name('publication')] = $publication->get_id();
			$props[$this->escape_column_name('user')] = $user_id;
			$this->connection->extended->autoExecute($this->get_table_name('learning_object_publication_user'), $props, MDB2_AUTOQUERY_INSERT);
		}
		$groups = $publication->get_target_groups();
		foreach($users as $index => $group_id)
		{
			$props = array();
			$props[$this->escape_column_name('publication')] = $publication->get_id();
			$props[$this->escape_column_name('group_id')] = $group_id;
			$this->connection->extended->autoExecute($this->get_table_name('learning_object_publication_group'), $props, MDB2_AUTOQUERY_INSERT);
		}
		// Update publication properties
		$where = $this->escape_column_name(LearningObjectPublication :: PROPERTY_ID).'='.$publication->get_id();
		$props = array();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_COURSE_ID)] = $publication->get_course_id();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_TOOL)] = $publication->get_tool();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_CATEGORY_ID)] = $publication->get_category_id();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_FROM_DATE)] = $publication->get_from_date();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_TO_DATE)] = $publication->get_to_date();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_PUBLISHER_ID)] = $publication->get_publisher_id();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_PUBLICATION_DATE)] = $publication->get_publication_date();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_HIDDEN)] = $publication->is_hidden();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX)] = $publication->get_display_order_index();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_EMAIL_SENT)] = $publication->is_email_sent();
		$this->connection->extended->autoExecute($this->get_table_name('learning_object_publication'), $props, MDB2_AUTOQUERY_UPDATE, $where);
		return true;
	}

	function delete_learning_object_publication($publication)
	{
		$parameters['id'] = $publication->get_id();
		$query = 'DELETE FROM '.$this->escape_table_name('learning_object_publication_user').' WHERE publication = ?';
		$statement = $this->connection->prepare($query);
		$statement->execute($publication->get_id());
		$query = 'DELETE FROM '.$this->escape_table_name('learning_object_publication_group').' WHERE publication = ?';
		$statement = $this->connection->prepare($query);
		$statement->execute($publication->get_id());
		$query = 'UPDATE '.$this->escape_table_name('learning_object_publication').' SET '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX).'='.$this->escape_column_name(LearningObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX).'-1 WHERE '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX).'>?';
		$statement = $this->connection->prepare($query);
		$statement->execute(array($publication->get_display_order_index()));
		$query = 'DELETE FROM '.$this->escape_table_name('learning_object_publication').' WHERE '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_ID).'=?';
		$this->connection->setLimit(0,1);
		$statement = $this->connection->prepare($query);
		$statement->execute($publication->get_id());
		return true;
	}

	function retrieve_learning_object_publication_categories($course, $tools)
	{
		if (!is_array($tools))
		{
			$tools = array($tools);
		}
		$query = 'SELECT * FROM '.$this->escape_table_name('learning_object_publication_category').' WHERE '.$this->escape_column_name(LearningObjectPublicationCategory :: PROPERTY_COURSE_ID).'=? AND '.$this->escape_column_name(LearningObjectPublicationCategory :: PROPERTY_TOOL).' IN ('.str_repeat('?,',count($tools)-1).'?)';
		$sth = $this->connection->prepare($query);
		$params = $tools;
		array_unshift ($params, $course);
		$res = $sth->execute($params);
		$cats = array ();
		while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$parent = $record[LearningObjectPublicationCategory :: PROPERTY_PARENT_CATEGORY_ID];
			$cat = $this->record_to_publication_category($record);
			$siblings = & $cats[$parent];
			$siblings[] = $cat;
		}
		return $this->get_publication_category_tree(0, & $cats);
	}

	function retrieve_learning_object_publication_category($id)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('learning_object_publication_category').' WHERE '.$this->escape_column_name(LearningObjectPublicationCategory :: PROPERTY_ID).'=?';
		$this->connection->setLimit(0,1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($id);
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		return $this->record_to_publication_category($record);
	}

	function get_next_learning_object_publication_category_id()
	{
		return $this->connection->nextID($this->get_table_name('learning_object_publication_category'));
	}

	function create_learning_object_publication_category($category)
	{
		$props = array();
		$props[$this->escape_column_name(LearningObjectPublicationCategory :: PROPERTY_ID)] = $category->get_id();
		$props[$this->escape_column_name(LearningObjectPublicationCategory :: PROPERTY_TITLE)] = $category->get_title();
		$props[$this->escape_column_name(LearningObjectPublicationCategory :: PROPERTY_PARENT_CATEGORY_ID)] = $category->get_parent_category_id();
		$props[$this->escape_column_name(LearningObjectPublicationCategory :: PROPERTY_COURSE_ID)] = $category->get_course();
		$props[$this->escape_column_name(LearningObjectPublicationCategory :: PROPERTY_TOOL)] = $category->get_tool();
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('learning_object_publication_category'), $props, MDB2_AUTOQUERY_INSERT);
		return true;
	}

	function update_learning_object_publication_category($category)
	{
		$where = $this->escape_column_name(LearningObjectPublicationCategory :: PROPERTY_ID).'='.$category->get_id();
		$props = array();
		$props[LearningObjectPublicationCategory :: PROPERTY_TITLE] = $category->get_title();
		$props[LearningObjectPublicationCategory :: PROPERTY_PARENT_CATEGORY_ID] = $category->get_parent_category_id();
		/*
		 * XXX: Will course and tool ever change?
		 */
		$props[$this->escape_column_name(LearningObjectPublicationCategory :: PROPERTY_COURSE_ID)] = $category->get_course();
		$props[$this->escape_column_name(LearningObjectPublicationCategory :: PROPERTY_TOOL)] = $category->get_tool();
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('learning_object_publication_category'), $props, MDB2_AUTOQUERY_UPDATE, $where);
		return true;
	}

	function delete_learning_object_publication_category($category)
	{
		// Delete subcategories in the category we delete
		$query = 'SELECT * FROM '.$this->escape_table_name('learning_object_publication_category').' WHERE '.$this->escape_column_name(LearningObjectPublicationCategory :: PROPERTY_PARENT_CATEGORY_ID).'=?';
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($category->get_id());
		while($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$this->delete_learning_object_publication_category($this->record_to_publication_category($record));
		}
		// Delete publications in the category we delete
		$publications = $this->retrieve_learning_object_publications($category->get_course(),$category->get_id())->as_array();
		foreach($publications as $index => $publication)
		{
			$publication->delete();
		}
		// Finally, delete the category itself
		$query = 'DELETE FROM '.$this->escape_table_name('learning_object_publication_category').' WHERE '.$this->escape_column_name(LearningObjectPublicationCategory :: PROPERTY_ID).'=?';
		$this->connection->setLimit(0,1);
		$statement = $this->connection->prepare($query);
		$statement->execute($category->get_id());
		return true;
	}

	function move_learning_object_publication($publication, $places)
	{
		if ($places < 0)
		{
			return $this->move_learning_object_publication_up($publication, - $places);
		}
		else
		{
			return $this->move_learning_object_publication_down($publication, $places);
		}
	}

	function log_course_module_access($course_code, $user_id,$module_name)
	{
		$params[] = time();
		$params[] = $course_code;
		$params[] = $user_id;
		$query = 'UPDATE '.$this->escape_table_name('course_module_last_access').' SET access_date = ? WHERE course_code = ? AND user_id = ? ';
		if(!is_null($module_name))
		{
			$params[] = $module_name;
			$query .= ' AND module_name = ? ';
		}
		else
		{
			$query .= ' AND module_name IS NULL';
		}
		$statement = $this->connection->prepare($query,null,MDB2_PREPARE_MANIP);
		$affectedRows = $statement->execute($params);
		if($affectedRows == 0)
		{
			$props = array ();
			$props[$this->escape_column_name('course_code')] = $course_code;
			$props[$this->escape_column_name('module_name')] = $module_name;
			$props[$this->escape_column_name('user_id')] = $user_id;
			$props[$this->escape_column_name('access_date')] = time();
			$this->connection->loadModule('Extended');
			$this->connection->extended->autoExecute($this->get_table_name('course_module_last_access'), $props, MDB2_AUTOQUERY_INSERT);
		}
	}
	function get_last_visit_date($course_code,$user_id,$module_name = null)
	{
		$params[] = $course_code;
		$params[] = $user_id;
		$query = 'SELECT * FROM '.$this->escape_table_name('course_module_last_access').' WHERE course_code = ? AND user_id = ? ';
		if(!is_null($module_name))
		{
			$params[] = $module_name;
			$query .= 'AND module_name = ? ';
		}
		$query .= ' ORDER BY access_date DESC ';
		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($params);
		if($res->numRows() == 0)
		{
			return 0;
		}
		$module = $res->fetchRow(MDB2_FETCHMODE_OBJECT);
		return $module->access_date;
	}
	function get_course_modules($course_code)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('course_module').' WHERE course_code = ?';
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($course_code);
		// If no modules are defined for this course -> insert them in database
		// @todo This is not the right place to do this, should happen upon course creation
		if($res->numRows() == 0)
		{
			$this->add_course_module($course_code,'announcement');
			$this->add_course_module($course_code,'description');
			$this->add_course_module($course_code,'calendar');
			$this->add_course_module($course_code,'document');
			$this->add_course_module($course_code,'forum');
			$this->add_course_module($course_code,'link');
			$this->add_course_module($course_code,'wiki');
			$this->add_course_module($course_code,'chat');
			$this->add_course_module($course_code,'search');
			$this->add_course_module($course_code,'user');
			$this->add_course_module($course_code,'dropbox');
			$this->add_course_module($course_code,'exercise');
			$this->add_course_module($course_code,'group');
			$this->add_course_module($course_code,'learning_path');
			$this->add_course_module($course_code,'statistics','course_admin');
			$this->add_course_module($course_code,'course_settings','course_admin');
			$this->add_course_module($course_code,'maintenance','course_admin');
			return $this->get_course_modules($course_code);
		}
		$modules = array();
		$module = null;
		while ($module = $res->fetchRow(MDB2_FETCHMODE_OBJECT)) {
		    $modules[] = $module;
		}
		return $modules;
	}

	function delete_course($course_code)
	{
		// Delete target users
		$sql = 'DELETE FROM '.$this->escape_table_name('learning_object_publication_user').'
				WHERE publication IN (
					SELECT id FROM '.$this->escape_table_name('learning_object_publication').'
					WHERE course = ?
				)';
		$statement = $this->connection->prepare($sql);
		$statement->execute($course_code);
		// Delete target groups
		$sql = 'DELETE FROM '.$this->escape_table_name('learning_object_publication_group').'
				WHERE publication IN (
					SELECT id FROM '.$this->escape_table_name('learning_object_publication').'
					WHERE course = ?
				)';
		$statement = $this->connection->prepare($sql);
		$statement->execute($course_code);
		// Delete categories
		$sql = 'DELETE FROM '.$this->escape_table_name('learning_object_publication_category').' WHERE course = ?';
		$statement = $this->connection->prepare($sql);
		$statement->execute($course_code);
		// Delete publications
		$sql = 'DELETE FROM '.$this->escape_table_name('learning_object_publication').' WHERE course = ?';
		$statement = $this->connection->prepare($sql);
		$statement->execute($course_code);
		// Delete modules
		$sql = 'DELETE FROM '.$this->escape_table_name('course_module').' WHERE course_code = ?';
		$statement = $this->connection->prepare($sql);
		$statement->execute($course_code);
		// Delete module last access
		$sql = 'DELETE FROM '.$this->escape_table_name('course_module_last_access').' WHERE course_code = ?';
		$statement = $this->connection->prepare($sql);
		$statement->execute($course_code);
		// Call Dokeos function to delete course
		CourseManager :: delete_course($course_code);
		unset($_SESSION['_course']);
		global $_course,$_cid;
		unset ($_course);
		unset ($_cid);
	}

	function set_module_visible($course_code,$module,$visible)
	{
		$query = 'UPDATE '.$this->escape_table_name('course_module').' SET visible = ? WHERE course_code = ? AND name = ?';
		$statement = $this->connection->prepare($query);
		$res = $statement->execute(array($visible,$course_code,$module));
	}

	function add_course_module($course_code,$module,$section = 'basic')
	{
		$props = array ();
		$props[$this->escape_column_name('course_code')] = $course_code;
		$props[$this->escape_column_name('name')] = $module;
		$props[$this->escape_column_name('section')] = $section;
		$props[$this->escape_column_name('visible')] = true;
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('course_module'), $props, MDB2_AUTOQUERY_INSERT);
	}

	/**
	 * Moves learning object publication up
	 * @param LearningObjectPublication $publication The publication to move
	 * @param int $places The number of places to move the publication up
	 */
 	private function move_learning_object_publication_up($publication, $places)
	{
		$oldIndex = $publication->get_display_order_index();
		$query = 'UPDATE '.$this->escape_table_name('learning_object_publication').' SET '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX).'='.$this->escape_column_name(LearningObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX).'+1 WHERE '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_COURSE_ID).'=? AND '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_TOOL).'=? AND '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_CATEGORY_ID).'=? AND '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX).'<? ORDER BY '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX).' DESC';
		$params = array($publication->get_course_id(), $publication->get_tool(), $publication->get_category_id(), $oldIndex);
		$rowsMoved = $this->limitQuery($query,$places,null,$params,true);
		$query = 'UPDATE '.$this->escape_table_name('learning_object_publication').' SET '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX).'=? WHERE '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_ID).'=?';
		$params = array($oldIndex - $places, $publication->get_id());
		$this->limitQuery($query,1,null,$params,true);
		return $rowsMoved;
	}

	/**
	 * Moves learning object publication down
	 * @param LearningObjectPublication $publication The publication to move
	 * @param int $places The number of places to move the publication down
	 */
	private function move_learning_object_publication_down($publication, $places)
	{
		$oldIndex = $publication->get_display_order_index();
		$query = 'UPDATE '.$this->escape_table_name('learning_object_publication').' SET '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX).'='.$this->escape_column_name(LearningObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX).'-1 WHERE '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_COURSE_ID).'=? AND '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_TOOL).'=? AND '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_CATEGORY_ID).'=? AND '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX).'>? ORDER BY '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX).' ASC';
		$params = array($publication->get_course_id(), $publication->get_tool(), $publication->get_category_id(), $oldIndex);
		$rowsMoved = $this->limitQuery($query,$places,null,$params,true);
		$query = 'UPDATE '.$this->escape_table_name('learning_object_publication').' SET '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX).'=? WHERE '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_ID).'=?';
		$params = array($oldIndex + $places, $publication->get_id());
		$this->limitQuery($query,1,null,$params,true);
		return $rowsMoved;
	}

	function get_next_learning_object_publication_display_order_index($course, $tool, $category)
	{
		$query = 'SELECT MAX('.$this->escape_column_name(LearningObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX).') AS h FROM '.$this->escape_table_name('learning_object_publication').' WHERE '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_COURSE_ID).'=? AND '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_TOOL).'=? AND '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_CATEGORY_ID).'=?';
		$statement = $this->connection->prepare($query);
		$res = $statement->execute(array ($course, $tool, $category));
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
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

	static function record_to_publication_category($record)
	{
		return new LearningObjectPublicationCategory($record[LearningObjectPublicationCategory :: PROPERTY_ID], $record[LearningObjectPublicationCategory :: PROPERTY_TITLE], $record[LearningObjectPublicationCategory :: PROPERTY_COURSE_ID], $record[LearningObjectPublicationCategory :: PROPERTY_TOOL], $record[LearningObjectPublicationCategory :: PROPERTY_PARENT_CATEGORY_ID]);
	}

	function record_to_publication($record)
	{
		$obj = $this->repoDM->retrieve_learning_object($record[LearningObjectPublication :: PROPERTY_LEARNING_OBJECT_ID]);
		$query = 'SELECT * FROM '.$this->escape_table_name('learning_object_publication_group').' WHERE publication = ?';
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($record[LearningObjectPublication :: PROPERTY_ID]);
		$target_groups = array();
		while($target_group = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$target_groups[] = $target_group['group_id'];
		}
		$query = 'SELECT * FROM '.$this->escape_table_name('learning_object_publication_user').' WHERE publication = ?';
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($record[LearningObjectPublication :: PROPERTY_ID]);
		$target_users = array();
		while($target_user = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$target_users[] = $target_user['user'];
		}
		return new LearningObjectPublication($record[LearningObjectPublication :: PROPERTY_ID], $obj, $record[LearningObjectPublication :: PROPERTY_COURSE_ID], $record[LearningObjectPublication :: PROPERTY_TOOL], $record[LearningObjectPublication :: PROPERTY_CATEGORY_ID], $target_users, $target_groups, $record[LearningObjectPublication :: PROPERTY_FROM_DATE], $record[LearningObjectPublication :: PROPERTY_TO_DATE], $record[LearningObjectPublication :: PROPERTY_PUBLISHER_ID], $record[LearningObjectPublication :: PROPERTY_PUBLICATION_DATE], $record[LearningObjectPublication :: PROPERTY_HIDDEN] != 0, $record[LearningObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX],$record[LearningObjectPublication :: PROPERTY_EMAIL_SENT]);
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