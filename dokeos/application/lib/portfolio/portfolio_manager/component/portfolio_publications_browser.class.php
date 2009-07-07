<?php
/**
 * @package application.portfolio.portfolio.component
 */

require_once dirname(__FILE__).'/../portfolio_manager.class.php';
require_once dirname(__FILE__).'/../portfolio_manager_component.class.php';
require_once dirname(__FILE__).'/portfolio_publication_browser/portfolio_publication_browser_table.class.php';

/**
 * portfolio component which allows the user to browse his portfolio_publications
 * @author Sven Vanpoucke
 */
class PortfolioManagerPortfolioPublicationsBrowserComponent extends PortfolioManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_BROWSE)), Translation :: get('BrowsePortfolio')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowsePortfolioPublications')));

		$this->display_header($trail);

		echo '<a href="' . $this->get_create_portfolio_publication_url() . '">' . Translation :: get('CreatePortfolioPublication') . '</a>';
		echo '<br /><br />';
		echo $this->get_table();
		$this->display_footer();
	}

	function get_table()
	{
		$table = new PortfolioPublicationBrowserTable($this, array(Application :: PARAM_APPLICATION => 'portfolio', Application :: PARAM_ACTION => PortfolioManager :: ACTION_BROWSE_PORTFOLIO_PUBLICATIONS), null);
		return $table->as_html();
	}

}
?>