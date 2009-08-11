<?php
/**
 * @package webconferencing.datamanager
 */
require_once dirname(__FILE__).'/../webconference.class.php';
require_once dirname(__FILE__).'/../webconference_option.class.php';
require_once Path :: get_library_path() . 'database/database.class.php';
require_once 'MDB2.php';

/**
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *  @author Stefaan Vanbillemont
 */

class DatabaseWebconferencingDataManager extends WebconferencingDataManager
{
    private $database;

    function initialize()
    {
        $aliases = array();
        $aliases[Webconference :: get_table_name()] = 'wece';
        $aliases[WebconferenceOption :: get_table_name()] = 'weon';

        $this->database = new Database($aliases);
        $this->database->set_prefix('webconferencing_');
    }

    function get_database()
    {
        return $this->database;
    }

    function create_storage_unit($name, $properties, $indexes)
    {
        return $this->database->create_storage_unit($name, $properties, $indexes);
    }

    function get_next_webconference_id()
    {
        return $this->database->get_next_id(Webconference :: get_table_name());
    }

    function create_webconference($webconference)
    {
    //return $this->database->create($webconference);

        $succes = $this->database->create($webconference);

        foreach($webconference->get_target_groups() as $group)
        {
            $webconference_group = new WebconferenceGroup();
            $webconference_group->set_webconference($webconference->get_id());
            $webconference_group->set_group_id($group);
            $succes = $webconference_group->create();
        }

        foreach($webconference->get_target_users() as $user)
        {
            $webconference_user = new WebconferenceUser();
            $webconference_user->set_webconference($webconference->get_id());
            $webconference_user->set_user($user);
            $succes = $webconference_user->create();
        }

        return $succes;
    }

    function update_webconference($webconference)
    {
        $condition = new EqualityCondition(Webconference :: PROPERTY_ID, $webconference->get_id());
        return $this->database->update($webconference, $condition);
    }

    function delete_webconference($webconference)
    {
        $this->delete_webconference_options($webconference);
        $condition = new EqualityCondition(Webconference :: PROPERTY_ID, $webconference->get_id());
        return $this->database->delete($webconference->get_table_name(), $condition);
    }

    function count_webconferences($condition = null)
    {
        return $this->database->count_objects(Webconference :: get_table_name(), $condition);
    }

    function retrieve_webconference($id)
    {
        $condition = new EqualityCondition(Webconference :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(Webconference :: get_table_name(), $condition);
    }

    function retrieve_webconferences($condition = null, $offset = null, $max_objects = null, $order_by = null, $order_dir = null)
    {
        return $this->database->retrieve_objects(Webconference :: get_table_name(), $condition, $offset, $max_objects, $order_by, $order_dir);
    }

    function get_next_webconference_option_id()
    {
        return $this->database->get_next_id(WebconferenceOption :: get_table_name());
    }

    function create_webconference_option($webconference_option)
    {
        return $this->database->create($webconference_option);
    }

    function update_webconference_option($webconference_option)
    {
        $condition = new EqualityCondition(WebconferenceOption :: PROPERTY_ID, $webconference_option->get_id());
        return $this->database->update($webconference_option, $condition);
    }

    function delete_webconference_option($webconference_option)
    {
        $condition = new EqualityCondition(WebconferenceOption :: PROPERTY_ID, $webconference_option->get_id());
        return $this->database->delete($webconference_option->get_table_name(), $condition);
    }

    function delete_webconference_options($webconference)
    {
        $condition = new EqualityCondition(WebconferenceOption :: PROPERTY_CONF_ID, $webconference->get_id());
        return $this->database->delete(WebconferenceOption :: get_table_name(), $condition);
    }

    function count_webconference_options($condition = null)
    {
        return $this->database->count_objects(WebconferenceOption :: get_table_name(), $condition);
    }

    function retrieve_webconference_option($id)
    {
        $condition = new EqualityCondition(WebconferenceOption :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(WebconferenceOption :: get_table_name(), $condition);
    }

    function retrieve_webconference_options($condition = null, $offset = null, $max_objects = null, $order_by = null, $order_dir = null)
    {
        return $this->database->retrieve_objects(WebconferenceOption :: get_table_name(), $condition, $offset, $max_objects, $order_by, $order_dir);
    }

    function retrieve_webconference_groups($condition = null, $offset = null, $max_objects = null, $order_by = null, $order_dir = null)
    {
        return $this->database->retrieve_objects(WebconferenceGroup :: get_table_name(), $condition, $offset, $max_objects, $order_by, $order_dir, WebconferenceGroup :: CLASS_NAME);
    }

    function retrieve_webconference_users($condition = null, $offset = null, $max_objects = null, $order_by = null, $order_dir = null)
    {
        return $this->database->retrieve_objects(WebconferenceUser :: get_table_name(), $condition, $offset, $max_objects, $order_by, $order_dir, WebconferenceUser :: CLASS_NAME);
    }

    function create_webconference_user($webconference_user)
    {
        return $this->database->create($webconference_user);
    }

    function create_webconference_group($webconference_group)
    {
        return $this->database->create($webconference_group);
    }

}
?>