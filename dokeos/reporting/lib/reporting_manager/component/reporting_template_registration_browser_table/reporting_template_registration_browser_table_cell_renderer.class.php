<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__).'/reporting_template_registration_browser_table_column_model.class.php';
require_once Path :: get_reporting_path().'lib/reporting_template_registration_table/default_reporting_template_registration_table_cell_renderer.class.php';
require_once Path :: get_reporting_path().'lib/reporting_template_registration.class.php';
require_once Path :: get_reporting_path().'lib/reporting_manager/reporting_manager.class.php';
/**
 * Cell renderer for the reporting template registration browser table
 */
class ReportingTemplateRegistrationBrowserTableCellRenderer extends DefaultReportingTemplateRegistrationTableCellRenderer
{
	/**
	 * The reporting template registration browser component
	 */
	private $browser;
	
	/**
	 * Constructor
	 * @param ReportingTemplateManagerBrowserComponent $browser
	 */
	function ReportingTemplateRegistrationBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $reporting_template_registration)
	{
		if ($column === ReportingTemplateRegistrationBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($reporting_template_registration);
		}
		
		return parent :: render_cell($column, $reporting_template_registration);
	}
	/**
	 * Gets the action links to display
	 * @param ReportingTemplateRegistration $reporting_template_registration The template
     * object for which the links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($reporting_template_registration)
	{
		$toolbar_data = array();
        
		$editing_url = $this->browser->get_reporting_template_registration_viewing_url($reporting_template_registration);
		$toolbar_data[] = array(
			'href' => $editing_url,
			'label' => Translation :: get('View'),
			'img' => Theme :: get_common_image_path().'action_chart.png',
		);
		
		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>