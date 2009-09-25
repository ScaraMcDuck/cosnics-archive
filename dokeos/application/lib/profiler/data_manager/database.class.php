<?php
/**
 * @package application.lib.profiler.data_manager.database
 */
require_once Path :: get_application_path() . 'lib/profiler/profiler_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/profiler/profile_publication.class.php';
require_once Path :: get_library_path() . 'condition/condition_translator.class.php';
require_once Path :: get_repository_path() . 'lib/data_manager/database.class.php';
require_once 'MDB2.php';

class DatabaseProfilerDataManager extends ProfilerDataManager
{

    private $prefix;
    private $userDM;
    private $database;

    const ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE = 'pmb';
    const ALIAS_LEARNING_OBJECT_TABLE = 'lo';

    function initialize()
    {
        $aliases = array();
        $aliases['user'] = 'u';
        $aliases['category'] = 'cat';

        $this->database = new Database($aliases);
        $this->database->set_prefix('profiler_');
    }

    function get_database()
    {
        return $this->database;
    }

    //Inherited.
    function get_next_profile_publication_id()
    {
        return $this->database->get_next_id(ProfilePublication :: get_table_name());
    }

    //Inherited.
    function count_profile_publications($condition = null)
    {
        return $this->database->count_objects(ProfilePublication :: get_table_name(), $condition);
    }

    //Inherited
    function retrieve_profile_publication($id)
    {
        $condition = new EqualityCondition(ProfilePublication :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(ProfilePublication :: get_table_name(), $condition, array(), array(), ProfilePublication :: CLASS_NAME);
    }

    //Inherited.
    function retrieve_profile_publications($condition = null, $order_by = array (), $order_dir = array (), $offset = 0, $max_objects = -1)
    {
        $udm = UserDataManager :: get_instance();
        $publication_alias = $this->database->get_alias(ProfilePublication :: get_table_name());

        $query  = 'SELECT * FROM ' . $this->database->escape_table_name(ProfilePublication :: get_table_name()) . ' AS ' . $publication_alias;
        $query .= ' JOIN ' . $udm->get_database()->escape_table_name(User :: get_table_name()) . ' AS ' . $this->database->get_alias(User :: get_table_name());
        $query .= ' ON ' . $this->database->escape_column_name(ProfilePublication :: PROPERTY_PUBLISHER, $publication_alias) . ' = ';
        $query .= $udm->get_database()->escape_column_name(User :: PROPERTY_USER_ID, $this->database->get_alias(User :: get_table_name()));

        $params = array();
        if (isset($condition))
        {
            $translator = new ConditionTranslator($this->database, $params, $publication_alias);
            $query .= $translator->render_query($condition);
            $params = $translator->get_parameters();
        }

        $orders = array();

        foreach($order_by as $order)
        {
            $orders[] = $this->database->escape_column_name($order->get_property(), ($order->alias_is_set() ? $order->get_alias() : $publication_alias)) . ' ' . ($order->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
        }
        if (count($orders))
        {
            $query .= ' ORDER BY ' . implode(', ', $orders);
        }
        if ($max_objects < 0)
        {
            $max_objects = null;
        }

        $this->database->get_connection()->setLimit(intval($max_objects), intval($offset));
        $statement = $this->database->get_connection()->prepare($query);
        $res = $statement->execute($params);

        return new ObjectResultSet($this->database, $res, ProfilePublication :: CLASS_NAME);
    }

    //Inherited.
    function update_profile_publication($profile_publication)
    {
        $condition = new EqualityCondition(ProfilePublication :: PROPERTY_ID, $profile_publication->get_id());
        return $this->database->update($profile_publication, $condition);
    }

    //Inherited
    function delete_profile_publication($profile_publication)
    {
        $condition = new EqualityCondition(ProfilePublication :: PROPERTY_ID, $profile_publication->get_id());
        return $this->database->delete(ProfilePublication :: get_table_name(), $condition);
    }

    //Inherited.
    function delete_profile_publications($object_id)
    {
        $condition = new EqualityCondition(ProfilePublication :: PROPERTY_PROFILE, $object_id);
        return $this->database->delete_objects(ProfilePublication :: get_table_name(), $condition);
    }

    //Inherited.
    function update_profile_publication_id($publication_attr)
    {
        $where = $this->database->escape_column_name(ProfilePublication :: PROPERTY_ID) . '=' . $publication_attr->get_id();
        $props = array();
        $props[$this->database->escape_column_name(ProfilePublication :: PROPERTY_PROFILE)] = $publication_attr->get_publication_object_id();
        $this->database->get_connection()->loadModule('Extended');
        if ($this->database->get_connection()->extended->autoExecute($this->database->get_table_name(ProfilePublication :: get_table_name()), $props, MDB2_AUTOQUERY_UPDATE, $where))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    //Inherited.
    function any_learning_object_is_published($object_ids)
    {
        $condition = new InCondition(ProfilePublication :: PROPERTY_PROFILE, $object_ids);
        return $this->database->count_objects(ProfilePublication :: get_table_name(), $condition) >= 1;
    }

    //Inherited.
    function learning_object_is_published($object_id)
    {
        $condition = new EqualityCondition(ProfilePublication :: PROPERTY_PROFILE, $object_id);
        return $this->database->count_objects(ProfilePublication :: get_table_name(), $condition) >= 1;
    }

    // Inherited.
    function create_storage_unit($name, $properties, $indexes)
    {
        return $this->database->create_storage_unit($name, $properties, $indexes);
    }

    //Inherited
    function get_learning_object_publication_attributes($user, $object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
    {
        if (isset($type))
        {
            if ($type == 'user')
            {
                $query = 'SELECT ' . self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE . '.*, ' . self :: ALIAS_LEARNING_OBJECT_TABLE . '.' . $this->database->escape_column_name('title') . ' FROM ' . $this->database->escape_table_name('publication') . ' AS ' . self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE . ' JOIN ' . RepositoryDataManager :: get_instance()->escape_table_name('learning_object') . ' AS ' . self :: ALIAS_LEARNING_OBJECT_TABLE . ' ON ' . self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE . '.`profile_id` = ' . self :: ALIAS_LEARNING_OBJECT_TABLE . '.`id`';
                $query .= ' WHERE ' . self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE . '.' . $this->database->escape_column_name(ProfilePublication :: PROPERTY_PUBLISHER) . '=?';

                $order = array();
                for($i = 0; $i < count($order_property); $i ++)
                {
                 	if ($order_property[$i] == 'application')
                    {
                    }
                    elseif ($order_property[$i] == 'location')
                    {
                        $order[] = self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE . '.' . $this->database->escape_column_name(LearningObjectPublication :: PROPERTY_COURSE_ID) . ' ' . ($order_direction[$i] == SORT_DESC ? 'DESC' : 'ASC');
                        $order[] = self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE . '.' . $this->database->escape_column_name(LearningObjectPublication :: PROPERTY_TOOL) . ' ' . ($order_direction[$i] == SORT_DESC ? 'DESC' : 'ASC');
                    }
                    elseif ($order_property[$i] == 'title')
                    {
                        $order[] = self :: ALIAS_LEARNING_OBJECT_TABLE . '.' . $this->database->escape_column_name('title') . ' ' . ($order_direction[$i] == SORT_DESC ? 'DESC' : 'ASC');
                    }
                    else
                    {
                        //$order[] = self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE . '.' . $this->database->escape_column_name($order_property[$i], true) . ' ' . ($order_direction[$i] == SORT_DESC ? 'DESC' : 'ASC');
                        $order[] = self :: ALIAS_LEARNING_OBJECT_TABLE . '.' . $this->database->escape_column_name('title') . ' ' . ($order_direction[$i] == SORT_DESC ? 'DESC' : 'ASC');
                    }
                }
                if (count($order))
                {
                    $query .= ' ORDER BY ' . implode(', ', $order);
                }

                $statement = $this->database->get_connection()->prepare($query);
                $param = $user->get_id();
            }
        }
        else
        {
            $query = 'SELECT * FROM ' . $this->database->escape_table_name('publication') . ' WHERE ' . $this->database->escape_column_name(ProfilePublication :: PROPERTY_PROFILE) . '=?';
            $statement = $this->database->get_connection()->prepare($query);
            $param = $object_id;
        }

        $res = $statement->execute($param);

        $publication_attr = array();
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $publication = $this->database->record_to_class_object($record, ProfilePublication :: CLASS_NAME);

            $info = new LearningObjectPublicationAttributes();
            $info->set_id($publication->get_id());
            $info->set_publisher_user_id($publication->get_publisher());
            $info->set_publication_date($publication->get_published());
            $info->set_application('Profiler');
            //TODO: i8n location string
            $info->set_location(Translation :: get('List'));
            $info->set_url('index_profiler.php?go=view&profile=' . $publication->get_id());
            $info->set_publication_object_id($publication->get_profile());

            $publication_attr[] = $info;
        }
        return $publication_attr;
    }

    //Indered.
    function get_learning_object_publication_attribute($publication_id)
    {
        $publication = $this->retrieve_profile_publication($publication_id);

        $info = new LearningObjectPublicationAttributes();
        $info->set_id($publication->get_id());
        $info->set_publisher_user_id($publication->get_publisher());
        $info->set_publication_date($publication->get_published());
        $info->set_application('Profiler');
        //TODO: i8n location string
        $info->set_location(Translation :: get('List'));
        $info->set_url('index_profiler.php?go=view&profile=' . $publication->get_id());
        $info->set_publication_object_id($publication->get_profile());

        return $info;
    }

    //Inherited.
    function count_publication_attributes($user, $type = null, $condition = null)
    {
        $condition = new EqualityCondition(ProfilePublication :: PROPERTY_PUBLISHER, Session :: get_user_id());
        return $this->database->count_objects(ProfilePublication :: get_table_name(), $condition);
    }

    //Inherited.
    function create_profile_publication($publication)
    {
        return $this->database->create($publication);
    }

    function get_next_category_id()
    {
        return $this->database->get_next_id(ProfilerCategory :: get_table_name());
    }

    function delete_category($category)
    {
        $condition = new EqualityCondition(ProfilerCategory :: PROPERTY_ID, $category->get_id());
        $succes = $this->database->delete('profiler_category', $condition);

        $query = 'UPDATE ' . $this->database->escape_table_name('profiler_category') . ' SET ' . $this->database->escape_column_name(ProfilerCategory :: PROPERTY_DISPLAY_ORDER) . '=' . $this->database->escape_column_name(ProfilerCategory :: PROPERTY_DISPLAY_ORDER) . '-1 WHERE ' . $this->database->escape_column_name(ProfilerCategory :: PROPERTY_DISPLAY_ORDER) . '>? AND ' . $this->database->escape_column_name(ProfilerCategory :: PROPERTY_PARENT) . '=?';
        $statement = $this->database->get_connection()->prepare($query);
        $statement->execute(array($category->get_display_order(), $category->get_parent()));

        return $succes;
    }

    function update_category($category)
    {
        $condition = new EqualityCondition(ProfilerCategory :: PROPERTY_ID, $category->get_id());
        return $this->database->update($category, $condition);
    }

    function create_category($category)
    {
        return $this->database->create($category);
    }

    function count_categories($conditions = null)
    {
        return $this->database->count_objects(ProfilerCategory :: get_table_name(), $conditions);
    }

    function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
    {
        return $this->database->retrieve_objects(ProfilerCategory :: get_table_name(), $condition, $offset, $count, $order_property, $order_direction, 'ProfilerCategory');
    }

    function select_next_category_display_order($parent_category_id)
    {
        $query = 'SELECT MAX(' . ProfilerCategory :: PROPERTY_DISPLAY_ORDER . ') AS do FROM ' . $this->database->escape_table_name('category');

        $condition = new EqualityCondition(ProfilerCategory :: PROPERTY_PARENT, $parent_category_id);
        //print_r($condition);
        $params = array();
        if (isset($condition))
        {
            $translator = new ConditionTranslator($this->database, $params);
            $query .= $translator->render_query($condition);
            $params = $translator->get_parameters();
        }

        $sth = $this->database->get_connection()->prepare($query); dump($sth);
        $res = $sth->execute($params);
        $record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
        $res->free();

        return $record[0] + 1;
    }
}
?>