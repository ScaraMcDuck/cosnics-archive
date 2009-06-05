<?php

/**
 * @package application.distribute
 * @author Hans De Bisschop
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
/**
 * Data provider for a distribute browser table.
 *
 * This class implements some functions to allow distribute browser tables to
 * retrieve information about the announcements to display.
 */
class AnnouncementDistributionBrowserTableDataProvider extends ObjectTableDataProvider
{
	/**
	 * Constructor
	 * @param DistributeManagerComponent $browser
	 * @param Condition $condition
	 */
	function AnnouncementDistributionBrowserTableDataProvider($browser, $condition)
	{
		parent :: __construct($browser, $condition);
	}
	/**
	 * Gets the announcement distributions
	 * @param int $offset
	 * @param int $count
	 * @param string $order_property
	 * @param int $order_direction (SORT_ASC or SORT_DESC)
	 * @return ResultSet A set of matching announcement distributions.
	 */
	function get_objects($offset, $count, $order_property = null, $order_direction = null)
	{
		$order_property = $this->get_order_property($order_property);
		$order_direction = $this->get_order_direction($order_direction);
		return $this->get_browser()->retrieve_announcement_distributions($this->get_condition(), $offset, $count, $order_property, $order_direction);
	}
	/**
	 * Gets the number of announcement distributions in the table
	 * @return int
	 */
	function get_object_count()
	{
		return $this->get_browser()->count_announcement_distributions($this->get_condition());
	}
}
?>