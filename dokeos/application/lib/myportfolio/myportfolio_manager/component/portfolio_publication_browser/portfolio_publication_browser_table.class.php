<?php
/**
 * @package application.lib.portfolio.portfolio_manager.component.portfolioepublicationbrowser
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/portfolio_publication_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/portfolio_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/portfolio_publication_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../myportfolio_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class PortfolioPublicationBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'portfolio_publication_browser_table';
	
	/**
	 * Constructor
	 */
	function PortfolioPublicationBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new PortfolioPublicationBrowserTableColumnModel();
		$renderer = new PortfolioPublicationBrowserTableCellRenderer($browser);
		$data_provider = new PortfolioPublicationBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, PortfolioPublicationBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$this->set_default_row_count(20);
	}
}
?>