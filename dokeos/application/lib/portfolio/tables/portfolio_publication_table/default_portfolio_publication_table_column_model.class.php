<?php
/**
 * @package portfolio.tables.portfolio_publication_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once dirname(__FILE__).'/../../portfolio_publication.class.php';

/**
 * Default column model for the portfolio_publication table
 * @author Sven Vanpoucke
 */
class DefaultPortfolioPublicationTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultPortfolioPublicationTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 1);
	}

	/**
	 * Gets the default columns for this model
	 * @return Array(ObjectTableColumn)
	 */
	private static function get_default_columns()
	{
		$columns = array();

		$columns[] = new ObjectTableColumn(PortfolioPublication :: PROPERTY_ID, true);
		$columns[] = new ObjectTableColumn(PortfolioPublication :: PROPERTY_LEARNING_OBJECT, true);
		$columns[] = new ObjectTableColumn(PortfolioPublication :: PROPERTY_FROM_DATE, true);
		$columns[] = new ObjectTableColumn(PortfolioPublication :: PROPERTY_TO_DATE, true);
		$columns[] = new ObjectTableColumn(PortfolioPublication :: PROPERTY_HIDDEN, true);
		$columns[] = new ObjectTableColumn(PortfolioPublication :: PROPERTY_PUBLISHER, true);
		$columns[] = new ObjectTableColumn(PortfolioPublication :: PROPERTY_PUBLISHED, true);

		return $columns;
	}
}
?>