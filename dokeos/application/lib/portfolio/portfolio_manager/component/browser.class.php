<?php
/**
 * @package application.portfolio.portfolio.component
 */
require_once dirname(__FILE__).'/../portfolio_manager.class.php';
require_once dirname(__FILE__).'/../portfolio_manager_component.class.php';
require_once dirname(__FILE__).'/../../user_menu.class.php';

/**
 * Portfolio component which allows the user to browse the portfolio application
 * @author Sven Vanpoucke
 */
class PortfolioManagerBrowserComponent extends PortfolioManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowsePortfolio')));

		$this->display_header($trail);

		$menu = new UserMenu('A');
		echo $menu->render_as_tree();
	
		$this->display_footer();
	}

}
?>