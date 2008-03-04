<?php
/**
 * @package application.lib.portfolio.portfolio_manager
 */
require_once dirname(__FILE__).'/../myportfolio.class.php';
require_once dirname(__FILE__).'/../portfoliocomponent.class.php';
require_once Path :: get_repository_path(). 'lib/repositoryutilities.class.php';
require_once Path :: get_repository_path(). 'lib/learningobjectdisplay.class.php';

class PortfolioViewerComponent extends PortfolioComponent
{	
	private $folder;
	private $publication;
	private $object;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get_lang('ViewProfile'));
		
		//$id = $_GET[MyPortfolio :: PARAM_ITEM];
		$item=$this->get_parent()->get_item_id();
		
		if ($item)
		{
			$this->publication = $this->retrieve_portfolio_publication_from_item($item);			
			
			$breadcrumbs = array();
			$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get_lang('ViewPortfolio') . ': ' . $this->publication->get_publication_publisher()->get_username());
			
			$this->display_header($breadcrumbs);

			$out = '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
			foreach (array (MyPortfolio::ACTION_VIEW, MyPortfolio::ACTION_CREATE,MyPortfolio::ACTION_EDIT,MyPortfolio::ACTION_PROPS,MyPortfolio::ACTION_SHARING, MyPortfolio::ACTION_STATE) as $action)
			{
				$out .= '<li><a';
				if ($this->get_parent()->get_action() == $action) $out .= ' class="current"';
				$out .= ' href="'.$this->get_url(array (MyPortfolio :: PARAM_ACTION => $action), true).'">'.htmlentities(Translation :: get_lang(ucfirst($action).'Title')).'</a></li>';
			}
			$out .= '</ul><div class="tabbed-pane-content">';



//			print '<a href="'.$this->get_url(array (MyPortfolio :: PARAM_ACTION => MyPortfolio::ACTION_CREATE, MyPortfolio :: PARAM_ITEM => $item), true).'">'.Translation :: get_lang("pf_create_child").'</a>';
//			print '<a href="'.$this->get_url(array (MyPortfolio :: PARAM_ACTION => MyPortfolio::ACTION_EDIT, MyPortfolio :: PARAM_ITEM => $item), true).'">'.Translation :: get_lang("pf_edit_item").'</a>';
//			print '<a href="'.$this->get_url(array (MyPortfolio :: PARAM_ACTION => MyPortfolio::ACTION_DELETE, MyPortfolio :: PARAM_ITEM => $item), true).'">'.Translation :: get_lang("pf_delete_item").'</a><br /><br />';

			$out.= $this->get_publication_as_html();

			$out .= '</div></div>';
			echo $out;
			$this->display_footer();
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get_lang('NoPortfolioSelected')));
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