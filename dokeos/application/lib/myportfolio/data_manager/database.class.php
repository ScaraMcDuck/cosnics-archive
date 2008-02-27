<?php
/**
 * $Id:$
 * @package application.portfolio
 * @subpackage datamanager
 */


require_once dirname(__FILE__).'/../portfoliodatamanager.class.php';
require_once dirname(__FILE__).'/../portfoliopublication.class.php';
require_once dirname(__FILE__).'/../../../../common/configuration/configuration.class.php';
require_once dirname(__FILE__).'/../../../../common/condition/conditiontranslator.class.php';


class DatabasePortfolioDataManager extends PortfolioDataManager
{

	private $connection;
	/**
	 * The table name prefix, if any.
	 */
	private $prefix;

	private $repoDM;
	private $userDM;
	private $adminDM;

	function initialize()
	{
		$this->repoDM = RepositoryDataManager :: get_instance();
		$this->userDM = UsersDataManager :: get_instance();
		$this->adminDM = AdminDataManager :: get_instance();
		$conf = Configuration :: get_instance();
		$this->connection = MDB2 :: connect($conf->get_parameter('database', 'connection_string'),array('debug'=>3,'debug_handler'=>array('DatabasePortfolioDataManager','debug')));
		$this->prefix = 'myportfolio_';
		$this->connection->query('SET NAMES utf8');

	}
	
	function debug()
	{
		$args = func_get_args();
		// Do something with the arguments
		if($args[1] == 'query')
		{
			//echo '<pre>';
		 	//echo($args[2]);
		 	//echo '</pre>';
		}
	}

	function ExecuteQuery($sql)
	{
		$this->connection->query($sql);
	}


	private function get_table_name($name)
	{
		$dsn = $this->connection->getDSN('array');
		return $dsn['database'].'.'.$this->prefix.$name;
	}

	function escape_table_name($name)
	{
		$dsn = $this->connection->getDSN('array');
		$database_name = $this->connection->quoteIdentifier($dsn['database']);
		return $database_name.'.'.$this->connection->quoteIdentifier($this->prefix.$name);
	}

	function escape_column_name($name)
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

	private function limitQuery($query,$limit,$offset,$params,$is_manip = false)
	{
		$this->connection->setLimit($limit,$offset);
		$statement = $this->connection->prepare($query,null,($is_manip ? MDB2_PREPARE_MANIP : null));
		$res = $statement->execute($params);
		return $res;
	}

	function get_root_element($user)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('tree_relation').' WHERE '. $this->escape_column_name('treeitem').' ="-1" AND '.$this->escape_column_name('userid').'=?';
		$res = $this->limitQuery($query, 1,null, array ($user->get_user_id()));
		//$res = $this->limitQuery($query, 1,null, array ($user));
		if($res->numRows() == 1)
		{
			$result=$res->fetchRow(MDB2_FETCHMODE_ASSOC);
			return $result['child'];
		}
		else
		{
			$props = array ();
			$props['userid']=$user->get_user_id();
			//$props['userid']=$user;
			$props['title']=Translation :: get_lang("my_portfolio");
			$this->connection->loadModule('Extended');
			$this->connection->extended->autoExecute($this->get_table_name('treeitem'), $props, MDB2_AUTOQUERY_INSERT);
			$query = 'SELECT * FROM '.$this->escape_table_name('treeitem').' WHERE '. $this->escape_column_name('title').' ="'.Translation :: get_lang("my_portfolio").'" AND '.$this->escape_column_name('userid').'=?';
			$res = $this->limitQuery($query, 1,null, array ($user->get_user_id()));
			//$res = $this->limitQuery($query, 1,null, array ($user));
			$result=$res->fetchRow(MDB2_FETCHMODE_ASSOC);
			$props = array ();
			$props['userid']=$user->get_user_id();
			//$props['userid']=$user;
			$props['treeitem']=-1;
			$props['child']=$result['treeitem'];
			$props['display_order']=1;
			$this->connection->extended->autoExecute($this->get_table_name('tree_relation'), $props, MDB2_AUTOQUERY_INSERT);
			return $result['treeitem'];
		}
	}

	function get_item_title($item)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('treeitem').' WHERE '.$this->escape_column_name('treeitem').'=?';
		$res = $this->limitQuery($query, 1,null, array ($item));
		if($res->numRows() == 1)
		{
			$result=$res->fetchRow(MDB2_FETCHMODE_ASSOC);
			return $result['title'];
		}
	}

	function get_owner($item)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('treeitem').' WHERE '.$this->escape_column_name('treeitem').'=?';
		$res = $this->limitQuery($query, 1,null, array ($item));
		if($res->numRows() == 1)
		{
			$result=$res->fetchRow(MDB2_FETCHMODE_ASSOC);
			return $result['userid'];
		}
	}

	function get_item_children($item)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('tree_relation').' WHERE '.$this->escape_column_name('treeitem').'=?';
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($item);
		$children = array();
		$i=0;
		$nrchild=$res->numRows();

		while($i<$nrchild)
		{
			$result=$res->fetchRow(MDB2_FETCHMODE_ASSOC);
			$children[]=$result['child'];
			$i++;
		}

		return $children;

	}

	function create_page($title,$user)
	{
		$props = array ();
		$props['userid']=$user;
		$props['title']=$title;
		$this->connection->loadModule('Extended');			$this->connection->extended->autoExecute($this->get_table_name('treeitem'), $props, MDB2_AUTOQUERY_INSERT);
		$query = 'SELECT * FROM '.$this->escape_table_name('treeitem').' WHERE '. $this->escape_column_name('title').' ="'.$title.'" AND '.$this->escape_column_name('userid').'=?';
		$res = $this->limitQuery($query, 1,null, array ($user));
		$result=$res->fetchRow(MDB2_FETCHMODE_ASSOC);
		return $result['treeitem'];
	}

	function connect_parent_to_child($parent,$child,$user)
	{
		$props = array ();
		$props['userid']=$user;
		$props['treeitem']=$parent;
		$props['child']=$child;
		//display_order order moet max+1 worden;
		$props['display_order']=1;
		$this->connection->loadModule('Extended');			$this->connection->extended->autoExecute($this->get_table_name('tree_relation'), $props, MDB2_AUTOQUERY_INSERT);
	}

	function get_parent($item)
	{
		$query = 'SELECT treeitem FROM '.$this->escape_table_name('tree_relation').' WHERE '. $this->escape_column_name('child').' =?';
		$res = $this->limitQuery($query, 1,null, array ($item));
		$result=$res->fetchRow(MDB2_FETCHMODE_ASSOC);
		return $result['treeitem'];
	}

	function set_parent($item, $new_parent)
	{
		$query = 'UPDATE '.$this->escape_table_name('tree_relation').' SET '. $this->escape_column_name('treeitem').' ="'.$new_parent.'" WHERE '.$this->escape_column_name('child').' ='.$item;
		$this->ExecuteQuery($query);
	}

	function remove_item($item)
	{
		$query = 'DELETE FROM '.$this->escape_table_name('tree_relation').' WHERE '.$this->escape_column_name('child').' ='.$item;
		$this->ExecuteQuery($query);
		$query = 'DELETE FROM '.$this->escape_table_name('treeitem').' WHERE '.$this->escape_column_name('treeitem').' ='.$item;
		$this->ExecuteQuery($query);
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

	//Inherited.
	function get_next_portfolio_publication_id()
	{
		return $this->connection->nextID($this->get_table_name('publication'));
	}

		//Inherited.
	function get_next_tree_item_id()
	{
		return $this->connection->nextID($this->get_table_name('treeitem'));
	}

	//Inherited.
    function count_portfolio_publications($condition = null)
    {
		$query = 'SELECT COUNT('.$this->escape_column_name(PortfolioPublication :: PROPERTY_ID).') FROM '.$this->escape_table_name('publication');
		$query .= 'JOIN '.$this->userDM->escape_table_name('user') . 'ON' . $this->escape_table_name('publication'). '.' . $this->escape_column_name(PortfolioPublication :: PROPERTY_PUBLISHER) .'='. $this->userDM->escape_table_name('user') .'.'. $this->userDM->escape_column_name('user_id');

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
		$res->free();
		return $record[0];
    }

    //Inherited
    function retrieve_portfolio_publication($id)
	{
		echo "moop";
		$query = 'SELECT * FROM '.$this->escape_table_name('publication');
		$query .= ' JOIN '.$this->userDM->escape_table_name('user') . ' ON ' . $this->escape_table_name('publication'). '.' . $this->escape_column_name(PortfolioPublication :: PROPERTY_PUBLISHER) .'='. $this->userDM->escape_table_name('user') .'.'. $this->userDM->escape_column_name('user_id');
		$query .= ' WHERE '.$this->escape_column_name(PortfolioPublication :: PROPERTY_ID).'=?';
		echo $query;

		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($id);
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$res->free();
		return self :: record_to_portfolio_publication($record);
	}


    function retrieve_portfolio_publication_from_item($item)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('publication');
		$query .= ' JOIN '.$this->userDM->escape_table_name('user') . ' ON ' . $this->escape_table_name('publication'). '.' . $this->escape_column_name(PortfolioPublication :: PROPERTY_PUBLISHER) .'='. $this->userDM->escape_table_name('user') .'.'. $this->userDM->escape_column_name('user_id');
		$query .= ' WHERE '.$this->escape_column_name(PortfolioPublication :: PROPERTY_TREEITEM).'=?';

		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($item);
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$res->free();
		return self :: record_to_portfolio_publication($record);
	}

    //Inherited.
    function retrieve_portfolio_publications($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{

		$query = 'SELECT * FROM '.$this->escape_table_name('publication');
		$query .= 'JOIN '.$this->userDM->escape_table_name('user') . ' ON ' . $this->escape_table_name('publication'). '.' . $this->escape_column_name(PortfolioPublication :: PROPERTY_PUBLISHER) .'='. $this->userDM->escape_table_name('user') .'.'. $this->userDM->escape_column_name('user_id');

		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
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
		return new DatabasePortfolioPublicationResultSet($this, $res);
	}

	//Inherited.
	function record_to_portfolio_publication($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(Translation :: get_lang('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (PortfolioPublication :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		return new PortfolioPublication($record[PortfolioPublication :: PROPERTY_ID], $defaultProp);
	}

	//Inherited.
	function update_portfolio_publication($portfolio_publication)
	{
		$where = $this->escape_column_name(PortfolioPublication :: PROPERTY_ID).'='.$portfolio_publication->get_id();
		$props = array();
		foreach ($portfolio_publication->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('publication'), $props, MDB2_AUTOQUERY_UPDATE, $where);
		return true;
	}

	//Inherited
	function delete_portfolio_publication($portfolio_publication)
	{
		$query = 'DELETE FROM '.$this->escape_table_name('publication').' WHERE '.$this->escape_column_name(PortfolioPublication :: PROPERTY_ID).'=?';
		$statement = $this->connection->prepare($query);
		if ($statement->execute($portfolio_publication->get_id()))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	//Inherited.
	function delete_portfolio_publications($object_id)
	{
		$condition = new EqualityCondition(PortfolioPublication :: PROPERTY_PROFILE, $object_id);
		$publications = $this->retrieve_portfolio_publications($condition, null, null, null, null, true, array (), array (), 0, -1, $object_id);
		while ($publication = $publications->next_result())
		{
//			$subject = '['.$this->adminDM->retrieve_setting_from_variable_name('site_name')->get_value().'] '.$publication->get_learning_object()->get_title();
//			// TODO: SCARA - Add meaningfull publication removal message
//			$body = 'message';
//			$user = $this->userDM->retrieve_user($publication->get_publisher_id());
//			api_send_mail($user->get_email(), $subject, $body);
			$this->delete_portfolio_publication($publication);
		}
		return true;
	}


	//Inherited.
	function update_portfolio_publication_id($publication_attr)
	{
		$where = $this->escape_column_name(PortfolioPublication :: PROPERTY_ID).'='.$publication_attr->get_id();
		$props = array();
		$props[$this->escape_column_name(PortfolioPublication :: PROPERTY_PROFILE)] = $publication_attr->get_publication_object_id();
		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('publication'), $props, MDB2_AUTOQUERY_UPDATE, $where))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	//Inherited.
	static function is_date_column($name)
	{
		return ($name == PortfolioPublication :: PROPERTY_PUBLISHED);
	}

	//Inherited.
	function any_learning_object_is_published($object_ids)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('publication').' WHERE '.$this->escape_column_name(PortfolioPublication :: PROPERTY_PROFILE).' IN (?'.str_repeat(',?', count($object_ids) - 1).')';
		$res = $this->limitQuery($query, 1, null,$object_ids);
		return $res->numRows() == 1;
	}

	//Inherited.
	function learning_object_is_published($object_id)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('publication').' WHERE '.$this->escape_column_name(PortfolioPublication :: PROPERTY_PROFILE).'=?';
		$res = $this->limitQuery($query, 1,null, array ($object_id));
		return $res->numRows() == 1;
	}


	//Inherited
	function get_learning_object_publication_attributes($user, $object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		if (isset($type))
		{
			if ($type == 'user')
			{
				$query  = 'SELECT '.self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE.'.*, '. self :: ALIAS_LEARNING_OBJECT_TABLE .'.'. $this->escape_column_name('title') .' FROM '.$this->escape_table_name('publication').' AS '. self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE .' JOIN '.$this->repoDM->escape_table_name('learning_object').' AS '. self :: ALIAS_LEARNING_OBJECT_TABLE .' ON '. self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE .'.`profile` = '. self :: ALIAS_LEARNING_OBJECT_TABLE .'.`id`';
				$query .= ' WHERE '.self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE. '.'.$this->escape_column_name(PortfolioPublication :: PROPERTY_PUBLISHER).'=?';

				$order = array ();
				for ($i = 0; $i < count($order_property); $i ++)
				{
					if ($order_property[$i] == 'application' || $order_property[$i] == 'location')
					{
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
			$query = 'SELECT * FROM '.$this->escape_table_name('publication').' WHERE '.$this->escape_column_name(PortfolioPublication :: PROPERTY_PROFILE).'=?';
			$statement = $this->connection->prepare($query);
			$param = $object_id;
		}

		$res = $statement->execute($param);

		$publication_attr = array();
		while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$publication = $this->record_to_portfolio_publication($record);

			$info = new LearningObjectPublicationAttributes();
			$info->set_id($publication->get_id());
			$info->set_publisher_user_id($publication->get_publisher());
			$info->set_publication_date($publication->get_published());
			$info->set_application('Portfolio');
			//TODO: i8n location string
			$info->set_location(Translation :: get_lang('List'));
			$info->set_url('index_myportfolio.php?go=view&portfolio='.$publication->get_id());
			$info->set_publication_object_id($publication->get_portfolio());

			$publication_attr[] = $info;
		}
		return $publication_attr;
	}

	//Indered.
	function get_learning_object_publication_attribute($publication_id)
	{

		$query = 'SELECT * FROM '.$this->escape_table_name('publication').' WHERE '.$this->escape_column_name(PortfolioPublication :: PROPERTY_ID).'=?';
		$statement = $this->connection->prepare($query);
		$this->connection->setLimit(0,1);
		$res = $statement->execute($publication_id);

		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

		$publication = $this->record_to_portfolio_publication($record);

		$info = new LearningObjectPublicationAttributes();
		$info->set_id($publication->get_id());
		$info->set_publisher_user_id($publication->get_publisher());
		$info->set_publication_date($publication->get_published());
		$info->set_application('Portfolio');
		//TODO: i8n location string
		$info->set_location(Translation :: get_lang('List'));
		$info->set_url('index_myportfolio.php?go=view&portfolio='.$publication->get_id());
		$info->set_publication_object_id($publication->get_portfolio());

		return $info;
	}

	//Inherited.
	function count_publication_attributes($user, $type = null, $condition = null)
	{
		$params = array ();
		$query = 'SELECT COUNT('.$this->escape_column_name(PortfolioPublication :: PROPERTY_ID).') FROM '.$this->escape_table_name('publication').' WHERE '.$this->escape_column_name(PortfolioPublication :: PROPERTY_PUBLISHER).'=?';;

		$sth = $this->connection->prepare($query);
		$res = $sth->execute($user->get_user_id());
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		return $record[0];
	}

	//Inherited.
	function create_portfolio_publication($publication)
	{
		$props = array();
		foreach ($publication->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$props[$this->escape_column_name(PortfolioPublication :: PROPERTY_ID)] = $publication->get_id();

		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('publication'), $props, MDB2_AUTOQUERY_INSERT))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
?>