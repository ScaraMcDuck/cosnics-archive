<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__).'/../reporting_data_manager.class.php';
require_once Path :: get_library_path().'configuration/configuration.class.php';
require_once Path :: get_library_path().'condition/condition_translator.class.php';
require_once dirname(__FILE__).'/database/databasereportingblockresultset.class.php';
require_once dirname(__FILE__).'/database/databasereportingtemplateresultset.class.php';

class DatabaseReportingDataManager extends ReportingDataManager
{
    private $database;

	function initialize()
	{
		$this->database = new Database(array('reporting_block' => 'rpb', 'reporting_template' => 'rpt'));
		$this->database->set_prefix('reporting_');
	}

	function create_storage_unit($name,$properties,$indexes)
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

    function get_next_reporting_template_id()
    {
        $id = $this->database->get_next_id(ReportingTemplate :: get_table_name());
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
	
	function create_reporting_template(&$reporting_template)
	{
        return $this->database->create($reporting_template);
	}//create_reporting_template
	
	function update_reporting_template(&$reporting_template)
	{
		$condition = new EqualityCondition(ReportingTemplate :: PROPERTY_ID, $reporting_template->get_id());
		return $this->database->update($reporting_template, $condition);
	}//update_reporting_template
	
	function retrieve_reporting_template_by_name($reporting_template_name)
	{
        $condition = new EqualityCondition(ReportingTemplate :: PROPERTY_NAME, $reporting_template_name);
		return $this->database->retrieve_object(ReportingTemplate :: get_table_name(), $condition);
	}//retrieve_reporting_template_by_name
	
	function retrieve_reporting_templates($condition = null,$offset = null,$maxObjects = null, $order_property = null, $order_direction = null)
	{
         return $this->database->retrieve_objects(ReportingTemplate :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}//retrieve_reporting_templates
	
	function count_reporting_templates($condition = null)
	{
        return $this->database->count_objects(ReportingTemplate :: get_table_name(), $condition);
	}//count_reporting_templates
	
	function retrieve_reporting_template($reporting_template_id)
	{
        $condition = new EqualityCondition(ReportingTemplate :: PROPERTY_ID, $reporting_template_id);
		return $this->database->retrieve_object(ReportingTemplate :: get_table_name(), $condition);
	}//retrieve_reporting_template
}
?>