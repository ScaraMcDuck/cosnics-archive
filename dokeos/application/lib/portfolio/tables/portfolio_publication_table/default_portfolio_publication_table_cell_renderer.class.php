<?php
/**
 * @package portfolio.tables.portfolio_publication_table
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../portfolio_publication.class.php';

/**
 * Default cell renderer for the portfolio_publication table
 * @author Sven Vanpoucke
 */
class DefaultPortfolioPublicationTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultPortfolioPublicationTableCellRenderer()
	{
	}

	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param PortfolioPublication $portfolio_publication - The portfolio_publication
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $portfolio_publication)
	{
		if ($property = $column->get_object_property())
		{
			switch ($property)
			{
				case PortfolioPublication :: PROPERTY_ID :
					return $portfolio_publication->get_id();
				case PortfolioPublication :: PROPERTY_LEARNING_OBJECT :
					return $portfolio_publication->get_learning_object();
				case PortfolioPublication :: PROPERTY_FROM_DATE :
					return $portfolio_publication->get_from_date();
				case PortfolioPublication :: PROPERTY_TO_DATE :
					return $portfolio_publication->get_to_date();
				case PortfolioPublication :: PROPERTY_HIDDEN :
					return $portfolio_publication->get_hidden();
				case PortfolioPublication :: PROPERTY_PUBLISHER :
					return $portfolio_publication->get_publisher();
				case PortfolioPublication :: PROPERTY_PUBLISHED :
					return $portfolio_publication->get_published();
			}
		}
		return '&nbsp;';
	}

	function render_id_cell($object)
	{
		return $object->get_id();
	}
}
?>