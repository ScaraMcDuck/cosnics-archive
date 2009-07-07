<?php
/**
 * @package application.portfolio.portfolio.component
 */
require_once dirname(__FILE__).'/../portfolio_manager.class.php';
require_once dirname(__FILE__).'/../portfolio_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/portfolio_publication_form.class.php';

/**
 * Component to create a new portfolio_publication object
 * @author Sven Vanpoucke
 */
class PortfolioManagerPortfolioPublicationCreatorComponent extends PortfolioManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_BROWSE)), Translation :: get('BrowsePortfolio')));
		$trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_BROWSE_PORTFOLIO_PUBLICATIONS)), Translation :: get('BrowsePortfolioPublications')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreatePortfolioPublication')));

		$portfolio_publication = new PortfolioPublication();
		$form = new PortfolioPublicationForm(PortfolioPublicationForm :: TYPE_CREATE, $portfolio_publication, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_portfolio_publication();
			$this->redirect($success ? Translation :: get('PortfolioPublicationCreated') : Translation :: get('PortfolioPublicationNotCreated'), !$success, array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_BROWSE_PORTFOLIO_PUBLICATIONS));
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