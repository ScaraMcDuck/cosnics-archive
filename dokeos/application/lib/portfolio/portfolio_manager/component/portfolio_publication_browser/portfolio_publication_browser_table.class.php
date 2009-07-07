<?php
/**
 * @package portfolio.portfolio_manager.component.portfolio_publication_browser
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/portfolio_publication_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/portfolio_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/portfolio_publication_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../portfolio_manager.class.php';

/**
 * Table to display a list of portfolio_publications
 * @author Sven Vanpoucke
 */
class PortfolioPublicationBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'portfolio_publication_browser_table';

	/**
	 * Constructor
	 */
	function PortfolioPublicationBrowserTable($browser, $parameters, $condition)
	{
		$model = new PortfolioPublicationBrowserTableColumnModel();
		$renderer = new PortfolioPublicationBrowserTableCellRenderer($browser);
		$data_provider = new PortfolioPublicationBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		$actions[PortfolioManager :: PARAM_DELETE_SELECTED_PORTFOLIO_PUBLICATIONS] = Translation :: get('RemoveSelected');
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>