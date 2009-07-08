<?php
/**
 * @package application.portfolio.portfolio.component
 */

require_once dirname(__FILE__).'/../portfolio_manager.class.php';
require_once dirname(__FILE__).'/../portfolio_manager_component.class.php';

/**
 * portfolio component which allows the user to browse his portfolio_publications
 * @author Sven Vanpoucke
 */
class PortfolioManagerViewerComponent extends PortfolioManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_BROWSE)), Translation :: get('BrowsePortfolios')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ViewPortfolio')));

		$this->display_header($trail);

		echo '<a href="' . $this->get_create_portfolio_publication_url() . '">' . Translation :: get('CreatePortfolioPublication') . '</a>';
		echo '<br /><br />';
	
		$this->display_footer();
	}
}
?>