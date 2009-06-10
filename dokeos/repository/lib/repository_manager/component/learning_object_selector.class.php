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
require_once dirname(__FILE__).'/browser/repository_browser_table.class.php';
/**
 * Default repository manager component which allows the user to browse through
 * the different categories and learning objects in the repository.
 */
class RepositoryManagerLearningObjectSelectorComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */

	private $root_id;
	private $cloi_id;

	function run()
	{
		$trail = new BreadcrumbTrail();
		$cloi_id = Request :: get(RepositoryManager :: PARAM_CLOI_ID);
		$root_id = Request :: get(RepositoryManager :: PARAM_CLOI_ROOT_ID);

		$trail = new BreadcrumbTrail();
		$trail->add_help('repository general');

		if(!Request :: get('publish'))
		{
			$trail->add(new Breadcrumb($this->get_link(array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS)), Translation :: get('Repository')));
		}

		if(isset($cloi_id) && isset($root_id))
		{
			$this->cloi_id = $cloi_id;
			$this->root_id = $root_id;
		}
		else
		{
			$this->display_header($trail, false, true);
			$this->display_error_message(Translation :: get('NoCLOISelected'));
			$this->display_footer();
			exit;
		}
		$root = $this->retrieve_learning_object($root_id);
		if(!Request :: get('publish'))
		{
			$trail->add(new Breadcrumb($this->get_link(array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_LEARNING_OBJECTS, RepositoryManager :: PARAM_LEARNING_OBJECT_ID => $root_id)), $root->get_title()));
			$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_COMPLEX_LEARNING_OBJECTS, RepositoryManager :: PARAM_CLOI_ID => $cloi_id, RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id)), Translation :: get('ViewComplexLearningObject')));
			$trail->add(new Breadcrumb($this->get_url(), Translation :: get('AddExistingLearningObject')));
		}

		$output = $this->get_learning_objects_html();
		$this->display_header($trail, false, true);
		echo $output;
		$this->display_footer();
	}
	/**
	 * Gets the  table which shows the learning objects in the currently active
	 * category
	 */
	private function get_learning_objects_html()
	{
		$condition = $this->get_condition();
		$parameters = $this->get_parameters(true);
		$types = Request :: get(RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE);
		if (is_array($types) && count($types))
		{
			$parameters[RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE] = $types;
		}
		$parameters = array_merge($parameters,
			array(RepositoryManager :: PARAM_CLOI_ID => $this->get_cloi_id(),
				  RepositoryManager :: PARAM_CLOI_ROOT_ID => $this->get_root_id(), 'publish' => Request :: get('publish')));

		$table = new RepositoryBrowserTable($this, $parameters, $condition);
		return $table->as_html();
	}

	private function get_condition()
	{

		$conditions = array();
		$conditions[] = $this->get_search_condition();

		$clo = $this->retrieve_learning_object($this->cloi_id);
		$types = $clo->get_allowed_types();
		$conditions1 = array();
		foreach($types as $type)
		{
			$conditions1[] = new EqualityCondition(LearningObject :: PROPERTY_TYPE, $type);
		}
		$conditions[] = new OrCondition($conditions1);

		$conditions = array_merge($conditions, $this->retrieve_used_items($this->root_id));
		$conditions[] = new NotCondition(new EqualityCondition(LearningObject :: PROPERTY_ID, $this->root_id));

		return new AndCondition($conditions);
	}

	function get_root_id()
	{
		return $this->root_id;
	}

	function get_cloi_id()
	{
		return $this->cloi_id;
	}

	/**
	 * This function is beeing used to determine all the complex learning objects that are used in a learning object
	 * so we won't get stuck in an endless loop and returns a conditionslist to exclude the items
	 */
	function retrieve_used_items($cloi_id)
	{
		$conditions = array();

		$clois = $this->retrieve_complex_learning_object_items(new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $cloi_id));
		while($cloi = $clois->next_result())
		{
			if($cloi->is_complex())
			{
				$conditions[] = new NotCondition(new EqualityCondition(LearningObject :: PROPERTY_ID, $cloi->get_ref()));
				$conditions = array_merge($conditions, $this->retrieve_used_items($cloi->get_ref()));
			}
		}

		return $conditions;
	}
}
?>
