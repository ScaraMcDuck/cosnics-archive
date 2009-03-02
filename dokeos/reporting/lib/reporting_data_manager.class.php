<?php
/**
 * @author Michael Kyndt
 */
abstract class ReportingDataManager {
	private static $instance;
	
	public static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.strtolower($type).'.class.php';
			$class = $type.'ReportingDataManager';
			self :: $instance = new $class ();
		}
		return self :: $instance;
	}//get_instance
	
	protected function ReportingDataManager()
	{
		$this->initialize();
	}//ReportingDataManager
	
		/**
	 * Creates a storage unit
	 * @param string $name Name of the storage unit
	 * @param array $properties Properties of the storage unit
	 * @param array $indexes The indexes which should be defined in the created
	 * storage unit
	 */
	abstract function create_storage_unit($name,$properties,$indexes); //?
	/**
	 * Creates a reporting block in the database
	 * @param Event $event
	 */
	abstract function create_reporting_block(&$reporting_block);
	/**
	 * Updates a reporting block (needed for change of activity)
	 * @param Event $event
	 */
	abstract function update_reporting_block(&$reporting_block);
	/**
	 * Retrieves the reporting block with the given name
	 * @param String $name
	 */
	abstract function retrieve_reporting_block_by_name($blockname);
	/**
	 * Retrieves all reporting blocks
	 */
	abstract function retrieve_reporting_blocks($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null);
	/**
	 * Counts the reporting blocks for a given condition
	 * @param Condition $condition
	 */
	abstract function count_reporting_blocks($condition = null);
	/**
	 * Retrieves a reporting block by given id
	 * @param int $event_id
	 * @return Event $event
	 */
	abstract function retrieve_reporting_block($block_id);
	
}//ReportingDataManager
?>