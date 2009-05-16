<?php
/**
 * @package application.lib.portfolio.portfolio_manager
 */
require_once dirname(__FILE__).'/../myportfolio_manager.class.php';
require_once dirname(__FILE__).'/../portfolio_component.class.php';
require_once dirname(__FILE__).'/../portfolio_props_form.class.php';

class PortfolioPropsComponent extends PortfolioComponent
{

		/**
	 * Runs this component and displays its output.
	 */
	function run()
	{	
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ViewPortfolio')));

		$item=$this->get_parent()->get_item_id();
		
		if ($item)
		{
			$this->publication = $this->retrieve_portfolio_publication_from_item($item);			
			
			//$breadcrumbs = array();
			//$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get('ViewPortfolio') . ': ' . $this->publication->get_publication_publisher()->get_username());
			
			$this->display_header($trail);

			$out = '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
			foreach (array (MyPortfolioManager :: ACTION_VIEW, MyPortfolioManager :: ACTION_EDIT,MyPortfolioManager :: ACTION_CREATE,MyPortfolioManager :: ACTION_PROPS) as $action)
			{
				$out .= '<li><a';
				if ($this->get_parent()->get_action() == $action) $out .= ' class="current"';
				$out .= ' href="'.$this->get_url(array (MyPortfolioManager :: PARAM_ACTION => $action), true).'">'.htmlentities(Translation :: get(ucfirst($action).'Title')).'</a></li>';
			}
			$out .= '</ul><div class="tabbed-pane-content">';

			$out.= $this->get_props_as_html($item);

			$out .= '</div></div>';
			echo $out;
			$this->display_footer();
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoPortfolioSelected')));
		}
	}
	
	function get_props_as_html($item)
	{
		$publication = $this->publication;
		$portfolio = $publication->get_publication_object();
			
		$form = new PortfolioPropertiesForm($this, $this->get_user(), $this->get_url(), $item);
		if ($form->validate())
		{
			$form->commit_changes();
			$result = "Changes applied";
		}
		
		return $result .= $form->toHtml();
	}

}
?>
