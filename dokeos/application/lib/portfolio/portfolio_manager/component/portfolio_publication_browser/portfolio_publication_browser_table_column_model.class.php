<?php
/**
 * @package portfolio.tables.portfolio_publication_table
 */

require_once dirname(__FILE__).'/../../../tables/portfolio_publication_table/default_portfolio_publication_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../portfolio_publication.class.php';

/**
 * Table column model for the portfolio_publication browser table
 * @author Sven Vanpoucke
 */

class PortfolioPublicationBrowserTableColumnModel extends DefaultPortfolioPublicationTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function PortfolioPublicationBrowserTableColumnModel()
	{
		parent :: __construct();
		$this->set_default_order_column(1);
		$this->add_column(self :: get_modification_column());
	}

	/**
	 * Gets the modification column
	 * @return LearningObjectTableColumn
	 */
	static function get_modification_column()
	{
		if (!isset(self :: $modification_column))
		{
			self :: $modification_column = new ObjectTableColumn('');
		}
		return self :: $modification_column;
	}
}
?>