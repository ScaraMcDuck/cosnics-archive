<?php
/**
 * @package portfolio.tables.portfolio_publication_table
 */
require_once dirname(__FILE__).'/portfolio_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/portfolio_publication_table/default_portfolio_publication_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../portfolio_publication.class.php';
require_once dirname(__FILE__).'/../../portfolio_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 * @author Sven Vanpoucke
 */

class PortfolioPublicationBrowserTableCellRenderer extends DefaultPortfolioPublicationTableCellRenderer
{
	/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function PortfolioPublicationBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $portfolio_publication)
	{
		if ($column === PortfolioPublicationBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($portfolio_publication);
		}

		return parent :: render_cell($column, $portfolio_publication);
	}

	/**
	 * Gets the action links to display
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($portfolio_publication)
	{
		$toolbar_data = array();

		$toolbar_data[] = array(
			'href' => $this->browser->get_update_portfolio_publication_url($portfolio_publication),
			'label' => Translation :: get('Edit'),
			'img' => Theme :: get_common_image_path().'action_edit.png'
		);

		$toolbar_data[] = array(
			'href' => $this->browser->get_delete_portfolio_publication_url($portfolio_publication),
			'label' => Translation :: get('Delete'),
			'img' => Theme :: get_common_image_path().'action_delete.png',
		);

		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>