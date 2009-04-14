<?php
/**
 * @package application.lib.portfolio.portfolio_manager
 */
require_once dirname(__FILE__).'/../myportfolio.class.php';
require_once dirname(__FILE__).'/../portfolio_component.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object_display.class.php';

class PortfolioViewerComponent extends PortfolioComponent
{	

	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ViewPortfolio')));
		
		//$id = $_GET[MyPortfolio :: PARAM_ITEM];
		$item=$this->get_parent()->get_item_id();
		
		if ($item >= 0)
		{
			$this->publication = $this->retrieve_portfolio_publication_from_item($item);			
			$this->display_header($trail);

			$out = '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
			$components = array();
			$components[] = MyPortfolio::ACTION_VIEW;
			
			$publication = $this->publication;
			$object = $publication->get_publication_object();
			if ($object->get_owner_id() == $this->get_user_id())
			{
				 $components[]= MyPortfolio::ACTION_EDIT;
				 $components[]= MyPortfolio::ACTION_CREATE;
				 $components[]= MyPortfolio::ACTION_PROPS;
			}
			
			foreach ($components as $action)
			{
				$out .= '<li><a';
				if ($this->get_parent()->get_action() == $action) $out .= ' class="current"';
				$out .= ' href="'.$this->get_url(array (MyPortfolio :: PARAM_ACTION => $action), true).'">'.htmlentities(Translation :: get(ucfirst($action).'Title')).'</a></li>';
			}
			$out .= '</ul><div class="tabbed-pane-content">';

			$out.= $this->get_publication_as_html();

			$out .= '</div></div>';

			echo $out;

			$this->display_footer();
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoPortfolioSelected')));
		}
	}
	
	function get_publication_as_html()
	{
		$publication = $this->publication;
		$portfolio = $publication->get_publication_object();
		
		$display = LearningObjectDisplay :: factory($portfolio);
		$html = array();
		$html[] = $display->get_full_html();		
		
		return implode("\n",$html);
	}
}
?>
