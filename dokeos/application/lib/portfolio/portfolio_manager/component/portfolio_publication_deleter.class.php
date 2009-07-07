<?php
/**
 * @package application.portfolio.portfolio.component
 */
require_once dirname(__FILE__).'/../portfolio_manager.class.php';
require_once dirname(__FILE__).'/../portfolio_manager_component.class.php';

/**
 * Component to delete portfolio_publications objects
 * @author Sven Vanpoucke
 */
class PortfolioManagerPortfolioPublicationDeleterComponent extends PortfolioManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[PortfolioManager :: PARAM_PORTFOLIO_PUBLICATION];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$portfolio_publication = $this->retrieve_portfolio_publication($id);

				if (!$portfolio_publication->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedPortfolioPublicationDeleted';
				}
				else
				{
					$message = 'SelectedPortfolioPublicationDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedPortfolioPublicationsDeleted';
				}
				else
				{
					$message = 'SelectedPortfolioPublicationsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_BROWSE_PORTFOLIO_PUBLICATIONS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoPortfolioPublicationsSelected')));
		}
	}
}
?>