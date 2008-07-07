<?php
/**
 * $Id: browser.class.php 15472 2008-05-27 18:47:47Z Scara84 $
 * @package repository.repositorymanager
 * 
 * @author Bart Mollet
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__).'/../../complex_learning_object_item.class.php';
require_once dirname(__FILE__).'/../../complex_learning_object_menu.class.php';
require_once dirname(__FILE__).'/complex_browser/complex_browser_table.class.php';
/**
 * Default repository manager component which allows the user to browse through
 * the different categories and learning objects in the repository.
 */
class RepositoryManagerComplexBrowserComponent extends RepositoryManagerComponent
{
	private $cloi;
	private $root;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$cloi_id = $_GET[RepositoryManager :: PARAM_CLOI_ID];
		$root_id = $_GET[RepositoryManager :: PARAM_CLOI_ROOT_ID];
		
		if(isset($cloi_id) && isset($root_id))
		{
			$this->cloi = $this->retrieve_complex_learning_object_item($cloi_id);
			$this->root = $this->retrieve_complex_learning_object_item($root_id);
		}
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_link(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS)), Translation :: get('MyRepository')));
		
		if(!isset($cloi_id))
		{
			$this->display_header($trail, false);
			$this->display_error_message(Translation :: get('NoCLOISelected'));
			$this->display_footer();
			exit;
		}
		
		$trail->add(new Breadcrumb($this->get_link(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_LEARNING_OBJECTS, RepositoryManager :: PARAM_LEARNING_OBJECT_ID => $this->cloi->get_ref())), Translation :: get('ViewLearningObject')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ViewComplexLearningObject')));
		
		$output = $this->get_learning_objects_html();
		$menu = $this->get_menu();
		
		$this->display_header($trail, false);
		
		if($menu)
		{
			echo '<div><div style="width: 80%; float: left;">' . $output . '</div>';
			echo '<div style="width: 18%; float: right;">' . $menu->render_as_tree() . '</div>';
			echo '</div><div class="clear"></div>';
			echo '<p><img src="' . Translation :: get('AddLearningObject') . '</p>';
		}
		else
			echo $output;
			
		$this->display_footer();
	}
	/**
	 * Gets the  table which shows the learning objects in the currently active
	 * category
	 */
	private function get_learning_objects_html()
	{
		$table = new ComplexBrowserTable($this, $this->get_parameters(), $this->get_condition());
		return $table->as_html();
	}
	
	private function get_condition()
	{
		$cloi_id = $_GET[RepositoryManager :: PARAM_CLOI_ID];
		if(isset($cloi_id))
		{
			return new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $cloi_id);
		}
		return null;
	}
	
	private function get_menu()
	{
		if(isset($this->cloi) && isset($this->root))
		{
			return new ComplexLearningObjectMenu($this->root, $this->cloi);
		}
		return null;
	}
}
?>
