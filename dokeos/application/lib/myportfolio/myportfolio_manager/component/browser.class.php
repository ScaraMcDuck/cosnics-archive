<?php
/**
 * @package application.lib.portfolio.portfolio_manager
 */
require_once dirname(__FILE__).'/../myportfolio.class.php';
require_once dirname(__FILE__).'/../portfolio_component.class.php';
require_once dirname(__FILE__).'/portfolio_publication_browser/portfolio_publication_browser_table.class.php';

class PortfolioBrowserComponent extends PortfolioComponent
{	
	private $firstletter;
	private $example;
	
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$this->firstletter = $_GET[MyPortfolioManager :: PARAM_FIRSTLETTER];
		$this->example = $_GET[MyPortfolioManager :: PARAM_EXAMPLE];
		
		
		$output = $this->get_publications_html();
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('MyPortfolio')));
		
		$this->display_header($trail, true);
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
		$condition = null;
		if (isset($this->firstletter))
		{
			$conditions = array();
			$conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, $this->firstletter. '*');
			$conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, chr(ord($this->firstletter)+1). '*');
			$conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, chr(ord($this->firstletter)+2). '*');
			$condition = new OrCondition($conditions);
		}
		elseif (isset($this->example))
		{
			$conditions = array();
			$conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, 'fquestie');
			$conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, 'kmarchan');
			$conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, 'vboschlo');

			$condition = new OrCondition($conditions);
		}
		else
		{
			//if (count($search_conditions))
			//{
			//	$condition = $search_conditions;
			//}
		}
		
		return $condition;
	}
}
?>
