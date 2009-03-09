<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/reporting_template_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../reporting_template_table/default_reporting_template_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../reporting_template.class.php';
require_once dirname(__FILE__).'/../../reporting_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class ReportingTemplateBrowserTableCellRenderer extends DefaultReportingTemplateTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;
	
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function ReportingTemplateBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $reporting_template)
	{
		if ($column === ReportingTemplateBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($reporting_template);
		}
		
		return parent :: render_cell($column, $reporting_template);
	}
	/**
	 * Gets the action links to display
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($reporting_template)
	{
		$toolbar_data = array();
        
		$editing_url = $this->browser->get_reporting_template_viewing_url($reporting_template);
		$toolbar_data[] = array(
			'href' => $editing_url,
			'label' => Translation :: get('View'),
			'img' => Theme :: get_common_image_path().'action_chart.png',
		);
		
		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>