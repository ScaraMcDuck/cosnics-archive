<?php
/**
 * @author Michael Kyndt
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
/**
 * Data provider for a reporting template registration browser table.
 *
 * This class implements some functions to allow reporting template registration
 * browser tables to retrieve information about the reporting template
 * registration objects to display.
 */
class ReportingTemplateRegistrationBrowserTableDataProvider extends ObjectTableDataProvider
{
  /**
   * Constructor
   * @param ReportingTemplateRegistrationManagerComponent $browser
   * @param Condition $condition
   */
  function ReportingTemplateRegistrationBrowserTableDataProvider($browser, $condition)
  {
		parent :: __construct($browser, $condition);
  }
  /**
   * Gets the reporting template registration objects
   * @param int $offset
   * @param int $count
   * @param string $order_property
   * @param int $order_direction (SORT_ASC or SORT_DESC)
   * @return ResultSet A set of matching learning objects.
   */
    function get_objects($offset, $count, $order_property = null, $order_direction = null)
    {

		$order_property = $this->get_order_property($order_property);
		$order_direction = $this->get_order_property($order_direction);
        //return $this->get_browser()->get_parent()->retrieve_platform_reporting_templates_for_application($this->get_condition(), $offset, $count, $order_property, $order_direction);
        //return $this->get_browser()->retrieve_reporting_templates($this->get_condition(), $offset, $count, $order_property, $order_direction);
        return $this->get_browser()->retrieve_reporting_template_registrations($this->get_condition(), $offset, $count, $order_property, $order_direction);
    }
  /**
   * Gets the number of reporting template registration objects in the table
   * @return int
   */
    function get_object_count()
    {
      return $this->get_browser()->count_reporting_template_registrations($this->get_condition());
    }
}
?>