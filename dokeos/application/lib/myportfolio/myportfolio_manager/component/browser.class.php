<?php
/**
 * @package application.lib.portfolio.portfolio_manager
 */
require_once dirname(__FILE__).'/../myportfolio.class.php';
require_once dirname(__FILE__).'/../portfoliocomponent.class.php';
require_once dirname(__FILE__).'/portfoliopublicationbrowser/portfoliopublicationbrowsertable.class.php';

class PortfolioBrowserComponent extends PortfolioComponent
{	
	private $firstletter;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$this->firstletter = $_GET[MyPortfolio :: PARAM_FIRSTLETTER];
		
		$output = $this->get_publications_html();
		
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get('MyPortfolio'));
		
		$this->display_header($breadcrumbs, true);
		echo $output;
		$this->display_footer();
	}
	
	private function get_publications_html()
	{
		$parameters = $this->get_parameters(true);
		
		$table = new PortfolioPublicationBrowserTable($this, null, $parameters, $this->get_condition());
		
		$html = array();
		$html[] = $table->as_html();
		
		return implode($html, "\n");
	}
	
	function get_condition()
	{
		$search_conditions = $this->get_search_condition();
		//$search_conditions = null;
		$condition = null;
		if (isset($this->firstletter))
		{
			$conditions = array();
			$conditions[] = new LikeCondition(User :: PROPERTY_USERNAME, $this->firstletter. '%');
			$conditions[] = new LikeCondition(User :: PROPERTY_USERNAME, chr(ord($this->firstletter)+1). '%');
			$conditions[] = new LikeCondition(User :: PROPERTY_USERNAME, chr(ord($this->firstletter)+2). '%');
			$condition = new OrCondition($conditions);
			if (count($search_conditions))
			{
				$condition = new AndCondition($condition, $search_conditions);
			}
		}
		else
		{
			if (count($search_conditions))
			{
				$condition = $search_conditions;
			}
		}
		
		return $condition;
	}
}
?>