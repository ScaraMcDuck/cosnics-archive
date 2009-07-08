<?php
/**
 * @package application.lib.portfolio.portfolio_manager
 */
require_once dirname(__FILE__).'/../myportfolio_manager.class.php';
require_once dirname(__FILE__).'/../portfolio_component.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object_display.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object/feedback/feedback_form.class.php';

class PortfolioViewerComponent extends PortfolioComponent
{	

	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array('portfolio_action' => null, 'item' => null)), Translation :: get('MyPortfolio')));
		//$trail->add(new Breadcrumb($this->get_url(), ))
		//$id = Request :: get(MyPortfolioManager :: PARAM_ITEM);
		$item=$this->get_parent()->get_item_id();

		if ($item >= 0)
		{
			$this->publication = $this->retrieve_portfolio_publication_from_item($item);
            if ($item > 1)
            $trail->add(new BreadCrumb($this->get_url(), $this->publication->get_publication_object()->get_title()));
			$this->display_header($trail);

			$out = '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
			$components = array();
			$components[] = MyPortfolioManager :: ACTION_VIEW;
			
			$publication = $this->publication;
			$object = $publication->get_publication_object();

            $trail->add(new Breadcrumb($this->get_url(),$object->get_title() ));
            $this->display_header($trail);

			if ($object->get_owner_id() == $this->get_user_id())
			{
				 $components[]= MyPortfolioManager :: ACTION_EDIT;
				 $components[]= MyPortfolioManager :: ACTION_CREATE;
				 $components[]= MyPortfolioManager :: ACTION_PROPS;
			}
			
			foreach ($components as $action)
			{
				$out .= '<li><a';
				if ($this->get_parent()->get_action() == $action) $out .= ' class="current"';
				$out .= ' href="'.$this->get_url(array (MyPortfolioManager :: PARAM_ACTION => $action), true).'">'.htmlentities(Translation :: get(ucfirst($action).'Title')).'</a></li>';
			}
			$out .= '</ul><div class="tabbed-pane-content">';

			$out.= $this->get_publication_as_html();

           // $out.= $this->show_feedback();

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

    function show_feedback(){
        $fb = new Feedback();
        $form = FeedbackForm :: factory('create', $fb, 'feedback', null);
       return  $form->display();
    }
}
?>
