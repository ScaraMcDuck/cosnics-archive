<?php
/**
 * @package application.lib.portfolio.portfolio_manager
 */
require_once dirname(__FILE__).'/../myportfolio_manager.class.php';
require_once dirname(__FILE__).'/../portfolio_component.class.php';
require_once dirname(__FILE__).'/../../portfolio_publisher.class.php';

class PortfolioPublishingComponent extends PortfolioComponent
{	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('PublishPortfolio')));
		
		$publisher = $this->get_publisher_html();
		
		$this->display_header($trail);
		
		$out = '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
		foreach (array (MyPortfolioManager :: ACTION_VIEW, MyPortfolioManager :: ACTION_EDIT,MyPortfolioManager :: ACTION_CREATE,MyPortfolioManager :: ACTION_PROPS) as $action)
		{
			$out .= '<li><a';
			if ($this->get_parent()->get_action() == $action) $out .= ' class="current"';
			$out .= ' href="'.$this->get_url(array (MyPortfolioManager :: PARAM_ACTION => $action), true).'">'.htmlentities(Translation :: get(ucfirst($action).'Title')).'</a></li>';
		}
		$out .= '</ul><div class="tabbed-pane-content">';

		$out.= $publisher;

		$out .= '</div></div>';
		echo $out;
		$this->display_footer();
	}
	
	private function get_publisher_html()
	{
		$pub = new PortfolioPublisher($this, 'portfolio_item', true);
		$html[] =  $pub->as_html();
		
		return implode($html, "\n");
	}
}
?>