<?php
/**
 * $Id: database.class.php 10251 2006-11-29 15:03:20Z bmol $
 * @package application.weblcms
 * @subpackage datamanager
 */
require_once dirname(__FILE__).'/database/databasecourseresultset.class.php';
require_once dirname(__FILE__).'/database/databasegroupresultset.class.php';
require_once dirname(__FILE__).'/database/databasecoursecategoryresultset.class.php';
require_once dirname(__FILE__).'/database/databasecourseusercategoryresultset.class.php';
require_once dirname(__FILE__).'/database/databasecourseuserrelationresultset.class.php';
require_once dirname(__FILE__).'/database/databaselearningobjectpublicationresultset.class.php';
require_once dirname(__FILE__).'/../weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/../learningobjectpublication.class.php';
require_once dirname(__FILE__).'/../learningobjectpublicationcategory.class.php';
require_once dirname(__FILE__).'/../course/course.class.php';
require_once dirname(__FILE__).'/../course/coursecategory.class.php';
require_once dirname(__FILE__).'/../course/courseusercategory.class.php';
require_once dirname(__FILE__).'/../course/courseuserrelation.class.php';

class DatabaseWeblcmsDataManager extends WeblcmsDataManager
{

	const ALIAS_LEARNING_OBJECT_TABLE = 'lo';
	const ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE = 'lop';
	const ALIAS_MAX_SORT = 'max_sort';

	private $connection;
	private $repoDM;
	private $userDM;
	/**
	 * The table name prefix, if any.
	 */
	private $prefix;


	function initialize()
	{
		$this->repoDM = & RepositoryDataManager :: get_instance();
		$this->userDM = & UsersDataManager :: get_instance();
		$conf = Configuration :: get_instance();
		$this->connection = MDB2 :: connect($conf->get_parameter('database', 'connection_string_weblcms'),array('debug'=>3,'debug_handler'=>array('DatabaseWeblcmsDataManager','debug')));
		$this->prefix = $conf->get_parameter('database', 'table_name_prefix');
		$this->connection->query('SET NAMES utf8');
	}
	/**
	 * This function can be used to handle some debug info from MDB2
	 */
	function debug()
	{
		$args = func_get_args();
		// Do something with the arguments
		if($args[1] == 'query')
		{
//			echo '<pre>';
//		 	echo($args[2]);
//		 	echo '</pre>';
		}
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

	function retrieve_max_sort_value($table, $column, $condition = null)
	{
		$params = array ();
		$query .= 'SELECT MAX('. $this->escape_column_name($column) .') as '. self :: ALIAS_MAX_SORT .' FROM'. $this->escape_table_name($table);

		if (isset ($condition))
		{
			$query .= ' WHERE '.$this->translate_condition($condition, & $params, true);
		}

		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		if ($res->numRows() >= 1)
		{
			$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
			$max = $record[0];
		}
		else
		{
			$max = 0;
		}

		return $max;
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

	function get_learning_object_publication_attributes($user, $object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		if (isset($type))
		{
			if ($type == 'user')
			{
				$query  = 'SELECT '.self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE.'.*, '. self :: ALIAS_LEARNING_OBJECT_TABLE .'.'. $this->escape_column_name('title') .' FROM '.$this->escape_table_name('learning_object_publication').' AS '. self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE .' JOIN '.$this->repoDM->escape_table_name('learning_object').' AS '. self :: ALIAS_LEARNING_OBJECT_TABLE .' ON '. self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE .'.`learning_object` = '. self :: ALIAS_LEARNING_OBJECT_TABLE .'.`id`';
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
				$param = $user->get_user_id();
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
			$info->set_url('index_weblcms.php?course='. $record[LearningObjectPublication :: PROPERTY_COURSE_ID] .'&amp;tool='.$record[LearningObjectPublication :: PROPERTY_TOOL]);
			$info->set_publication_object_id($record[LearningObjectPublication :: PROPERTY_LEARNING_OBJECT_ID]);

			$publication_attr[] = $info;
		}
		return $publication_attr;
	}

	function get_learning_object_publication_attribute($publication_id)
	{

		$query = 'SELECT * FROM '.$this->escape_table_name('learning_object_publication').' WHERE '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_ID).'=?';
		$statement = $this->connection->prepare($query);
		$this->connection->setLimit(0,1);
		$res = $statement->execute($publication_id);

		$publication_attr = array();
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

		$publication_attr = new LearningObjectPublicationAttributes();
		$publication_attr->set_id($record[LearningObjectPublication :: PROPERTY_ID]);
		$publication_attr->set_publisher_user_id($record[LearningObjectPublication :: PROPERTY_PUBLISHER_ID]);
		$publication_attr->set_publication_date($record[LearningObjectPublication :: PROPERTY_PUBLICATION_DATE]);
		$publication_attr->set_application('weblcms');
		//TODO: i8n location string
		$publication_attr->set_location($record[LearningObjectPublication :: PROPERTY_COURSE_ID].' &gt; '.$record[LearningObjectPublication :: PROPERTY_TOOL]);
		//TODO: set correct URL
		$publication_attr->set_url('index_weblcms.php?tool='.$record[LearningObjectPublication :: PROPERTY_TOOL].'&amp;cidReq='.$record[LearningObjectPublication :: PROPERTY_COURSE_ID]);
		$publication_attr->set_publication_object_id($record[LearningObjectPublication :: PROPERTY_LEARNING_OBJECT_ID]);

		return $publication_attr;
	}

	function count_publication_attributes($user, $type = null, $condition = null)
	{
		$params = array ();
		$query = 'SELECT COUNT('.$this->escape_column_name(LearningObjectPublication :: PROPERTY_ID).') FROM '.$this->escape_table_name('learning_object_publication').' WHERE '.$this->escape_column_name(LearningObjectPublication :: PROPERTY_PUBLISHER_ID).'=?';;

		$sth = $this->connection->prepare($query);
		$res = $sth->execute($user->get_user_id());
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		return $record[0];
	}

	function retrieve_learning_object_publications($course = null, $categories = null, $users = null, $groups = null, $condition = null, $allowDuplicates = false, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1, $learning_object = null)
	{
		if(is_array($groups))
		{
			if(count($groups) == 0)
			{
				$groups = null;
			}
			else
			{
				$group_ids = array();
				foreach($groups as $index => $group)
				{
					$group_ids[] = $group->get_id();
				}
				$groups = $group_ids;
			}
		}
		$params = array ();
		$query = 'SELECT '.($allowDuplicates ? '' : 'DISTINCT ').'p.* FROM '.$this->escape_table_name('learning_object_publication').' AS p LEFT JOIN '.$this->escape_table_name('learning_object_publication_group').' AS pg ON p.'.$this->escape_column_name(LearningObjectPublication :: PROPERTY_ID).'=pg.'.$this->escape_column_name('publication').' LEFT JOIN '.$this->escape_table_name('learning_object_publication_user').' AS pu ON p.'.$this->escape_column_name(LearningObjectPublication :: PROPERTY_ID).'=pu.'.$this->escape_column_name('publication');
		/*
		 * Add WHERE clause (also extends $params).
		 */
		$query .= ' ' . $this->get_publication_retrieval_where_clause($learning_object, $course, $categories, $users, $groups, $condition, & $params);
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

	function count_learning_object_publications($course = null, $categories = null, $users = null, $groups = null, $condition = null, $allowDuplicates = false, $learning_object = null)
	{
		if(is_array($groups))
		{
			if(count($groups) == 0)
			{
				$groups = null;
			}
			else
			{
				$group_ids = array();
				foreach($groups as $index => $group)
				{
					$group_ids[] = $group->get_id();
				}
				$groups = $group_ids;
			}
		}
		$params = array ();
		$query = 'SELECT COUNT('.($allowDuplicates ? '*' : 'DISTINCT p.'.$this->escape_column_name(LearningObjectPublication :: PROPERTY_ID)).') FROM '.$this->escape_table_name('learning_object_publication').' AS p LEFT JOIN '.$this->escape_table_name('learning_object_publication_group').' AS pg ON p.'.$this->escape_column_name(LearningObjectPublication :: PROPERTY_ID).'=pg.'.$this->escape_column_name('publication').' LEFT JOIN '.$this->escape_table_name('learning_object_publication_user').' AS pu ON p.'.$this->escape_column_name(LearningObjectPublication :: PROPERTY_ID).'=pu.'.$this->escape_column_name('publication');
		$query .= ' ' . $this->get_publication_retrieval_where_clause($learning_object, $course, $categories, $users, $groups, $condition, & $params);
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		return $record[0];
	}

	function count_courses($condition = null)
	{
		$params = array ();
		$query = 'SELECT COUNT('.$this->escape_column_name(Course :: PROPERTY_ID).') FROM '.$this->escape_table_name('course');
		if (isset ($condition))
		{
			$query .= ' WHERE '.$this->translate_condition($condition, & $params, true);
		}
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		return $record[0];
	}

	function count_course_categories($condition = null)
	{
		$params = array ();
		$query = 'SELECT COUNT('.$this->escape_column_name(CourseCategory :: PROPERTY_ID).') FROM '.$this->escape_table_name('course_category');
		if (isset ($condition))
		{
			$query .= ' WHERE '.$this->translate_condition($condition, & $params, true);
		}
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		return $record[0];
	}

	function count_user_courses($condition = null)
	{
		$params = array ();
		$query = 'SELECT COUNT('.$this->escape_table_name('course').'.'.$this->escape_column_name(Course :: PROPERTY_ID).') FROM '.$this->escape_table_name('course');
		$query .= 'JOIN '.$this->escape_table_name('course_rel_user').' ON '.$this->escape_table_name('course').'.'.$this->escape_column_name(Course :: PROPERTY_ID).'='.$this->escape_table_name('course_rel_user').'.'.$this->escape_column_name('course_code');
		if (isset ($condition))
		{
			$query .= ' WHERE '.$this->translate_condition($condition, & $params, true);
		}

		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		return $record[0];
	}

	function count_course_user_categories($conditions = null)
	{
		$params = array ();
		$query = 'SELECT COUNT('.$this->escape_column_name(CourseUserCategory :: PROPERTY_ID).') FROM '.$this->escape_table_name('course_user_category');
		if (isset ($condition))
		{
			$query .= ' WHERE '.$this->translate_condition($condition, & $params, true);
		}
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		return $record[0];
	}

	function retrieve_course_list_of_user_as_course_admin($user_id)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('course_rel_user');
		$query .= ' WHERE '.$this->escape_table_name('course_rel_user').'.'.$this->escape_column_name(CourseUserRelation :: PROPERTY_USER).'=? AND '.$this->escape_table_name('course_rel_user').'.'.$this->escape_column_name(CourseUserRelation :: PROPERTY_STATUS).'=1';

		$statement = $this->connection->prepare($query);
		$res = $statement->execute($user_id);
		return new DatabaseCourseUserRelationResultSet($this, $res);
	}


	function count_course_user_relations($conditions = null)
	{
		$params = array ();
		$query = 'SELECT COUNT('.$this->escape_column_name(CourseUserRelation :: PROPERTY_COURSE).') FROM '.$this->escape_table_name('course_rel_user');
		if (isset ($condition))
		{
			$query .= ' WHERE '.$this->translate_condition($condition, & $params, true);
		}
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		return $record[0];
	}

	private function get_publication_retrieval_where_clause ($learning_object, $course, $categories, $users, $groups, $condition, & $params)
	{
		$cond = array ();
		if (!is_null($learning_object))
		{
			$cond[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_LEARNING_OBJECT_ID, $learning_object);
		}
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
			$accessConditions[] = new OrCondition($userConditions);

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
			$accessConditions[] = new OrCondition($groupConditions);

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
		foreach($groups as $index => $group_id)
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

	function update_learning_object_publication_id($publication_attr)
	{
		$where = $this->escape_column_name(LearningObjectPublication :: PROPERTY_ID).'='.$publication_attr->get_id();
		$props = array();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_LEARNING_OBJECT_ID)] = $publication_attr->get_publication_object_id();
		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('learning_object_publication'), $props, MDB2_AUTOQUERY_UPDATE, $where))
		{
			return true;
		}
		else
		{
			return false;
		}
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

	function delete_learning_object_publications($object_id)
	{
		$publications = $this->retrieve_learning_object_publications(null, null, null, null, null, true, array (), array (), 0, -1, $object_id);
		while ($publication = $publications->next_result())
		{
			$subject = '['.api_get_setting('siteName').'] '.$publication->get_learning_object()->get_title();
			// TODO: SCARA - Add meaningfull publication removal message
			$body = 'message';
			$user = $this->userDM->retrieve_user($publication->get_publisher_id());
			api_send_mail($user->get_email(), $subject, $body);
			$this->delete_learning_object_publication($publication);
		}
		return true;
	}

	function retrieve_learning_object_publication_categories($course, $tools,$root_category_id = 0)
	{
		if (!is_array($tools))
		{
			$tools = array($tools);
		}
		if(count($tools) > 1)
		{
			$root_category_id = 0;
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
		return $this->get_publication_category_tree($root_category_id, & $cats);
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

	function log_course_module_access($course_code, $user_id,$module_name = null,$category_id = 0)
	{
		$params[] = time();
		$params[] = $course_code;
		$params[] = $user_id;
		$params[] = $category_id;
 		$query = 'UPDATE '.$this->escape_table_name('course_module_last_access').' SET access_date = ? WHERE course_code = ? AND user_id = ? AND category_id = ? ';
		if(!is_null($module_name))
		{
			$params[] = $module_name;
			$query .= ' AND module_name = ? ';
		}
		else
		{
			$query .= ' AND module_name IS NULL ';
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
			$props[$this->escape_column_name('category_id')] = $category_id;
			$this->connection->loadModule('Extended');
			$this->connection->extended->autoExecute($this->get_table_name('course_module_last_access'), $props, MDB2_AUTOQUERY_INSERT);
		}
	}
	function get_last_visit_date($course_code,$user_id,$module_name = null,$category_id = 0)
	{
		$params[] = $course_code;
		$params[] = $user_id;
		$params[] = $category_id;
		$query = 'SELECT * FROM '.$this->escape_table_name('course_module_last_access').' WHERE course_code = ? AND user_id = ? AND category_id = ? ';
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
	function get_course_modules($course_code, $auto_added = false)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('course_module').' WHERE course_code = ?';
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($course_code);
		// If no modules are defined for this course -> insert them in database
		// @todo This is not the right place to do this, should happen upon course creation
		if($res->numRows() == 0 && !$auto_added)
		{
			$tool_dir = implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', 'tool'));
			if ($handle = opendir($tool_dir))
			{
				while (false !== ($file = readdir($handle)))
				{
					if (substr($file, 0, 1) != '.')
					{
						$file_path = $tool_dir.DIRECTORY_SEPARATOR.$file;
						if (is_dir($file_path))
						{
							// TODO: Move to an XML format for tool properties, instead of .hidden, .section and whatnot
							$visible = !file_exists($file_path.DIRECTORY_SEPARATOR.'.hidden');
							$section_file = $file_path.DIRECTORY_SEPARATOR.'.section';
							if (file_exists($section_file))
							{
								$contents = file($section_file);
								$section = rtrim($contents[0]);
							}
							else
							{
								$section = 'basic';
							}
							$this->add_course_module($course_code, $file, $section, $visible);
						}
					}
				}
				closedir($handle);
			}
			return $this->get_course_modules($course_code, true);
		}
		$modules = array();
		$module = null;
		while ($module = $res->fetchRow(MDB2_FETCHMODE_OBJECT)) {
		    $modules[] = $module;
		}
		return $modules;
	}

	function retrieve_course($course_code)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('course') .' WHERE '.$this->escape_column_name(Course :: PROPERTY_ID).'=?';
		$res = $this->limitQuery($query, 1, null, array ($course_code));
		if ($res->numRows() == 1)
		{
			$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			return $this->record_to_course($record);
		}
		else
		{
			return new Course();
		}
	}

	// TODO: Change $category from user's personal course list to condition object thus eliminating the need for another parameter
	function retrieve_courses($user = null, $category = null, $condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('course');
		if (isset($user) && isset($category))
		{
			$query .= ' JOIN '. $this->escape_table_name('course_rel_user') .' ON '.$this->escape_table_name('course').'.'.$this->escape_column_name(Course :: PROPERTY_ID).'='.$this->escape_table_name('course_rel_user').'.'.$this->escape_column_name('course_code');
			$query .= ' WHERE '.$this->escape_table_name('course_rel_user').'.'.$this->escape_column_name('user_id').'=?';
			$query .= ' AND '.$this->escape_table_name('course_rel_user').'.'.$this->escape_column_name('user_course_cat').'=?';
			$query .= ' ORDER BY '. $this->escape_table_name('course_rel_user') .'.'.$this->escape_column_name(CourseUserRelation :: PROPERTY_SORT);
			$params = array($user, $category);
		}
		elseif(!isset($user) && !isset($category))
		{
			$params = array ();
			if (isset ($condition))
			{
				$query .= ' WHERE '.$this->translate_condition($condition, & $params, true);
			}
			$orderBy[] = Course :: PROPERTY_NAME;
			$orderDir[] = SORT_ASC;
			$order = array ();

			for ($i = 0; $i < count($orderBy); $i ++)
			{
				$order[] = $this->escape_column_name($orderBy[$i], true).' '. ($orderDir[$i] == SORT_DESC ? 'DESC' : 'ASC');
			}
			if (count($order))
			{
				$query .= ' ORDER BY '.implode(', ', $order);
			}
			if ($maxObjects < 0)
			{
				$maxObjects = null;
			}
			$this->connection->setLimit(intval($maxObjects),intval($offset));
		}
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($params);
		return new DatabaseCourseResultSet($this, $res);
	}

	function retrieve_course_user_relation($course_code, $user_id)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('course_rel_user') .' WHERE '.$this->escape_column_name(CourseUserRelation :: PROPERTY_COURSE).'=? AND '.$this->escape_column_name(CourseUserRelation :: PROPERTY_USER).'=?';
		$res = $this->limitQuery($query, 1, null, array ($course_code, $user_id));
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		return $this->record_to_course_user_relation($record);
	}

	function retrieve_course_user_relations($user_id, $course_user_category)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('course_rel_user') .' WHERE '.$this->escape_column_name(CourseUserRelation :: PROPERTY_USER).'=? AND '.$this->escape_column_name(CourseUserRelation :: PROPERTY_CATEGORY).'=? ORDER BY '.$this->escape_column_name(CourseUserRelation :: PROPERTY_SORT);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute(array($user_id, $course_user_category));
		return new DatabaseCourseUserRelationResultSet($this, $res);
	}

	/**
	 * @return DatabaseCourseUserRelationResultSet
	 */
	function retrieve_course_users($course)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('course_rel_user') .' WHERE '.$this->escape_column_name(CourseUserRelation :: PROPERTY_COURSE).'=?';
		$statement = $this->connection->prepare($query);
		$res = $statement->execute(array($course->get_id()));
		return new DatabaseCourseUserRelationResultSet($this, $res);
	}

	function retrieve_course_user_relation_at_sort($user_id, $category_id, $sort, $direction)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('course_rel_user') .' WHERE '.$this->escape_column_name(CourseUserRelation :: PROPERTY_USER).'=? AND '.$this->escape_column_name(CourseUserRelation :: PROPERTY_CATEGORY).'=?';
		if ($direction == 'up')
		{
			$query .= ' AND '.$this->escape_column_name(CourseUserRelation :: PROPERTY_SORT).'<? ORDER BY '.$this->escape_column_name(CourseUserRelation :: PROPERTY_SORT) . 'DESC';
		}
		elseif ($direction == 'down')
		{
			$query .= ' AND '.$this->escape_column_name(CourseUserRelation :: PROPERTY_SORT).'>? ORDER BY '.$this->escape_column_name(CourseUserRelation :: PROPERTY_SORT) . 'ASC';
		}
		$res = $this->limitQuery($query, 1, null, array ($user_id, $category_id, $sort));
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		return $this->record_to_course_user_relation($record);
	}

	function retrieve_course_user_category_at_sort($user_id, $sort, $direction)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('course_user_category') .' WHERE '.$this->escape_column_name(CourseUserCategory :: PROPERTY_USER).'=?';
		if ($direction == 'up')
		{
			$query .= ' AND '.$this->escape_column_name(CourseUserCategory :: PROPERTY_SORT).'<? ORDER BY '.$this->escape_column_name(CourseUserCategory :: PROPERTY_SORT) . 'DESC';
		}
		elseif ($direction == 'down')
		{
			$query .= ' AND '.$this->escape_column_name(CourseUserCategory :: PROPERTY_SORT).'>? ORDER BY '.$this->escape_column_name(CourseUserCategory :: PROPERTY_SORT) . 'ASC';
		}
		$res = $this->limitQuery($query, 1, null, array ($user_id, $sort));
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		return $this->record_to_course_user_category($record);
	}

	function retrieve_user_courses($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('course');
		$query .= ' JOIN '. $this->escape_table_name('course_rel_user') .' ON '.$this->escape_table_name('course').'.'.$this->escape_column_name(Course :: PROPERTY_ID).'='.$this->escape_table_name('course_rel_user').'.'.$this->escape_column_name('course_code');

		$params = array ();
		if (isset ($condition))
		{
			$query .= ' WHERE '.$this->translate_condition($condition, & $params, true);
		}

		/*
		 * Always respect display order as a last resort.
		 */
		$orderBy[] = Course :: PROPERTY_NAME;
		$orderDir[] = SORT_ASC;
		$order = array ();

		for ($i = 0; $i < count($orderBy); $i ++)
		{
			$order[] = $this->escape_column_name($orderBy[$i], true).' '. ($orderDir[$i] == SORT_DESC ? 'DESC' : 'ASC');
		}
		if (count($order))
		{
			$query .= ' ORDER BY '.implode(', ', $order);
		}
		if ($maxObjects < 0)
		{
			$maxObjects = null;
		}
		$this->connection->setLimit(intval($maxObjects),intval($offset));
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($params);
		return new DatabaseCourseResultSet($this, $res);
	}

	function record_to_course($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(get_lang('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (Course :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}

		return new Course($record[Course :: PROPERTY_ID], $defaultProp);
	}

	function record_to_course_user_relation($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(get_lang('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (CourseUserRelation :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}

		return new CourseUserRelation($record[CourseUserRelation :: PROPERTY_COURSE], $record[CourseUserRelation :: PROPERTY_USER], $defaultProp);
	}

	function create_course($course)
	{
		$now = time();
		$props = array();
		foreach ($course->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$props[Course :: PROPERTY_ID] = $course->get_id();
		$props[Course :: PROPERTY_LAST_VISIT] = self :: to_db_date($now);
		$props[Course :: PROPERTY_LAST_EDIT] = self :: to_db_date($now);
		$props[Course :: PROPERTY_CREATION_DATE] = self :: to_db_date($now);
		$props[Course :: PROPERTY_EXPIRATION_DATE] = self :: to_db_date($now);
		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('course'), $props, MDB2_AUTOQUERY_INSERT))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function is_subscribed($course, $user_id)
	{
		$query = 'SELECT COUNT('.$this->escape_column_name(CourseUserRelation :: PROPERTY_COURSE).') FROM '.$this->escape_table_name('course_rel_user').' WHERE '.$this->escape_column_name(CourseUserRelation :: PROPERTY_USER).'=? AND '.$this->escape_column_name(CourseUserRelation :: PROPERTY_COURSE).'=?';
		$params = array($user_id, $course->get_id());
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		$res->free();
		if ($record[0] > 0)
		{
		  return true;
		}
		else
		{
		  return false;
		}
	}

	function is_course_category($category_code)
	{
		$query = 'SELECT COUNT('.$this->escape_column_name(CourseCategory :: PROPERTY_CODE).') FROM '.$this->escape_table_name('course_category').' WHERE '.$this->escape_column_name(CourseCategory :: PROPERTY_CODE).'=?';
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($category_code);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		$res->free();
		if ($record[0] = 1)
		{
		  return true;
		}
		else
		{
		  return false;
		}
	}

	function is_course($course_code)
	{
		$query = 'SELECT COUNT('.$this->escape_column_name(Course :: PROPERTY_ID).') FROM '.$this->escape_table_name('course').' WHERE '.$this->escape_column_name(Course :: PROPERTY_ID).'=?';
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($course_code);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		$res->free();
		if ($record[0] == 1)
		{
		  return true;
		}
		else
		{
		  return false;
		}
	}

	function is_course_admin($course, $user_id)
	{
		$query = 'SELECT '.$this->escape_column_name(CourseUserRelation :: PROPERTY_STATUS).' FROM '.$this->escape_table_name('course_rel_user').' WHERE '.$this->escape_column_name(CourseUserRelation :: PROPERTY_COURSE).'=? AND '.$this->escape_column_name(CourseUserRelation :: PROPERTY_USER).'=?';
		$sth = $this->connection->prepare($query);
		$res = $sth->execute(array($course->get_id(), $user_id));
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		$res->free();
		if ($record[0] == 1)
		{
		  return true;
		}
		else
		{
		  return false;
		}
	}

	function subscribe_user_to_course($course, $status, $tutor_id, $user_id)
	{
		$this->connection->loadModule('Extended');

		$conditions = array();
		$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $user_id);
		$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_CATEGORY, 0);
		$condition = new AndCondition($conditions);

		$sort = $this->retrieve_max_sort_value('course_rel_user', CourseUserRelation :: PROPERTY_SORT, $condition);

		$courseuserrelation = new CourseUserRelation($course->get_id(), $user_id);
		$courseuserrelation->set_status($status);
		$courseuserrelation->set_role(null);
		$courseuserrelation->set_tutor($tutor_id);
		$courseuserrelation->set_sort($sort+1);
		$courseuserrelation->set_category(0);

		if ($this->create_course_user_relation($courseuserrelation))
		{
			$role_id = ($status == COURSEMANAGER) ? COURSE_ADMIN : NORMAL_COURSE_MEMBER;
			$location_id = RolesRights::get_course_location_id($course->get_id());

			$user_rel_props = array();
			$user_rel_props['user_id'] = $user_id;
			$user_rel_props['role_id'] = $role_id;
			$user_rel_props['location_id'] = $location_id;

			if ($this->connection->extended->autoExecute(Database :: get_main_table(MAIN_USER_ROLE_TABLE), $user_rel_props, MDB2_AUTOQUERY_INSERT))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	function create_course_user_relation($courseuserrelation)
	{
		$props = array();
		foreach ($courseuserrelation->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}

		$props[CourseUserRelation :: PROPERTY_COURSE] = $courseuserrelation->get_course();
		$props[CourseUserRelation :: PROPERTY_USER] = $courseuserrelation->get_user();
		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('course_rel_user'), $props, MDB2_AUTOQUERY_INSERT))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function unsubscribe_user_from_course($course, $user_id)
	{
		$sql = 'DELETE FROM '.$this->escape_table_name('course_rel_user').' WHERE '. $this->escape_column_name('course_code') .'=? AND'. $this->escape_column_name('user_id') .'=?';
		$statement = $this->connection->prepare($sql);
		if ($statement->execute(array($course->get_id(), $user_id)))
		{
			$location_id = RolesRights::get_course_location_id($course->get_id());

			$sql = 'DELETE FROM '.Database :: get_main_table(MAIN_USER_ROLE_TABLE).' WHERE '. $this->escape_column_name('user_id') .'=? AND'. $this->escape_column_name('location_id') .'=?';
			$statement = $this->connection->prepare($sql);
			if ($statement->execute(array($user_id, $location_id)))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	function create_course_category($coursecategory)
	{
		$props = array();
		foreach ($coursecategory->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}

		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('course_category'), $props, MDB2_AUTOQUERY_INSERT))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function create_course_user_category($courseusercategory)
	{
		$props = array();
		foreach ($courseusercategory->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$props[CourseUserCategory :: PROPERTY_ID] = $courseusercategory->get_id();

		$condition = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $courseusercategory->get_user());
		$sort = $this->retrieve_max_sort_value('course_user_category', CourseUserCategory :: PROPERTY_SORT, $condition);

		$props[CourseUserCategory :: PROPERTY_SORT] = $sort+1;

		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('course_user_category'), $props, MDB2_AUTOQUERY_INSERT))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function delete_course_user_category($courseusercategory)
	{
		$query = 'DELETE FROM '.$this->escape_table_name('course_user_category').' WHERE '.$this->escape_column_name(CourseUserCategory :: PROPERTY_ID).'=?';
		$statement = $this->connection->prepare($query);
		if ($statement->execute($courseusercategory->get_id()))
		{
			$query = 'UPDATE '.$this->escape_table_name('course_rel_user').' SET '.$this->escape_column_name('user_course_cat').'=0 WHERE '.$this->escape_column_name('user_course_cat').'=? AND '.$this->escape_column_name('user_id').'=?';
			$statement = $this->connection->prepare($query);
			if ($statement->execute(array($courseusercategory->get_id(), $courseusercategory->get_user())))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	function delete_course_category($coursecategory)
	{
		$query = 'DELETE FROM '.$this->escape_table_name('course_category').' WHERE '.$this->escape_column_name(CourseCategory :: PROPERTY_ID).'=?';
		$statement = $this->connection->prepare($query);
		if ($statement->execute($coursecategory->get_id()))
		{
			$query = 'UPDATE '.$this->escape_table_name('course_category').' SET '.$this->escape_column_name(CourseCategory :: PROPERTY_PARENT).'="'. $coursecategory->get_parent() .'" WHERE '.$this->escape_column_name(CourseCategory :: PROPERTY_PARENT).'=?';
			$statement = $this->connection->prepare($query);
			if ($statement->execute(array($coursecategory->get_id())))
			{
				$query = 'UPDATE '.$this->escape_table_name('course').' SET '.$this->escape_column_name(Course :: PROPERTY_CATEGORY_CODE).'="" WHERE '.$this->escape_column_name(Course :: PROPERTY_CATEGORY_CODE).'=?';
				$statement = $this->connection->prepare($query);
				if ($statement->execute(array($coursecategory->get_code())))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	function update_course($course)
	{
		$where = $this->escape_column_name(Course :: PROPERTY_ID).'="'. $course->get_id().'"';
		$props = array();
		foreach ($course->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->escape_table_name('course'), $props, MDB2_AUTOQUERY_UPDATE, $where);
		return true;
	}

	function update_course_category($coursecategory)
	{
		$where = $this->escape_column_name(CourseCategory :: PROPERTY_ID).'='. $coursecategory->get_id();
		$props = array();

		foreach ($coursecategory->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->escape_table_name('course_category'), $props, MDB2_AUTOQUERY_UPDATE, $where);
		return true;
	}

	function update_course_user_category($courseusercategory)
	{
		$where = $this->escape_column_name(CourseUserCategory :: PROPERTY_ID).'="'. $courseusercategory->get_id().'"';
		$props = array();
		foreach ($courseusercategory->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->escape_table_name('course_user_category'), $props, MDB2_AUTOQUERY_UPDATE, $where);
		return true;
	}

	function update_course_user_relation($courseuserrelation)
	{
		$where = $this->escape_column_name(CourseUserRelation :: PROPERTY_COURSE).'="'. $courseuserrelation->get_course().'" AND '.$this->escape_column_name(CourseUserRelation :: PROPERTY_USER).'='. $courseuserrelation->get_user();
		$props = array();
		foreach ($courseuserrelation->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->escape_table_name('course_rel_user'), $props, MDB2_AUTOQUERY_UPDATE, $where);
		return true;
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
		// Delete subscriptions of classes in the course
		$sql = 'DELETE FROM '.$this->escape_table_name('course_rel_class').' WHERE course_code = ?';
		$statement = $this->connection->prepare($sql);
		$statement->execute($course_code);
		// Delete subscriptions of users in the course
		$sql = 'DELETE FROM '.$this->escape_table_name('course_rel_user').' WHERE course_code = ?';
		$statement = $this->connection->prepare($sql);
		$statement->execute($course_code);
		// Delete course
		$sql = 'DELETE FROM '.$this->escape_table_name('course').' WHERE code = ?';
		$statement = $this->connection->prepare($sql);
		$statement->execute($course_code);
		unset($_SESSION['_course']);
		global $_course,$_cid;
		unset ($_course);
		unset ($_cid);
		return true;
	}

	function retrieve_course_category($category_code = null)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('course_category');
		if (isset($category_code))
		{
			$query .= ' WHERE '.$this->escape_column_name(CourseCategory :: PROPERTY_CODE).'=?';
			$res = $this->limitQuery($query, 1, null, array ($category_code));
		}
		else
		{
			$res = $this->limitQuery($query, 1, null);
		}
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		return $this->record_to_course_category($record);
	}

	function retrieve_course_categories($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('course_category');
		$params = array ();
		if (isset ($condition))
		{
			$query .= ' WHERE '.$this->translate_condition($condition, & $params, true);
		}

		/*
		 * Always respect display order as a last resort.
		 */
		$orderBy[] = CourseCategory :: PROPERTY_NAME;
		$orderDir[] = SORT_ASC;
		$order = array ();

		for ($i = 0; $i < count($orderBy); $i ++)
		{
			$order[] = $this->escape_column_name($orderBy[$i], true).' '. ($orderDir[$i] == SORT_DESC ? 'DESC' : 'ASC');
		}
		if (count($order))
		{
			$query .= ' ORDER BY '.implode(', ', $order);
		}
		if ($maxObjects < 0)
		{
			$maxObjects = null;
		}
		$this->connection->setLimit(intval($maxObjects),intval($offset));
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($params);
		return new DatabaseCourseCategoryResultSet($this, $res);
	}

	function retrieve_course_user_categories ($conditions = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('course_user_category');

		$params = array ();

		if (isset ($conditions))
		{
			$query .= ' WHERE '.$this->translate_condition($conditions, & $params, true);
		}

		/*
		 * Always respect alphabetical order as a last resort.
		 */
		if (!count($orderBy))
		{
			$orderBy[] = CourseUserCategory :: PROPERTY_TITLE;
			$orderDir[] = SORT_ASC;
		}
		$order = array ();

		for ($i = 0; $i < count($orderBy); $i ++)
		{
			$order[] = $this->escape_column_name($orderBy[$i], true).' '. ($orderDir[$i] == SORT_DESC ? 'DESC' : 'ASC');
		}
		if (count($order))
		{
			$query .= ' ORDER BY '.implode(', ', $order);
		}
		if ($maxObjects < 0)
		{
			$maxObjects = null;
		}
		$this->connection->setLimit(intval($maxObjects),intval($offset));
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($params);
		return new DatabaseCourseUserCategoryResultSet($this, $res);
	}

	function retrieve_course_user_category ($course_user_category_id, $user_id = null)
	{
		$params = array();
		$query = 'SELECT * FROM '. $this->escape_table_name('course_user_category') .' WHERE '. $this->escape_column_name(CourseUserCategory :: PROPERTY_ID) . '=?';
		$params[] = $course_user_category_id;

		if ($user_id)
		{
			$query .= ' AND' . $this->escape_column_name(CourseUserCategory :: PROPERTY_USER) . '=?';
			$params[] = $user_id;
		}

		$statement = $this->connection->prepare($query);
		$res = $this->limitQuery($query, 1, null, $params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		return $this->record_to_course_user_category($record);
	}

	function record_to_course_category($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(get_lang('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (CourseCategory :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		return new CourseCategory($record[CourseCategory :: PROPERTY_ID], $defaultProp);
	}

	function record_to_course_user_category($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(get_lang('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (CourseUserCategory :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		return new CourseUserCategory($record[CourseUserCategory :: PROPERTY_ID], $defaultProp);
	}

	function set_module_visible($course_code,$module,$visible)
	{
		$query = 'UPDATE '.$this->escape_table_name('course_module').' SET visible = ? WHERE course_code = ? AND name = ?';
		$statement = $this->connection->prepare($query);
		$res = $statement->execute(array($visible,$course_code,$module));
	}

	function add_course_module($course_code,$module,$section = 'basic',$visible = true)
	{
		$props = array ();
		$props[$this->escape_column_name('course_code')] = $course_code;
		$props[$this->escape_column_name('name')] = $module;
		$props[$this->escape_column_name('section')] = $section;
		$props[$this->escape_column_name('visible')] = $visible;
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
	function record_to_group($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(get_lang('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (Group :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}

		return new Group($record[Group :: PROPERTY_ID], $record[Group::PROPERTY_COURSE_CODE], $defaultProp);
	}
	// Inherited
	function delete_group($id)
	{
		// TODO: Delete subscription of users in this group
		// TODO: Delete other group stuff
		// Delete group
		$sql = 'DELETE FROM '.$this->escape_table_name('group').' WHERE id = ?';
		$statement = $this->connection->prepare($sql);
		$statement->execute($id);
	}
	// Inherited
	function create_group($group)
	{
		$props = array();
		$props[Group :: PROPERTY_ID] = $this->get_next_group_id();
		$props[Group :: PROPERTY_COURSE_CODE] = $group->get_course_code();
		$props[Group :: PROPERTY_NAME] = $group->get_name();
		$props[Group :: PROPERTY_DESCRIPTION] = $group->get_description();
		$props[Group :: PROPERTY_MAX_NUMBER_OF_MEMBERS] = $group->get_max_number_of_members();
		$props[Group :: PROPERTY_SELF_REG] = $group->is_self_registration_allowed();
		$props[Group :: PROPERTY_SELF_UNREG] = $group->is_self_unregistration_allowed();
		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('group'), $props, MDB2_AUTOQUERY_INSERT))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	function get_next_group_id()
	{
		return $this->connection->nextID($this->get_table_name('group'));
	}
	// Inherited
	function update_group($group)
	{
		$where = $this->escape_column_name(Group :: PROPERTY_ID).'="'. $group->get_id().'"';
		$props = array();
		foreach ($group->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->escape_table_name('group'), $props, MDB2_AUTOQUERY_UPDATE, $where);
		return true;
	}
	// Inherited
	function retrieve_group($id)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('group');
		$query .= ' WHERE '.$this->escape_column_name('id').'=?';
		$params[] = $id;
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($params);
		return $this->record_to_group($res->fetchRow(MDB2_FETCHMODE_ASSOC));
	}
	// Inherited
	//@todo: Take parameters into account
	function retrieve_groups($course_code,$category = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('group');
		$query .= ' WHERE '.$this->escape_column_name('course_code').'=?';
		$params[] = $course_code;
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($params);
		return new DatabaseGroupResultSet($this, $res);
	}
	// Inherited
	function retrieve_group_user_ids($group)
	{
		$query = 'SELECT user_id FROM '.$this->escape_table_name('group_rel_user');
		$query .= ' WHERE '.$this->escape_column_name('group_id').'=?';
		$params[] = $group->get_id();
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($params);
		$user_ids = array();
		while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$user_ids[] = $record['user_id'];
		}
		return $user_ids;
	}
	// Inherited
	function retrieve_groups_from_user($user,$course = null)
	{
		if(!is_null($course))
		{
			$query = 'SELECT g.* FROM '. $this->escape_table_name('group').' g, '. $this->escape_table_name('group_rel_user').' u';
			$query .= ' WHERE g.id = u.group_id AND g.'.$this->escape_column_name('course_code').'=? AND u.user_id = ?';
			$params[] = $course->get_id();
			$params[] = $user->get_user_id();
		}
		else
		{
			$query = 'SELECT g.* FROM '. $this->escape_table_name('group').' g, '. $this->escape_table_name('group_rel_user').' u';
			$query .= ' WHERE g.id = u.group_id AND u.user_id = ?';
			$params[] = $user->get_user_id();
		}
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($params);
		return new DatabaseGroupResultSet($this, $res);
	}
	// Inherited
	function retrieve_group_users($group,$condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		$user_ids = $this->retrieve_group_user_ids($group);
		if(count($user_ids)>0)
		{
			$user_condition = new InCondition('user_id',$user_ids);
			if(is_null($condition))
			{
				$condition = $user_condition;
			}
			else
			{
				$condition = new AndCondition($condition,$user_condition);
			}
			$udm = UsersDataManager::get_instance();
			return $udm->retrieve_users($condition , $offset , $count, $order_property, $order_direction);
		}
		return null;
	}
	// Inherited
	function count_group_users($group,$conditions = null)
	{
		$user_ids = $this->retrieve_group_user_ids($group);
		if(count($user_ids) > 0)
		{
			$condition = new InCondition('user_id',$user_ids);
			if(is_null($conditions))
			{
				$conditions = array($condition);
			}
			else
			{
				$conditions = new AndCondition($condition,$conditions);
			}
			$udm = UsersDataManager::get_instance();
			return $udm->count_users($conditions);
		}
		return 0;
	}
	// Inherited
	function retrieve_possible_group_users($group,$condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		$udm = UsersDataManager::get_instance();
		$query = 'SELECT user_id FROM '. $this->escape_table_name('course_rel_user') .' WHERE '.$this->escape_column_name(CourseUserRelation :: PROPERTY_COURSE).'=?';
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($group->get_course_code());
		while($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$course_user_ids[] = $record[User::PROPERTY_USER_ID];
		}
		if(!is_null($condition))
		{
			$condition = new AndCondition($condition,new InCondition(User::PROPERTY_USER_ID,$course_user_ids));
		}
		else
		{
			$condition = new InCondition(User::PROPERTY_USER_ID,$course_user_ids);
		}
		$user_ids = $this->retrieve_group_user_ids($group);
		if(count($user_ids)>0)
		{
			$user_condition = new NotCondition(new InCondition('user_id',$user_ids));
			if(is_null($condition))
			{
				$condition = $user_condition;
			}
			else
			{
				$condition = new AndCondition($condition,$user_condition);
			}
		}
		return $udm->retrieve_users($condition , $offset , $count, $order_property, $order_direction);
	}
	// Inherited
	function count_possible_group_users($group,$conditions = null)
	{
		if(!is_array($conditions))
		{
			$conditions = array();
		}
		$udm = UsersDataManager::get_instance();
		$query = 'SELECT user_id FROM '. $this->escape_table_name('course_rel_user') .' WHERE '.$this->escape_column_name(CourseUserRelation :: PROPERTY_COURSE).'=?';
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($group->get_course_code());
		while($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$course_user_ids[] = $record[User::PROPERTY_USER_ID];
		}
		$conditions[] = new InCondition(User::PROPERTY_USER_ID,$course_user_ids);
		$user_ids = $this->retrieve_group_user_ids($group);
		if(count($user_ids) > 0)
		{
			$user_condition = new NotCondition(new InCondition('user_id',$user_ids));
			$conditions[] = $user_condition;
		}
		$condition = new AndCondition($conditions);
		return $udm->count_users($condition);
	}
	// Inherited
	function subscribe_users_to_groups($users,$groups)
	{
		if(!is_array($users))
		{
			$users = array($users);
		}
		if(!is_array($groups))
		{
			$groups = array($groups);
		}
		foreach($users as $index => $user)
		{
			$props = array();
			$props[User :: PROPERTY_USER_ID] = $user->get_user_id();
			foreach($groups as $index => $group)
			{
				$props['group_id'] = $group->get_id();
				$this->connection->loadModule('Extended');
				$this->connection->extended->autoExecute($this->get_table_name('group_rel_user'), $props, MDB2_AUTOQUERY_INSERT);
			}
		}
	}
	// Inherited
	function unsubscribe_users_from_groups($users,$groups)
	{
		if(!is_array($users))
		{
			$users = array($users);
		}
		if(!is_array($groups))
		{
			$groups = array($groups);
		}
		foreach($users as $index => $user)
		{
			foreach($groups as $index => $group)
			{
				$sql = 'DELETE FROM '.$this->escape_table_name('group_rel_user').' WHERE group_id = ? AND user_id = ?';
				$statement = $this->connection->prepare($sql);
				$statement->execute(array($group->get_id(),$user->get_user_id()));
			}
		}
	}
	//Inherited
	function is_group_member($group,$user)
	{
		$sql = 'SELECT * FROM '.$this->escape_table_name('group_rel_user').' WHERE group_id = ? AND user_id = ?';
		$statement = $this->connection->prepare($sql);
		$res = $statement->execute(array($group->get_id(),$user->get_user_id()));
		return $res->numRows() > 0;
	}
	/**
	 * Translates any type of condition to a SQL WHERE clause.
	 * @param Condition $condition The Condition object.
	 * @param array $params A reference to the query's parameter list.
	 * @param boolean $prefix_learning_object_properties Whether or not to
	 *                                                   prefix learning
	 *                                                   object properties
	 *                                                   to avoid collisions.
	 * @return string The WHERE clause.
	 */
	function translate_condition($condition, & $params, $prefix_learning_object_properties = false)
	{
		if ($condition instanceof AggregateCondition)
		{
			return $this->translate_aggregate_condition($condition, & $params, $prefix_learning_object_properties);
		}
		elseif ($condition instanceof InCondition)
		{
			return $this->translate_in_condition($condition, & $params, $prefix_learning_object_properties);
		}
		elseif ($condition instanceof Condition)
		{
			return $this->translate_simple_condition($condition, & $params, $prefix_learning_object_properties);
		}
		else
		{
			die('Need a Condition instance');
		}
	}

	/**
	 * Translates an aggregate condition to a SQL WHERE clause.
	 * @param AggregateCondition $condition The AggregateCondition object.
	 * @param array $params A reference to the query's parameter list.
	 * @param boolean $prefix_learning_object_properties Whether or not to
	 *                                                   prefix learning
	 *                                                   object properties
	 *                                                   to avoid collisions.
	 * @return string The WHERE clause.
	 */
	function translate_aggregate_condition($condition, & $params, $prefix_learning_object_properties = false)
	{
		if ($condition instanceof AndCondition)
		{
			$cond = array ();
			foreach ($condition->get_conditions() as $c)
			{
				$cond[] = $this->translate_condition($c, & $params, $prefix_learning_object_properties);
			}
			return '('.implode(' AND ', $cond).')';
		}
		elseif ($condition instanceof OrCondition)
		{
			$cond = array ();
			foreach ($condition->get_conditions() as $c)
			{
				$cond[] = $this->translate_condition($c, & $params, $prefix_learning_object_properties);
			}
			return '('.implode(' OR ', $cond).')';
		}
		elseif ($condition instanceof NotCondition)
		{
			return 'NOT ('.$this->translate_condition($condition->get_condition(), & $params, $prefix_learning_object_properties) . ')';
		}
		else
		{
			die('Cannot translate aggregate condition');
		}
	}

	/**
	 * Translates an in condition to a SQL WHERE clause.
	 * @param InCondition $condition The InCondition object.
	 * @param array $params A reference to the query's parameter list.
	 * @param boolean $prefix_learning_object_properties Whether or not to
	 *                                                   prefix learning
	 *                                                   object properties
	 *                                                   to avoid collisions.
	 * @return string The WHERE clause.
	 */
	function translate_in_condition($condition, & $params, $prefix_learning_object_properties = false)
	{
		if ($condition instanceof InCondition)
		{
			$name = $condition->get_name();
			$where_clause = $this->escape_column_name($name).' IN (';
			$values = $condition->get_values();
			$placeholders = array();
			foreach($values as $index => $value)
			{
				$placeholders[] = '?';
				$params[] = $value;
			}
			$where_clause .= implode(',',$placeholders).')';
			return $where_clause;
		}
		else
		{
			die('Cannot translate in condition');
		}
	}

	/**
	 * Translates a simple condition to a SQL WHERE clause.
	 * @param Condition $condition The Condition object.
	 * @param array $params A reference to the query's parameter list.
	 * @param boolean $prefix_learning_object_properties Whether or not to
	 *                                                   prefix learning
	 *                                                   object properties
	 *                                                   to avoid collisions.
	 * @return string The WHERE clause.
	 */
	function translate_simple_condition($condition, & $params, $prefix_learning_object_properties = false)
	{
		if ($condition instanceof EqualityCondition)
		{
			$name = $condition->get_name();
			$value = $condition->get_value();
			if (self :: is_date_column($name))
			{
				$value = self :: to_db_date($value);
			}
			if (is_null($value))
			{
				return $this->escape_column_name($name).' IS NULL';
			}
			$params[] = $value;
			return $this->escape_column_name($name, $prefix_learning_object_properties).' = ?';
		}
		elseif ($condition instanceof LikeCondition)
		{
			$name = $condition->get_name();
			$value = $condition->get_value();
			if (is_null($value))
			{
				return $this->escape_column_name($name).' IS NULL';
			}
			$params[] = $value;
			return $this->escape_column_name($name, $prefix_learning_object_properties).' LIKE ?';
		}
		elseif ($condition instanceof InequalityCondition)
		{
			$name = $condition->get_name();
			$value = $condition->get_value();
			if (self :: is_date_column($name))
			{
				$value = self :: to_db_date($value);
			}
			$params[] = $value;
			switch ($condition->get_operator())
			{
				case InequalityCondition :: GREATER_THAN :
					$operator = '>';
					break;
				case InequalityCondition :: GREATER_THAN_OR_EQUAL :
					$operator = '>=';
					break;
				case InequalityCondition :: LESS_THAN :
					$operator = '<';
					break;
				case InequalityCondition :: LESS_THAN_OR_EQUAL :
					$operator = '<=';
					break;
				default :
					die('Unknown operator for inequality condition');
			}
			return $this->escape_column_name($name, $prefix_learning_object_properties).' '.$operator.' ?';
		}
		elseif ($condition instanceof PatternMatchCondition)
		{
			$params[] = $this->translate_search_string($condition->get_pattern());
			return $this->escape_column_name($condition->get_name(), $prefix_learning_object_properties).' LIKE ?';
		}
		else
		{
			die('Cannot translate condition');
		}
	}

	function ExecuteQuery($sql)
	{
		$this->connection->query($sql);
	}

	function create_storage_unit($name,$properties,$indexes)
	{
		$name = $this->get_table_name($name);
		$this->connection->loadModule('Manager');
		$manager = $this->connection->manager;
		// If table allready exists -> drop it
		// @todo This should change: no automatic table drop but warning to user
		$tables = $manager->listTables();
		if( in_array($name,$tables))
		{
			$manager->dropTable($name);
		}
		$options['charset'] = 'utf8';
		$options['collate'] = 'utf8_unicode_ci';
		$manager->createTable($name,$properties,$options);
		foreach($indexes as $index_name => $index_info)
		{
			if($index_info['type'] == 'primary')
			{
				$index_info['primary'] = 1;
				$manager->createConstraint($name,$index_name,$index_info);
			}
			else if($index_info['type'] == 'unique')
			{
				$index_info['unique'] = 1;
				$manager->createConstraint($name,$index_name,$index_info);
			}
			else
			{
				$manager->createIndex($name,$index_name,$index_info);
			}
		}
	}

	private function get_table_name($name)
	{
		global $weblcms_database;
		return $weblcms_database.'.'.$this->prefix.$name;
	}

	/**
	 * Escapes a table name in accordance with the database type.
	 * @param string $name The table identifier.
	 * @return string The escaped table name.
	 */
	function escape_table_name($name)
	{
		global $weblcms_database;
		$database_name = $this->connection->quoteIdentifier($weblcms_database);
		return $database_name.'.'.$this->connection->quoteIdentifier($this->prefix.$name);
	}

	function escape_column_name($name, $prefix_learning_object_properties = false)
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

	private static function from_db_date($date)
	{
		return DatabaseRepositoryDataManager :: from_db_date($date);
	}

	private static function to_db_date($date)
	{
		return DatabaseRepositoryDataManager :: to_db_date($date);
	}

	/**
	 * Checks whether the given column name is the name of a column that
	 * contains a date value, and hence should be formatted as such.
	 * @param string $name The column name.
	 * @return boolean True if the column is a date column, false otherwise.
	 */
	static function is_date_column($name)
	{
		return ($name == LearningObject :: PROPERTY_CREATION_DATE || $name == LearningObject :: PROPERTY_MODIFICATION_DATE);
	}
}
?>