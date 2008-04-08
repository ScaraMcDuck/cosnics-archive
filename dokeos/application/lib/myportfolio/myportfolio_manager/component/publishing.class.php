<?php
/**
 * @package application.lib.portfolio.portfolio_manager
 */
require_once dirname(__FILE__).'/../myportfolio.class.php';
require_once dirname(__FILE__).'/../portfoliocomponent.class.php';
require_once dirname(__FILE__).'/../../portfoliopublisher.class.php';

class PortfolioPublishingComponent extends PortfolioComponent
{	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get('PublishPortfolio'));
		
		$publisher = $this->get_publisher_html();
		
		$this->display_header($breadcrumbs);
		
		$out = '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
		foreach (array (MyPortfolio::ACTION_VIEW, MyPortfolio::ACTION_CREATE,MyPortfolio::ACTION_EDIT,MyPortfolio::ACTION_PROPS,MyPortfolio::ACTION_SHARING, MyPortfolio::ACTION_STATE) as $action)
		{
			$out .= '<li><a';
			if ($this->get_parent()->get_action() == $action) $out .= ' class="current"';
			$out .= ' href="'.$this->get_url(array (MyPortfolio :: PARAM_ACTION => $action), true).'">'.htmlentities(Translation :: get(ucfirst($action).'Title')).'</a></li>';
		}
		$out .= '</ul><div class="tabbed-pane-content">';

		$out.= $publisher;

	//	echo $publisher;
	//	echo '<div style="clear: both;"></div>';
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