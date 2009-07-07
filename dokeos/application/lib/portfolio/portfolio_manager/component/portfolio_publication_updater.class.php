<?php
/**
 * @package application.portfolio.portfolio.component
 */
require_once dirname(__FILE__).'/../portfolio_manager.class.php';
require_once dirname(__FILE__).'/../portfolio_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/portfolio_publication_form.class.php';

/**
 * Component to edit an existing portfolio_publication object
 * @author Sven Vanpoucke
 */
class PortfolioManagerPortfolioPublicationUpdaterComponent extends PortfolioManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_BROWSE)), Translation :: get('BrowsePortfolio')));
		$trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_BROWSE_PORTFOLIO_PUBLICATIONS)), Translation :: get('BrowsePortfolioPublications')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdatePortfolioPublication')));

		$portfolio_publication = $this->retrieve_portfolio_publication(Request :: get(PortfolioManager :: PARAM_PORTFOLIO_PUBLICATION));
		$form = new PortfolioPublicationForm(PortfolioPublicationForm :: TYPE_EDIT, $portfolio_publication, $this->get_url(array(PortfolioManager :: PARAM_PORTFOLIO_PUBLICATION => $portfolio_publication->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_portfolio_publication();
			$this->redirect($success ? Translation :: get('PortfolioPublicationUpdated') : Translation :: get('PortfolioPublicationNotUpdated'), !$success, array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_BROWSE_PORTFOLIO_PUBLICATIONS));
		}
		else
		{
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}
	}
}
?>