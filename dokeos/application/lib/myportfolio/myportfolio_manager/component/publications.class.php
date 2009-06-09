<?php
/**
 * @package application.lib.portfolio.portfolio_manager
 */
//verwijst naar publications in de map vub die afgeschermd is voor vreemden

require_once dirname(__FILE__).'/../myportfolio_manager.class.php';
require_once dirname(__FILE__).'/../portfolio_component.class.php';

class PortfolioPublicationsComponent extends PortfolioComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array('portfolio_action' => null, 'item' => null)), Translation :: get('MyPortfolio')));
        $trail->add(new Breadcrumb($this->get_url(array('portfolio_action' => null)), Translation :: get('MyResearch')));
        $trail->add(new Breadcrumb($this->get_url(array('portfolio_action' => 'pf_pubs', 'user' => Request ::get('user'), 'item' => Request :: get('item'))), Translation :: get('MyPublications')));

		$item=$this->get_parent()->get_item_id();
        //willen we vub veranderen dan gaan we naar ../../install/myportfolio_installer.php
        $agency = 'vub';//Configuration :: get_instance()->get_parameter('portfolio', 'agency');
        $file = dirname(__FILE__).'/../../'.$agency.'/publications.class.php';


		if ($item >= 0)
		{
			$this->publication = $this->retrieve_portfolio_publication_from_item($item);

            if($item>1)
            $trail->add(new BreadCrumb($this->get_url(),$this->publication->get_publication_object()->get_title()));

			$this->display_header($trail);
			$out = '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
			foreach (array (MyPortfolioManager :: ACTION_PFPUBS,
														//MyPortfolioManager :: ACTION_PROPS
														) as $action)
			{
				$out .= '<li><a';
				if ($this->get_parent()->get_action() == $action) $out .= ' class="current"';
				$out .= ' href="'.$this->get_url(array (MyPortfolioManager :: PARAM_ACTION => $action), true).'">'.htmlentities(Translation :: get(ucfirst($action).'Title')).'</a></li>';
			}
			$out .= '</ul><div class="tabbed-pane-content">';

            // when their is no agency specefied it gives a blank page

            if (file_exists($file)){
            require_once $file;
                 $out.= Publications::get_publication_as_html();
            } else {
                $out.= $this -> get_publication_as_html();
            }
			

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
		//fracking dirty hack for presentation
		$userid= htmlspecialchars(Request :: get("user"));
//		echo $userid;
		$user = UserDataManager::get_instance()->retrieve_user($userid);

		$html.= "<b>Publications of ";
		//$html.= $this->get_parent()->get_owner()->get_firstname()." ".$this->get_parent()->get_owner()->get_lastname();
		$html.= $user->get_firstname()." ".$user->get_lastname();
		$html.= "</b><br /><br />";

		
		$html.= "No Publications found";

		return $html;
	}
}
?>
