<?php
/**
 * @package application.lib.portfolio.portfolio_manager
 */
require_once dirname(__FILE__).'/../myportfolio.class.php';
require_once dirname(__FILE__).'/../portfoliocomponent.class.php';

class PortfolioDeleterComponent extends PortfolioComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[MyPortfolio :: PARAM_PROFILE_ID];
		$failures = 0;
		
		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}
			
			foreach ($ids as $id)
			{
				$publication = $this->get_parent()->retrieve_portfolio_publication($id);
				
				if (!$publication->delete())
				{
					$failures++;
				}
			}
			
			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedPublicationNotDeleted';
				}
				else
				{
					$message = 'SelectedPublicationsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedPublicationDeleted';
				}
				else
				{
					$message = 'SelectedPublicationsDeleted';
				}
			}
			
			$this->redirect(null, Translation :: get($message), ($failures ? true : false), array(MyPortfolio :: PARAM_ACTION => MyPortfolio :: ACTION_BROWSE_PROFILES));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoPublicationSelected')));
		}
	}
}
?>