<?php
/**
 * @package application.lib.portfolio.portfolio_manager
 */
require_once dirname(__FILE__).'/../myportfolio_manager.class.php';
require_once dirname(__FILE__).'/../portfolio_component.class.php';
require_once dirname(__FILE__).'/../../portfolio_publisher.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/learning_object_form.class.php';

class PortfolioEditorComponent extends PortfolioComponent
{	

	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array('portfolio_action' => null, 'item' => null)), Translation :: get('MyPortfolio')));
		$item=$this->get_parent()->get_item_id();
		
		if ($item >= -1)
		{
			$this->publication = $this->retrieve_portfolio_publication_from_item($item);
			$this->display_header($trail);
			//$breadcrumbs = array();
			//$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get('ViewPortfolio') . ': ' . $this->publication->get_publication_publisher()->get_username());
			
			$out = '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
			foreach (array (MyPortfolioManager :: ACTION_VIEW, MyPortfolioManager :: ACTION_EDIT,MyPortfolioManager :: ACTION_CREATE, MyPortfolioManager :: ACTION_PROPS) as $action)
			{
				$out .= '<li><a';
				if ($this->get_parent()->get_action() == $action) $out .= ' class="current"';
				$out .= ' href="'.$this->get_url(array (MyPortfolioManager :: PARAM_ACTION => $action), true).'">'.htmlentities(Translation :: get(ucfirst($action).'Title')).'</a></li>';
			}
			$out .= '</ul><div class="tabbed-pane-content">';
			
			$publication = $this->publication;
			$object = $publication->get_publication_object();

			if ($object->get_owner_id() != $this->get_user_id())
			{
				$this->not_allowed();
			}
			/*elseif (!$object->is_latest_version())
			{
				//$this->redirect(MyPortfolioManager :: ACTION_VIEW, get('EditNotAllowed'), $object->get_parent_id(), true);
			}
			*/
			$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_EDIT, $object, 'edit', 'post', $this->get_url(array (MyPortfolioManager :: PARAM_ITEM => $item)));
			if ($form->validate())
			{
				$success = $form->update_learning_object();
				//$category_id = $object->get_parent_id();
				//$this->redirect(MyPortfolioManager :: ACTION_VIEW, Translation :: get($success == LearningObjectForm :: RESULT_SUCCESS ? 'ObjectUpdated' : 'ObjectUpdateFailed'), $category_id);
				$this->redirect(null, Translation :: get($success == LearningObjectForm :: RESULT_SUCCESS ? 'ObjectUpdated' : 'ObjectUpdateFailed'), false, array(MyPortfolioManager :: PARAM_ACTION => MyPortfolioManager :: ACTION_VIEW, MyPortfolioManager :: PARAM_ITEM => $item));
			}
			else
			{
				$trail->add(new BreadCrumb($this->get_url(array('portfolio_action' => 'pf_item_view')), $this->publication->get_publication_object()->get_title()));
				$trail->add(new BreadCrumb($this->get_url(), Translation :: get('Edit')));
                
				
				echo $out;
				$form->display();
				//$this->display_footer();
				echo '</div></div>';
				
			}
			$this->display_footer();
			
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoPortfolioSelected')));
		}
	}
	
	function get_editor_as_html()
	{
		$publication = $this->publication;
		$object = $publication->get_publication_object();
		
		$id = Request :: get(MyPortfolioManager :: PARAM_ITEM);
		if (true)
		{
			// TODO: Roles & Rights.
			if ($object->get_owner_id() != $this->get_user_id())
			{
				$this->not_allowed();
			}
			elseif (!$object->is_latest_version())
			{
				$this->redirect(MyPortfolioManager :: ACTION_VIEW, Translation :: get('EditNotAllowed'), $object->get_parent_id(), true);
			}
			$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_EDIT, $object, 'edit', 'post', $this->get_url(array (MyPortfolioManager :: PARAM_ITEM => $id)));
			if ($form->validate())
			{
				$success = $form->update_learning_object();
				//$category_id = $object->get_parent_id();
				//$this->redirect(MyPortfolioManager :: ACTION_VIEW, get($success == LearningObjectForm :: RESULT_SUCCESS ? 'ObjectUpdated' : 'ObjectUpdateFailed'), $category_id);
				$this->redirect(null, Translation :: get($success == LearningObjectForm :: RESULT_SUCCESS ? 'ObjectUpdated' : 'ObjectUpdateFailed'), false, array(MyPortfolioManager :: PARAM_ACTION => MyPortfolioManager :: ACTION_VIEW, MyPortfolioManager :: PARAM_ITEM => $id));
			}
			else
			{
				$breadcrumbs = array(array('url' => $this->get_url(), 'name' => Translation :: get('Edit')));
				//$this->display_header($breadcrumbs);
				$form->display();
				//$this->display_footer();
			}
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
		}
		
	}
	
}
?>