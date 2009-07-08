<?php
/**
 * @package application.portfolio.portfolio.component
 */

require_once dirname(__FILE__).'/../portfolio_manager.class.php';
require_once dirname(__FILE__).'/../portfolio_manager_component.class.php';
require_once dirname(__FILE__).'/../../portfolio_menu.class.php';
require_once dirname(__FILE__).'/../../forms/portfolio_publication_form.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';

/**
 * portfolio component which allows the user to browse his portfolio_publications
 * @author Sven Vanpoucke
 */
class PortfolioManagerViewerComponent extends PortfolioManagerComponent
{
	private $action_bar;
	private $selected_object;
	private $publication;
	private $portfolio_item;
	private $cid;
	private $pid;
	
	function run()
	{
		$user_id = Request :: get('user_id');
		$pid = Request :: get('pid');
		$this->pid = $pid;
		$cid = Request :: get('cid');
		$this->cid = $cid;
		
		$rdm = RepositoryDataManager :: get_instance();
		
		if($pid && $cid)
		{
			$wrapper = $rdm->retrieve_complex_learning_object_item($cid);
			$this->selected_object = $rdm->retrieve_learning_object($wrapper->get_ref());
			
			if($this->selected_object->get_type() == 'portfolio_item')
			{
				$this->portfolio_item = $this->selected_object;
				$this->selected_object = $rdm->retrieve_learning_object($this->selected_object->get_reference());
			}
		}
		elseif($pid && !$cid)
		{
			$publication = PortfolioDataManager :: get_instance()->retrieve_portfolio_publication($pid);
			$this->publication = $publication;
			$this->selected_object = $rdm->retrieve_learning_object($publication->get_learning_object());
		}
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_BROWSE)), Translation :: get('BrowsePortfolios')));
		$trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_USER_ID => $user_id)), Translation :: get('ViewPortfolio')));

		$this->display_header($trail);
		
		if($user_id == $this->get_user_id())
		{
			$this->action_bar = $this->get_action_bar();
			echo $this->action_bar->as_html();
			
			$actions = array('view');
			
			if($pid && !$cid)
			{
				$actions[] = 'edit';
				$actions[] = 'properties';
			}
			
			if($cid)
				$actions[] = 'edit';
			
		}
		else 
		{
			$actions = array('view');
		}
		
		echo '<div id="action_bar_browser">';
		
		echo '<div style="width: 18%; float: left; overflow: auto;">';
		$menu = new PortfolioMenu($this->get_user(), 'run.php?go=view_portfolio&application=portfolio&user_id=' . $this->get_user_id() . '&pid=%s&cid=%s', $pid, $cid);
		echo $menu->render_as_tree();
		echo '</div>';
		
		echo '<div style="width: 80%; overflow: auto;">';
		echo '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
		
		$current_action = Request :: get('action') ? Request :: get('action') : 'view';
		
		foreach ($actions as $action)
		{
			echo '<li><a';
			if ($action == $current_action)
			{
				echo ' class="current"';
			}
			echo ' href="'.$this->get_url(array('pid' => $pid, 'cid' => $cid, 'user_id' => $user_id, 'action' => $action)).'">'.htmlentities(Translation :: get(ucfirst($action).'Title')).'</a></li>';
		}
		echo '</ul><div class="tabbed-pane-content">';
	
		echo call_user_func(array($this, 'display_' . $current_action . '_page'));
		
		echo '</div></div>';
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
	
	function display_view_page()
	{
		$html = array();
	
		if($this->selected_object)
		{ 
			$display = LearningObjectDisplay :: factory($this->selected_object);
			$html[] = $display->get_full_html();
		}
		else
		{
			$html[] = Translation :: get('PortfolioIntroduction');
		}
		
		return implode("\n", $html);
	}
	
	function display_edit_page()
	{
		$html = array();

		$allow_new_version = ($this->selected_object->get_type() != 'portfolio');
		
		$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_EDIT, $this->selected_object, 'learning_object_form', 'post', $this->get_url(array('user_id' => $this->get_user_id(), 'pid' => $this->pid, 'cid' => $this->cid, 'action' => 'edit')), null, null, $allow_new_version);
		
		if($form->validate())
		{
			$success = $form->update_learning_object();
			
			if($form->is_version())
			{
				$object = $form->get_learning_object();
				if($this->publication)
				{
					$this->publication->set_learning_object($object->get_latest_version()->get_id());
	                $this->publication->update(false);
				}
				else 
				{
					$this->portfolio_item->set_reference($object->get_latest_version()->get_id());
					$this->portfolio_item->update();
				}
			}
		
			$this->redirect($success ? Translation :: get('PortfolioUpdated') : Translation :: get('PortfolioNotUpdated'), !$success, array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_USER_ID => $this->get_user_id(), 'pid' => $this->pid, 'cid' => $this->cid));
		}
		else
		{
			$html[] = $form->display();
		}
		
		return implode("\n", $html);
	}
	
	function display_properties_page()
	{
		$html = array();

		$form = new PortfolioPublicationForm(PortfolioPublicationForm :: TYPE_EDIT, $this->publication, $this->get_url(array('user_id' => $this->get_user_id(), 'pid' => $this->pid, 'cid' => $this->cid, 'action' => 'properties')), $this->get_user());
		
		if($form->validate())
		{
			$success = $form->update_portfolio_publication();
			$this->redirect($success ? Translation :: get('PortfolioPropertiesUpdated') : Translation :: get('PortfolioPropertiesNotUpdated'), !$success, array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_USER_ID => $this->get_user_id(), 'pid' => $this->pid, 'cid' => $this->cid));
		}
		else
		{
			$html[] = $form->display();
		}
		
		return implode("\n", $html);
	}
}
?>