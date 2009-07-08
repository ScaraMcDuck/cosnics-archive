<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/../reporting_data_manager.class.php';
require_once Path :: get_library_path() . 'configuration/configuration.class.php';
require_once Path :: get_library_path() . 'condition/condition_translator.class.php';

class DatabaseReportingDataManager extends ReportingDataManager
{
    private $database;

    function initialize()
    {
        $this->database = new Database(array('reporting_block' => 'rpb', 'reporting_template_registration' => 'rpt'));
        $this->database->set_prefix('reporting_');
    }

    function get_database()
    {
        return $this->database;
    }

    function create_storage_unit($name, $properties, $indexes)
    {
        return $this->database->create_storage_unit($name, $properties, $indexes);
    }

    /**
     * Retrieves the next id from the given table
     * @param string $tablename the tablename
     */
    function get_next_reporting_block_id()
    {
        $id = $this->database->get_next_id(ReportingBlock :: get_table_name());
        return $id;
    }

    function get_next_reporting_template_registration_id()
    {
        $id = $this->database->get_next_id(ReportingTemplateRegistration :: get_table_name());
        return $id;
    }

    /**
     * Creates a reporting block in the database
     * @param ReportingBlock $reporting_block
     */
    function create_reporting_block(&$reporting_block)
    {
        return $this->database->create($reporting_block);
    }

    /**
     * Updates an reporting block (needed for change of activity)
     * @param ReportingBlock $reporting_block
     */
    function update_reporting_block(&$reporting_block)
    {
        $condition = new EqualityCondition(ReportingBlock :: PROPERTY_ID, $reporting_block->get_id());
        return $this->database->update($reporting_block, $condition);
    }

    /**
     * Retrieves the reporting block with the given name
     * @param String $name
     * @return ReportingBlock $reporting_block
     */
    function retrieve_reporting_block_by_name($blockname)
    {
        $condition = new EqualityCondition(ReportingBlock :: PROPERTY_NAME, $blockname);
        return $this->database->retrieve_object(ReportingBlock :: get_table_name(), $condition);
    }

    /**
     * Retrieves all reporting blocks
     * @return array of reporting blocks
     */
    function retrieve_reporting_blocks($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
    {
        return $this->database->retrieve_objects(ReportingBlock :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
    }

    /**
     * Count reporting blocks for a given condition
     * @param Condition $condition
     * @return Int reporting block count
     */
    function count_reporting_blocks($condition = null)
    {
        return $this->database->count_objects(ReportingBlock :: get_table_name(), $condition);
    }

    /**
     * Retrieves a reporting block by given id
     * @param int $reporting_block_id
     * @return ReportingBlock $reporting_block
     */
    function retrieve_reporting_block($reporting_block_id)
    {
        $condition = new EqualityCondition(ReportingBlock :: PROPERTY_ID, $reporting_block_id);
        return $this->database->retrieve_object(ReportingBlock :: get_table_name(), $condition);
    }

    function create_reporting_template_registration(&$reporting_template_registration)
    {
        return $this->database->create($reporting_template_registration);
    } //create_reporting_template_registration


    function update_reporting_template_registration(&$reporting_template_registration)
    {
        $condition = new EqualityCondition(ReportingTemplateRegistration :: PROPERTY_ID, $reporting_template_registration->get_id());
        return $this->database->update($reporting_template_registration, $condition);
    } //update_reporting_template_registration


    //function retrieve_reporting_template_registration_by_name($reporting_template_registration_name)
    //{
    //$condition = new EqualityCondition(ReportingTemplateRegistration :: PROPERTY_NAME, $reporting_template_registration_name);
    //return $this->database->retrieve_object(ReportingTemplateRegistration :: get_table_name(), $condition);
    //}//retrieve_reporting_template_registration_by_name


    function retrieve_reporting_template_registrations($condition = null, $offset = null, $maxObjects = null, $order_property = null, $order_direction = null)
    {
        return $this->database->retrieve_objects(ReportingTemplateRegistration :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
    } //retrieve_reporting_template_registrations


    function count_reporting_template_registrations($condition = null)
    {
        return $this->database->count_objects(ReportingTemplateRegistration :: get_table_name(), $condition);
    } //count_reporting_template_registrations


    function retrieve_reporting_template_registration($reporting_template_registration_id)
    {
        $condition = new EqualityCondition(ReportingTemplateRegistration :: PROPERTY_ID, $reporting_template_registration_id);
        return $this->database->retrieve_object(ReportingTemplateRegistration :: get_table_name(), $condition);
    } //retrieve_reporting_template_registration


    function delete_reporting_template_registrations($condition = null)
    {
        return $this->database->delete_objects(ReportingTemplateRegistration :: get_table_name(), $condition);
    }

    function delete_reporting_blocks($condition = null)
    {
        return $this->database->delete_objects(ReportingBlock :: get_table_name(), $condition);
    }

    function delete_orphaned_block_template_relations()
    {
		$query = 'DELETE FROM '.$this->database->escape_table_name('reporting_template_registration_rel_reporting_block').' WHERE ';
		$query .= $this->database->escape_column_name('reporting_template_registration_id') . ' NOT IN (SELECT ' . $this->database->escape_column_name(ReportingTemplateRegistration :: PROPERTY_ID) . ' FROM ' . $this->database->escape_table_name(ReportingTemplateRegistration :: get_table_name()) . ') OR ';
		$query .= $this->database->escape_column_name('reporting_block_id').' NOT IN (SELECT ' . $this->database->escape_column_name(ReportingBlock :: PROPERTY_ID) . ' FROM ' . $this->database->escape_table_name(ReportingBlock :: get_table_name()) . ')';
		$sth = $this->database->get_connection()->prepare($query);
		return $sth->execute();
    }
}
?>