<?php
/**
 * $Id: database.class.php 10251 2006-11-29 15:03:20Z bmol $
 * @package application.weblcms
 * @subpackage datamanager
 */
require_once dirname(__FILE__).'/database/database_course_result_set.class.php';
require_once dirname(__FILE__).'/database/database_course_group_result_set.class.php';
require_once dirname(__FILE__).'/database/database_course_category_result_set.class.php';
require_once dirname(__FILE__).'/database/database_course_user_category_result_set.class.php';
require_once dirname(__FILE__).'/database/database_course_user_relation_result_set.class.php';
require_once dirname(__FILE__).'/database/database_learning_object_publication_result_set.class.php';
require_once dirname(__FILE__).'/../weblcms_data_manager.class.php';
require_once dirname(__FILE__).'/../learning_object_publication.class.php';
require_once dirname(__FILE__).'/../learning_object_publication_category.class.php';
require_once dirname(__FILE__).'/../learning_object_publication_feedback.class.php';
require_once dirname(__FILE__).'/../course/course.class.php';
require_once dirname(__FILE__).'/../course/course_category.class.php';
require_once dirname(__FILE__).'/../course/course_user_category.class.php';
require_once dirname(__FILE__).'/../course/course_user_relation.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/data_manager/database.class.php';
require_once Path :: get_library_path().'condition/condition_translator.class.php';
require_once dirname(__FILE__) . '/../category_manager/course_category.class.php';

class DatabaseWeblcmsDataManager extends WeblcmsDataManager
{

	const ALIAS_LEARNING_OBJECT_TABLE = 'lo';
	const ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE = 'lop';
	const ALIAS_MAX_SORT = 'max_sort';

	private $connection;
	/**
	 * The table name prefix, if any.
	 */
	private $prefix;
	
	private $database;

	function initialize()
	{
		$this->connection = Connection :: get_instance()->get_connection();
		$this->connection->setOption('debug_handler', array(get_class($this),'debug'));
		
		$this->prefix = 'weblcms_';
		$this->connection->query('SET NAMES utf8');
		
		$this->db = new Database(array('course_category' => 'cat'));
		$this->db->set_prefix('weblcms_');
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
		$query .= 'SELECT MAX('. $this->escape_column_name($column) .') as '. self :: ALIAS_MAX_SORT .' FROM'. $this->escape_table_name($table);

		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
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

	function retrieve_learning_object_publication_feedback($pid)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('learning_object_publication');
		$query .= ' WHERE '.$this->escape_table_name('learning_object_publication').'.'.$this->escape_column_name(LearningObjectPublication :: PROPERTY_PARENT_ID).'=?';
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($pid);
		while($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$publication_feedback = $this->record_to_learning_object_publication_feedback($record);
			$feedback_array[] = $publication_feedback;
		}
		return $feedback_array;
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
				$query  = 'SELECT '.self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE.'.*, '. self :: ALIAS_LEARNING_OBJECT_TABLE .'.'. $this->escape_column_name('title') .' FROM '.$this->escape_table_name('learning_object_publication').' AS '. self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE .' JOIN '.RepositoryDataManager :: get_instance()->escape_table_name('learning_object').' AS '. self :: ALIAS_LEARNING_OBJECT_TABLE .' ON '. self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE .'.`learning_object` = '. self :: ALIAS_LEARNING_OBJECT_TABLE .'.`id`';
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
				$param = $user->get_id();
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
		$res = $sth->execute($user->get_id());
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		return $record[0];
	}

	function retrieve_learning_object_publications($course = null, $categories = null, $users = null, $course_groups = null, $condition = null, $allowDuplicates = false, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1, $learning_object = null, $search_condition = null)
	{
		if(is_array($course_groups))
		{
			if(count($course_groups) == 0)
			{
				$course_groups = null;
			}
			else
			{
				$course_group_ids = array();
				foreach($course_groups as $index => $course_group)
				{
					$course_group_ids[] = $course_group->get_id();
				}
				$course_groups = $course_group_ids;
			}
		}
		$params = array ();
		$query = 'SELECT '.($allowDuplicates ? '' : 'DISTINCT ').'p.* FROM '.$this->escape_table_name('learning_object_publication').' AS p LEFT JOIN '.$this->escape_table_name('learning_object_publication_course_group').' AS pg ON p.'.$this->escape_column_name(LearningObjectPublication :: PROPERTY_ID).'=pg.'.$this->escape_column_name('publication').' LEFT JOIN '.$this->escape_table_name('learning_object_publication_user').' AS pu ON p.'.$this->escape_column_name(LearningObjectPublication :: PROPERTY_ID).'=pu.'.$this->escape_column_name('publication');
		/*
		 * Add WHERE clause (also extends $params).
		 */
		$translator = $this->get_publication_retrieval_where_clause($learning_object, $course, $categories, $users, $course_groups, $condition, $params);
		if (!is_null($translator))
		{
			$query .= $translator->render_query();
			if($search_condition)
				$query .= ' AND ';
			$params = $translator->get_parameters();
		}
		
		if($search_condition)
			$query .= 'learning_object IN (SELECT id FROM repository_learning_object WHERE ' . $search_condition . ')';
		
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

	function count_learning_object_publications($course = null, $categories = null, $users = null, $course_groups = null, $condition = null, $allowDuplicates = false, $learning_object = null)
	{
		if(is_array($course_groups))
		{
			if(count($course_groups) == 0)
			{
				$course_groups = null;
			}
			else
			{
				$course_group_ids = array();
				foreach($course_groups as $index => $course_group)
				{
					$course_group_ids[] = $course_group->get_id();
				}
				$course_groups = $course_group_ids;
			}
		}
		$params = array ();
		$query = 'SELECT COUNT('.($allowDuplicates ? '*' : 'DISTINCT p.'.$this->escape_column_name(LearningObjectPublication :: PROPERTY_ID)).') FROM '.$this->escape_table_name('learning_object_publication').' AS p LEFT JOIN '.$this->escape_table_name('learning_object_publication_course_group').' AS pg ON p.'.$this->escape_column_name(LearningObjectPublication :: PROPERTY_ID).'=pg.'.$this->escape_column_name('publication').' LEFT JOIN '.$this->escape_table_name('learning_object_publication_user').' AS pu ON p.'.$this->escape_column_name(LearningObjectPublication :: PROPERTY_ID).'=pu.'.$this->escape_column_name('publication');
		
		$translator = $this->get_publication_retrieval_where_clause($learning_object, $course, $categories, $users, $course_groups, $condition, $params);
		if (!is_null($translator))
		{
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		return $record[0];
	}

	function count_courses($condition = null)
	{
		$query = 'SELECT COUNT('.$this->escape_column_name(Course :: PROPERTY_ID).') FROM '.$this->escape_table_name('course');
		
		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		return $record[0];
	}

	function count_course_categories($condition = null)
	{
		$query = 'SELECT COUNT('.$this->escape_column_name(CourseCategory :: PROPERTY_ID).') FROM '.$this->escape_table_name('course_category');

		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}

		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		return $record[0];
	}

	function count_user_courses($condition = null)
	{
		$query = 'SELECT COUNT('.$this->escape_table_name('course').'.'.$this->escape_column_name(Course :: PROPERTY_ID).') FROM '.$this->escape_table_name('course');
		$query .= 'JOIN '.$this->escape_table_name('course_rel_user').' ON '.$this->escape_table_name('course').'.'.$this->escape_column_name(Course :: PROPERTY_ID).'='.$this->escape_table_name('course_rel_user').'.'.$this->escape_column_name('course_code');

		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}

		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		return $record[0];
	}

	function count_course_user_categories($conditions = null)
	{
		$query = 'SELECT COUNT('.$this->escape_column_name(CourseUserCategory :: PROPERTY_ID).') FROM '.$this->escape_table_name('course_user_category');

		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
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
		$query = 'SELECT COUNT('.$this->escape_column_name(CourseUserRelation :: PROPERTY_COURSE).') FROM '.$this->escape_table_name('course_rel_user');

		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}

		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		return $record[0];
	}

	private function get_publication_retrieval_where_clause ($learning_object, $course, $categories, $users, $course_groups, $condition, $params)
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
		// Add condition to retrieve publications for given users (user=id and course_group=null)
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
		// Add condition to retrieve publications for given course_groups (user=null and course_group=id)
		if (!is_null($course_groups))
		{
			if (!is_array($course_groups))
			{
				$course_groups = array ($course_groups);
			}
			$course_groupConditions = array();
			foreach ($course_groups as $g)
			{
				$course_groupConditions[] = new EqualityCondition('course_group_id', $g);
			}
			$accessConditions[] = new OrCondition($course_groupConditions);

		}
		if(!is_null($course_groups) || !is_null($users))
		{
			// Add condition to retrieve publications for everybody (user=null and course_group=null)
			$accessConditions[] = new AndCondition(new EqualityCondition('user',null),new EqualityCondition('course_group_id',null));
		}

		/*
		 * Add user/course_group conditions to global condition.
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
		
		if (!is_null($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			return $translator;
		}
		else
		{
			return null;
		}
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
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_PARENT_ID)] = $publication->get_parent_id();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_CATEGORY_ID)] = $publication->get_category_id();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_FROM_DATE)] = $publication->get_from_date();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_TO_DATE)] = $publication->get_to_date();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_PUBLISHER_ID)] = $publication->get_publisher_id();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_PUBLICATION_DATE)] = $publication->get_publication_date();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_MODIFIED_DATE)] = $publication->get_modified_date();
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
		$course_groups = $publication->get_target_course_groups();
		foreach($course_groups as $index => $course_group_id)
		{
			$props = array();
			$props[$this->escape_column_name('publication')] = $publication->get_id();
			$props[$this->escape_column_name('course_group_id')] = $course_group_id;
			$this->connection->extended->autoExecute($this->get_table_name('learning_object_publication_course_group'), $props, MDB2_AUTOQUERY_INSERT);
		}
		return true;
	}

	function update_learning_object_publication($publication)
	{
		// Delete target users and course_groups
		$parameters['id'] = $publication->get_id();
		$query = 'DELETE FROM '.$this->escape_table_name('learning_object_publication_user').' WHERE publication = ?';
		$statement = $this->connection->prepare($query);
		$statement->execute($parameters['id']);
		$query = 'DELETE FROM '.$this->escape_table_name('learning_object_publication_course_group').' WHERE publication = ?';
		$statement = $this->connection->prepare($query);
		$statement->execute($parameters['id']);
		// Add updated target users and course_groups
		$users = $publication->get_target_users();
		$this->connection->loadModule('Extended');
		foreach($users as $index => $user_id)
		{
			$props = array();
			$props[$this->escape_column_name('publication')] = $publication->get_id();
			$props[$this->escape_column_name('user')] = $user_id;
			$this->connection->extended->autoExecute($this->get_table_name('learning_object_publication_user'), $props, MDB2_AUTOQUERY_INSERT);
		}
		$course_groups = $publication->get_target_course_groups();
		foreach($course_groups as $index => $course_group_id)
		{
			$props = array();
			$props[$this->escape_column_name('publication')] = $publication->get_id();
			$props[$this->escape_column_name('course_group_id')] = $course_group_id;
			$this->connection->extended->autoExecute($this->get_table_name('learning_object_publication_course_group'), $props, MDB2_AUTOQUERY_INSERT);
		}
		// Update publication properties
		$where = $this->escape_column_name(LearningObjectPublication :: PROPERTY_ID).'='.$publication->get_id();
		$props = array();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_COURSE_ID)] = $publication->get_course_id();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_TOOL)] = $publication->get_tool();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_PARENT_ID)] = $publication->get_parent_id();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_CATEGORY_ID)] = $publication->get_category_id();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_FROM_DATE)] = $publication->get_from_date();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_TO_DATE)] = $publication->get_to_date();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_PUBLISHER_ID)] = $publication->get_publisher_id();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_PUBLICATION_DATE)] = $publication->get_publication_date();
		$props[$this->escape_column_name(LearningObjectPublication :: PROPERTY_MODIFIED_DATE)] = $publication->get_modified_date();
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
		$query = 'DELETE FROM '.$this->escape_table_name('learning_object_publication_course_group').' WHERE publication = ?';
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
			$site_name_setting = PlatformSetting :: get('site_name');
			$subject = '['.$site_name_setting->get_value().'] '.$publication->get_learning_object()->get_title();
			// TODO: SCARA - Add meaningfull publication removal message
//			$body = 'message';
//			$user = $this->userDM->retrieve_user($publication->get_publisher_id());
//			$mail = Mail :: factory($subject, $body, $user->get_email());
//			$mail->send();
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
			$siblings = $cats[$parent];
			$siblings[] = $cat;
		}
		return $this->get_publication_category_tree($root_category_id, $cats);
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
					if (substr($file, 0, 1) != '.' && $file != 'component')
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
		    $modules[$module->name] = $module;
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
	// TODO: Maybe also try to eliminate the user ?
	function retrieve_courses($user = null, $condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('course');
		if (isset($user))
		{
			$query .= ' JOIN '. $this->escape_table_name('course_rel_user') .' ON '.$this->escape_table_name('course').'.'.$this->escape_column_name(Course :: PROPERTY_ID).'='.$this->escape_table_name('course_rel_user').'.'.$this->escape_column_name('course_code');
			
			$params = array ();
			if (isset ($condition))
			{
				$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
				$translator->translate($condition);
				$query .= $translator->render_query();
				$params = $translator->get_parameters();
			}
			
			$query .= ' AND '.$this->escape_table_name('course_rel_user').'.'.$this->escape_column_name('user_id').'=?';
			//$query .= ' AND '.$this->escape_table_name('course_rel_user').'.'.$this->escape_column_name('user_course_cat').'=?';
			$query .= ' ORDER BY '. $this->escape_table_name('course_rel_user') .'.'.$this->escape_column_name(CourseUserRelation :: PROPERTY_SORT);
			$params[] = $user;
		}
		elseif(!isset($user))
		{
			$params = array ();
			if (isset ($condition))
			{
				$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
				$translator->translate($condition);
				$query .= $translator->render_query();
				$params = $translator->get_parameters();
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
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
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
			throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
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
			throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
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
	
	function create_course_all($course)
	{
		$props = array();
		foreach ($course->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$props[Course :: PROPERTY_ID] = $course->get_id();
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

	function is_course_category($category)
	{
		$query = 'SELECT COUNT('.$this->escape_column_name(CourseCategory :: PROPERTY_CODE).') FROM '.$this->escape_table_name('course_category').' WHERE '.$this->escape_column_name(CourseCategory :: PROPERTY_ID).'=?';
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($category);
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
			// TODO: New Roles & Rights system
//			$role_id = ($status == COURSEMANAGER) ? COURSE_ADMIN : NORMAL_COURSE_MEMBER;
//			$location_id = RolesRights::get_course_location_id($course->get_id());
//
//			$user_rel_props = array();
//			$user_rel_props['user_id'] = $user_id;
//			$user_rel_props['role_id'] = $role_id;
//			$user_rel_props['location_id'] = $location_id;
//
//			if ($this->connection->extended->autoExecute(Database :: get_main_table(MAIN_USER_ROLE_TABLE), $user_rel_props, MDB2_AUTOQUERY_INSERT))
//			{
				return true;
//			}
//			else
//			{
//				return false;
//			}
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
			// TODO: New Roles & Rights system
//			$location_id = RolesRights::get_course_location_id($course->get_id());
//
//			$sql = 'DELETE FROM '.Database :: get_main_table(MAIN_USER_ROLE_TABLE).' WHERE '. $this->escape_column_name('user_id') .'=? AND'. $this->escape_column_name('location_id') .'=?';
//			$statement = $this->connection->prepare($sql);
//			if ($statement->execute(array($user_id, $location_id)))
//			{
				return true;
//			}
//			else
//			{
//				return false;
//			}
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
		$props[$this->escape_column_name(CourseUserCategory :: PROPERTY_ID)] = $courseusercategory->get_id();

		$condition = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $courseusercategory->get_user());
		$sort = $this->retrieve_max_sort_value('course_user_category', CourseUserCategory :: PROPERTY_SORT, $condition);

		$props[$this->escape_column_name(CourseUserCategory :: PROPERTY_SORT)] = $sort+1;

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
	
	function get_next_course_user_category_id()
	{
		return $this->connection->nextID($this->get_table_name('course_user_category'));
	}
	
	function get_next_course_category_id()
	{
		return $this->connection->nextID($this->get_table_name('course_category'));
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
				$query = 'UPDATE '.$this->escape_table_name('course').' SET '.$this->escape_column_name(Course :: PROPERTY_CATEGORY).'="" WHERE '.$this->escape_column_name(Course :: PROPERTY_CATEGORY).'=?';
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
		// Delete target course_groups
		$sql = 'DELETE FROM '.$this->escape_table_name('learning_object_publication_course_group').'
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
		return true;
	}

	function retrieve_course_category($category = null)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('course_category');
		if (isset($category))
		{
			$query .= ' WHERE '.$this->escape_column_name(CourseCategory :: PROPERTY_ID).'=?';
			$res = $this->limitQuery($query, 1, null, array ($category));
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
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
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
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
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
			throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (CourseCategory :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		return new CourseCategory($defaultProp);
	}

	function record_to_course_user_category($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
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
		$props[$this->escape_column_name('id')] = $this->get_next_course_module_id();
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

	private function get_publication_category_tree($parent, $categories)
	{
		$subtree = array ();
		foreach ($categories[$parent] as $child)
		{
			$id = $child->get_id();
			$ar = array ();
			$ar['obj'] = $child;
			$ar['sub'] = $this->get_publication_category_tree($id, $categories);
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
		$obj = RepositoryDataManager :: get_instance()->retrieve_learning_object($record[LearningObjectPublication :: PROPERTY_LEARNING_OBJECT_ID]);
		$query = 'SELECT * FROM '.$this->escape_table_name('learning_object_publication_course_group').' WHERE publication = ?';
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($record[LearningObjectPublication :: PROPERTY_ID]);
		$target_course_groups = array();
		while($target_course_group = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$target_course_groups[] = $target_course_group['course_group_id'];
		}
		$query = 'SELECT * FROM '.$this->escape_table_name('learning_object_publication_user').' WHERE publication = ?';
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($record[LearningObjectPublication :: PROPERTY_ID]);
		$target_users = array();
		while($target_user = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$target_users[] = $target_user['user'];
		}
		return new LearningObjectPublication($record[LearningObjectPublication :: PROPERTY_ID], $obj, $record[LearningObjectPublication :: PROPERTY_COURSE_ID], $record[LearningObjectPublication :: PROPERTY_TOOL], $record[LearningObjectPublication :: PROPERTY_CATEGORY_ID], $target_users, $target_course_groups, $record[LearningObjectPublication :: PROPERTY_FROM_DATE], $record[LearningObjectPublication :: PROPERTY_TO_DATE], $record[LearningObjectPublication :: PROPERTY_PUBLISHER_ID], $record[LearningObjectPublication :: PROPERTY_PUBLICATION_DATE], $record[LearningObjectPublication :: PROPERTY_MODIFIED_DATE], $record[LearningObjectPublication :: PROPERTY_HIDDEN] != 0, $record[LearningObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX],$record[LearningObjectPublication :: PROPERTY_EMAIL_SENT]);
	}
	function record_to_course_group($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (CourseGroup :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}

		return new CourseGroup($record[CourseGroup :: PROPERTY_ID], $record[CourseGroup::PROPERTY_COURSE_CODE], $defaultProp);
	}
	// Inherited
	function delete_course_group($id)
	{
		// TODO: Delete subscription of users in this course_group
		// TODO: Delete other course_group stuff
		// Delete course_group
		$sql = 'DELETE FROM '.$this->escape_table_name('course_group').' WHERE id = ?';
		$statement = $this->connection->prepare($sql);
		$statement->execute($id);
	}
	// Inherited
	function create_course_group($course_group)
	{
		$props = array();
		$props[CourseGroup :: PROPERTY_ID] = $course_group->get_id();
		$props[CourseGroup :: PROPERTY_COURSE_CODE] = $course_group->get_course_code();
		$props[CourseGroup :: PROPERTY_NAME] = $course_group->get_name();
		$props[CourseGroup :: PROPERTY_DESCRIPTION] = $course_group->get_description();
		$props[CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS] = $course_group->get_max_number_of_members();
		$props[CourseGroup :: PROPERTY_SELF_REG] = $course_group->is_self_registration_allowed();
		$props[CourseGroup :: PROPERTY_SELF_UNREG] = $course_group->is_self_unregistration_allowed();
		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('course_group'), $props, MDB2_AUTOQUERY_INSERT))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	function get_next_course_group_id()
	{
		return $this->connection->nextID($this->get_table_name('course_group'));
	}
	// Inherited
	function update_course_group($course_group)
	{
		$where = $this->escape_column_name(CourseGroup :: PROPERTY_ID).'="'. $course_group->get_id().'"';
		$props = array();
		foreach ($course_group->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->escape_table_name('course_group'), $props, MDB2_AUTOQUERY_UPDATE, $where);
		return true;
	}
	// Inherited
	function retrieve_course_group($id)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('course_group');
		$query .= ' WHERE '.$this->escape_column_name('id').'=?';
		$params[] = $id;
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($params);
		return $this->record_to_course_group($res->fetchRow(MDB2_FETCHMODE_ASSOC));
	}
	// Inherited
	//@todo: Take parameters into account
	function retrieve_course_groups($course_code,$category = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('course_group');
		$query .= ' WHERE '.$this->escape_column_name('course_code').'=?';
		$params[] = $course_code;
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($params);
		return new DatabaseCourseGroupResultSet($this, $res);
	}
	// Inherited
	function retrieve_course_group_user_ids($course_group)
	{
		$query = 'SELECT user_id FROM '.$this->escape_table_name('course_group_rel_user');
		$query .= ' WHERE '.$this->escape_column_name('course_group_id').'=?';
		$params[] = $course_group->get_id();
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
	function retrieve_course_groups_from_user($user,$course = null)
	{
		if(!is_null($course))
		{
			$query = 'SELECT g.* FROM '. $this->escape_table_name('course_group').' g, '. $this->escape_table_name('course_group_rel_user').' u';
			$query .= ' WHERE g.id = u.course_group_id AND g.'.$this->escape_column_name('course_code').'=? AND u.user_id = ?';
			$params[] = $course->get_id();
			$params[] = $user->get_id();
		}
		else
		{
			$query = 'SELECT g.* FROM '. $this->escape_table_name('course_group').' g, '. $this->escape_table_name('course_group_rel_user').' u';
			$query .= ' WHERE g.id = u.course_group_id AND u.user_id = ?';
			$params[] = $user->get_id();
		}
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($params);
		return new DatabaseCourseGroupResultSet($this, $res);
	}
	// Inherited
	function retrieve_course_group_users($course_group,$condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		$user_ids = $this->retrieve_course_group_user_ids($course_group);
		
		$udm = UserDataManager::get_instance();
		
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
			return $udm->retrieve_users($condition , $offset , $count, $order_property, $order_direction);
		}
		else
		{
			// TODO: We need a better fix for this !
			$condition = new EqualityCondition('user_id','-1000');
			return $udm->retrieve_users($condition , $offset , $count, $order_property, $order_direction);
		}
	}
	// Inherited
	function count_course_group_users($course_group,$conditions = null)
	{
		$user_ids = $this->retrieve_course_group_user_ids($course_group);
		if(count($user_ids) > 0)
		{
			$condition = new InCondition('user_id',$user_ids);
			if(is_null($conditions))
			{
				$conditions = $condition;
			}
			else
			{
				$conditions = new AndCondition($condition,$conditions);
			}
			
			$udm = UserDataManager::get_instance();
			return $udm->count_users($conditions);
		}
		else
		{
			return 0;
		}
	}
	// Inherited
	function retrieve_possible_course_group_users($course_group,$condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		$udm = UserDataManager::get_instance();
		$query = 'SELECT user_id FROM '. $this->escape_table_name('course_rel_user') .' WHERE '.$this->escape_column_name(CourseUserRelation :: PROPERTY_COURSE).'=?';
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($course_group->get_course_code());
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
		$user_ids = $this->retrieve_course_group_user_ids($course_group);
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
	function count_possible_course_group_users($course_group,$conditions = null)
	{
		if(!is_array($conditions))
		{
			$conditions = array();
		}
		$udm = UserDataManager::get_instance();
		$query = 'SELECT user_id FROM '. $this->escape_table_name('course_rel_user') .' WHERE '.$this->escape_column_name(CourseUserRelation :: PROPERTY_COURSE).'=?';
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($course_group->get_course_code());
		while($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$course_user_ids[] = $record[User::PROPERTY_USER_ID];
		}
		$conditions[] = new InCondition(User::PROPERTY_USER_ID,$course_user_ids);
		$user_ids = $this->retrieve_course_group_user_ids($course_group);
		if(count($user_ids) > 0)
		{
			$user_condition = new NotCondition(new InCondition('user_id',$user_ids));
			$conditions[] = $user_condition;
		}
		$condition = new AndCondition($conditions);
		return $udm->count_users($condition);
	}
	// Inherited
	function subscribe_users_to_course_groups($users,$course_groups)
	{
		if(!is_array($users))
		{
			$users = array($users);
		}
		if(!is_array($course_groups))
		{
			$course_groups = array($course_groups);
		}
		foreach($users as $index => $user)
		{
			$props = array();
			$props[User :: PROPERTY_USER_ID] = $user->get_id();
			foreach($course_groups as $index => $course_group)
			{
				$props['course_group_id'] = $course_group->get_id();
				$this->connection->loadModule('Extended');
				$this->connection->extended->autoExecute($this->get_table_name('course_group_rel_user'), $props, MDB2_AUTOQUERY_INSERT);
			}
		}
	}
	// Inherited
	function unsubscribe_users_from_course_groups($users,$course_groups)
	{
		if(!is_array($users))
		{
			$users = array($users);
		}
		if(!is_array($course_groups))
		{
			$course_groups = array($course_groups);
		}
		foreach($users as $index => $user)
		{
			foreach($course_groups as $index => $course_group)
			{
				$sql = 'DELETE FROM '.$this->escape_table_name('course_group_rel_user').' WHERE course_group_id = ? AND user_id = ?';
				$statement = $this->connection->prepare($sql);
				$statement->execute(array($course_group->get_id(),$user->get_id()));
			}
		}
	}
	//Inherited
	function is_course_group_member($course_group,$user)
	{
		$sql = 'SELECT * FROM '.$this->escape_table_name('course_group_rel_user').' WHERE course_group_id = ? AND user_id = ?';
		$statement = $this->connection->prepare($sql);
		$res = $statement->execute(array($course_group->get_id(),$user->get_id()));
		return $res->numRows() > 0;
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
		if (!MDB2 :: isError($manager->createTable($name,$properties,$options)))
		{
			foreach($indexes as $index_name => $index_info)
			{
				if($index_info['type'] == 'primary')
				{
					$index_info['primary'] = 1;
					if (MDB2 :: isError($manager->createConstraint($name,$index_name,$index_info)))
					{
						return false;
					}
				}
				else
				{
					if (MDB2 :: isError($manager->createIndex($name,$index_name,$index_info)))
					{
						return false;
					}
				}
			}
			return true;
		}
		else
		{
			return false;
		}
	}

	private function get_table_name($name)
	{
		$dsn = $this->connection->getDSN('array');
		return $dsn['database'].'.'.$this->prefix.$name;
	}

	/**
	 * Escapes a table name in accordance with the database type.
	 * @param string $name The table identifier.
	 * @return string The escaped table name.
	 */
	function escape_table_name($name)
	{
		$dsn = $this->connection->getDSN('array');
		$database_name = $this->connection->quoteIdentifier($dsn['database']);
		return $database_name.'.'.$this->connection->quoteIdentifier($this->prefix.$name);
	}

	function escape_column_name($name, $prefix_learning_object_properties = false)
	{
		// Check whether the name contains a seperator, avoids notices.
		$contains_table_name = strpos($name, '.');
		if ($contains_table_name === false)
		{
			$table = $name;
			$column = null;
		}
		else
		{
			list($table, $column) = explode('.', $name, 2);
		}
		
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
		// TODO: Temporary bugfix, publication dates were recognized as LO-dates and wrongfully converted
		return false;
		//return ($name == LearningObject :: PROPERTY_CREATION_DATE || $name == LearningObject :: PROPERTY_MODIFICATION_DATE);
	}

	function record_to_learning_object_publication_feedback($record)
	{
		$obj = RepositoryDataManager :: get_instance()->retrieve_learning_object($record[LearningObjectPublication :: PROPERTY_LEARNING_OBJECT_ID]);
		$query = 'SELECT * FROM '.$this->escape_table_name('learning_object_publication_course_group').' WHERE publication = ?';
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($record[LearningObjectPublication :: PROPERTY_ID]);
		$target_course_groups = array();
		while($target_course_group = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$target_course_groups[] = $target_course_group['course_group_id'];
		}
		$query = 'SELECT * FROM '.$this->escape_table_name('learning_object_publication_user').' WHERE publication = ?';
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($record[LearningObjectPublication :: PROPERTY_ID]);
		$target_users = array();
		while($target_user = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$target_users[] = $target_user['user'];
		}

		return new LearningObjectPublicationFeedback($record[LearningObjectPublication :: PROPERTY_ID], $obj, $record[LearningObjectPublication :: PROPERTY_COURSE_ID], $record[LearningObjectPublication :: PROPERTY_TOOL], $record[LearningObjectPublication :: PROPERTY_PARENT_ID], $record[LearningObjectPublication :: PROPERTY_PUBLISHER_ID], $record[LearningObjectPublication :: PROPERTY_PUBLICATION_DATE], $record[LearningObjectPublication :: PROPERTY_HIDDEN] != 0, $record[LearningObjectPublication :: PROPERTY_EMAIL_SENT]);
	}
	
	function get_next_category_id()
	{
		return $this->db->get_next_id('course_category');
	}
	
	function get_next_course_module_id()
	{
		return $this->db->get_next_id('course_module');
	}
	
	function delete_category($category)
	{
		$condition = new EqualityCondition(CourseCategory :: PROPERTY_ID, $category->get_id());
		$succes = $this->db->delete('course_category', $condition);
		
		$query = 'UPDATE '.$this->db->escape_table_name('course_category').' SET '.
				 $this->db->escape_column_name(CourseCategory :: PROPERTY_DISPLAY_ORDER).'='.
				 $this->db->escape_column_name(CourseCategory :: PROPERTY_DISPLAY_ORDER).'-1 WHERE '.
				 $this->db->escape_column_name(CourseCategory :: PROPERTY_DISPLAY_ORDER).'>? AND ' .
				 $this->db->escape_column_name(CourseCategory :: PROPERTY_PARENT) . '=?';
		$statement = $this->db->get_connection()->prepare($query); 
		$statement->execute(array($category->get_display_order(), $category->get_parent()));
		
		return $succes;
	}
	
	function update_category($category)
	{ 
		$condition = new EqualityCondition(CourseCategory :: PROPERTY_ID, $category->get_id());
		return $this->db->update($category, $condition);
	}
	
	function create_category($category)
	{
		return $this->db->create($category);
	}
	
	function count_categories($conditions = null)
	{
		return $this->db->count_objects('course_category', $conditions);
	}
	
	function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->db->retrieve_objects('course_category', $condition, $offset, $count, $order_property, $order_direction);
	}
	
	function select_next_display_order($parent_category_id)
	{
		$query = 'SELECT MAX(' . CourseCategory :: PROPERTY_DISPLAY_ORDER . ') AS do FROM ' . 
		$this->db->escape_table_name('course_category');
	
		$condition = new EqualityCondition(CourseCategory :: PROPERTY_PARENT, $parent_category_id);
		//print_r($condition);
		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this->db, $params, $prefix_properties = false);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		
		$sth = $this->db->get_connection()->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		$res->free();
	
		return $record[0] + 1;
	}
}
?>