<?php
/**
 * @package application.portfolio.portfolio.component
 */

require_once dirname(__FILE__).'/../portfolio_manager.class.php';
require_once dirname(__FILE__).'/../portfolio_manager_component.class.php';
require_once dirname(__FILE__).'/../../portfolio_menu.class.php';

/**
 * portfolio component which allows the user to browse his portfolio_publications
 * @author Sven Vanpoucke
 */
class PortfolioManagerViewerComponent extends PortfolioManagerComponent
{

	function run()
	{
		$user_id = Request :: get('user_id');
		$pid = Request :: get('pid');
		$cid = Request :: get('cid');
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_BROWSE)), Translation :: get('BrowsePortfolios')));
		$trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_USER_ID => $user_id)), Translation :: get('ViewPortfolio')));

		$this->display_header($trail);
		
		if($user_id == $this->get_user_id())
		{
			$this->action_bar = $this->get_action_bar();
			echo $this->action_bar->as_html();
		}
		
		echo '<div id="action_bar_browser">';
		
		echo '<div style="width: 18%; float: left; overflow: auto;">';
		$menu = new PortfolioMenu($this->get_user(), 'run.php?go=view_portfolio&application=portfolio&user_id=' . $this->get_user_id() . '&pid=%s&cid=%s', $pid, $cid);
		echo $menu->render_as_tree();
		echo '</div>';
		
		echo '<div style="width: 80%; overflow: auto;">';
		echo '</div>';
		
		echo '</div>';
	
		$this->display_footer();
	}
	
	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Create'), Theme :: get_common_image_path().'action_create.png', $this->get_create_portfolio_publication_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

		return $action_bar;
	}
}
?>