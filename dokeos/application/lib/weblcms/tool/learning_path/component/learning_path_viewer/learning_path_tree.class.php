<?php
/**
 * @package repository
 */
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
require_once Path :: get_repository_path(). 'lib/learning_object.class.php';
require_once Path :: get_library_path().'condition/equality_condition.class.php';
require_once Path :: get_library_path() . 'html/menu/tree_menu_renderer.class.php';
require_once Path :: get_library_path() . 'html/menu/options_menu_renderer.class.php';
/**
 * This class provides a navigation menu to allow a user to browse through his
 * categories of learning objects.
 * @author Sven Vanpoucke
 */
class LearningPathTree extends HTML_Menu
{
	
	private $current_step;
	private $lp_id;
	private $lpi_tracker_data;
	
	/**
	 * The string passed to sprintf() to format category URLs
	 */
	private $urlFmt;

	private $current_object;
	private $current_cloi;
	private $current_tracker;
	private $objects = array();
	
	private $dm;
	/**
	 * Creates a new category navigation menu.
	 * @param int $owner The ID of the owner of the categories to provide in
	 * this menu.
	 * @param int $current_category The ID of the current category in the menu.
	 * @param string $url_format The format to use for the URL of a category.
	 *                           Passed to sprintf(). Defaults to the string
	 *                           "?category=%s".
	 * @param array $extra_items An array of extra tree items, added to the
	 *                           root.
	 */
	function LearningPathTree($lp_id, $current_step, $url_format, $lpi_tracker_data)
	{
		$this->current_step = $current_step;
		$this->lp_id = $lp_id;
		$this->urlFmt = $url_format;
		$this->lpi_tracker_data = $lpi_tracker_data;
		
		$this->dm = RepositoryDataManager :: get_instance();
		$menu = $this->get_menu($lp_id);
		parent :: __construct($menu);
		$this->array_renderer = new HTML_Menu_ArrayRenderer();
		
		
		if(!$current_step)
		{
			$this->forceCurrentUrl($this->get_progress_url());
		}
		else
		{
			$this->forceCurrentUrl($this->get_url($current_step));
		}
	}
	
	function get_menu($lp_id)
	{
		$menu = array();
		$datamanager = $this->dm;
		$lo = $datamanager->retrieve_learning_object($lp_id);
		$lp_item = array();
		$lp_item['title'] = $lo->get_title();
		//$menu_item['url'] = $this->get_url($lp_id);
	
		$sub_menu_items = $this->get_menu_items($lo);
		if(count($sub_menu_items) > 0)
		{
			$lp_item['sub'] = $sub_menu_items;
		}
		$lp_item['class'] = 'type_' . $lo->get_type();
		//$menu_item['class'] = 'type_category';
		$lp_item[OptionsMenuRenderer :: KEY_ID] = -1;
		
		
		$menu_item = array();
		$menu_item['title'] = Translation :: get('Progress');
		$menu_item['url'] = $this->get_progress_url();
		$menu_item['class'] = 'type_statistics';
		$menu_item[OptionsMenuRenderer :: KEY_ID] = $this->step;
		$lp_item['sub'] = array_merge($lp_item['sub'],array($menu_item));
		
		$menu[] = $lp_item;
		
		return $menu;
	}
	
	/**
	 * Returns the menu items.
	 * @param array $extra_items An array of extra tree items, added to the
	 *                           root.
	 * @return array An array with all menu items. The structure of this array
	 *               is the structure needed by PEAR::HTML_Menu, on which this
	 *               class is based.
	 */
	 
	private $step = 1;
	private $step_urls = array();
	
	private function get_menu_items($parent)
	{
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $parent->get_id());
		$datamanager = $this->dm;
		$objects = $datamanager->retrieve_complex_learning_object_items($condition);
		
		while (($object = $objects->next_result()))
		{
			$lo = $datamanager->retrieve_learning_object($object->get_ref());
			$lpi_tracker_data = $this->lpi_tracker_data[$object->get_id()];
			
			if($lo->get_type() == 'learning_path_item')
			{
				$lo = $datamanager->retrieve_learning_object($lo->get_reference());
			}
			
			$menu_item = array();
			$menu_item['title'] = $lo->get_title();
			$menu_item['class'] = 'type_' . $lo->get_type();
			$menu_item[OptionsMenuRenderer :: KEY_ID] = -1;
			
			$sub_menu_items = array();
			
			if($lo->get_type() == 'learning_path')
				$sub_menu_items = $this->get_menu_items($lo);
			
			$control_mode = $parent->get_control_mode();	
			
			if(count($sub_menu_items) > 0)
			{
				$menu_item['sub'] = $sub_menu_items;
			}
			else
			{	
				$this->objects[$object->get_id()] = $lo;
				if($lpi_tracker_data['completed'])
				{
					$menu_item['title'] = $menu_item['title'] . Theme :: get_common_image('status_ok_mini');
					$this->taken_steps++;
				}
				
				if($control_mode['choice'] != 0)
				{
					$menu_item['url'] = $this->get_url($this->step);
					$menu_item[OptionsMenuRenderer :: KEY_ID] = $this->step;	
				}
				
				if($this->step == $this->current_step)
				{
					$this->current_cloi = $object;
					$this->current_object = $lo;
					$this->current_tracker = $lpi_tracker_data['active_tracker'];
				}
				
				if($this->check_condition_rules($lo, $lpi_tracker_data))
				{
					$this->step_urls[$this->step] = $this->get_url($this->step);
				}
				else
				{
					$this->step_urls[$this->step] = null;
				}
				
				$this->step++;
				
			}

			$menu[] = $menu_item;
		}
		
		return $menu;
	}
	
	function get_continue_url()
	{
		$step = $this->current_step + 1;
		while($this->step_urls[$step] == null && $step <= $this->count_steps())
		{
			$step++;
		}
		
		if($step <= $this->count_steps())
		{
			return $this->step_urls[$step];
		}
		
		return $this->get_progress_url();
	}
	
	function get_previous_url()
	{
		$step = $this->current_step - 1;
		while($this->step_urls[$step] == null && $step > 0)
		{
			$step--;
		}
		
		if($step > 0)
		{
			return $this->step_urls[$step];
		}
		
		return null;
	}
	
	function check_condition_rules($object, $tracker_data)
	{
		if(get_class($object) != 'ScormItem')
			return true;
		
		if(($rules = $object->get_condition_rules()) == null)
			return true;
		
		if(($objectives = $object->get_objectives()) != null)
		{
			if(($primary_objective = $objectives->get_primary_objective()) == null)
			{
				$objective_trackers = null;
			}
			else 
			{
				$ids = array();
				foreach($tracker_data['trackers'] as $tracker)
					$ids[] = $tracker->get_id();
				
				if(count($ids) == 0)
				{
					$objective_trackers = null;
				}
				else 
				{
					$conditions[] = new InCondition(WeblcmsLpiAttemptObjectiveTracker :: PROPERTY_LPI_VIEW_ID, $ids);		
					$conditions[] = new EqualityCondition(WeblcmsLpiAttemptObjectiveTracker :: PROPERTY_OBJECTIVE_ID, $primary_objective->get_id());
					$condition = new AndCondition($conditions);
					$dummy = new WeblcmsLpiAttemptObjectiveTracker();
					$objective_trackers = $dummy->retrieve_tracker_items($condition);
					
				}
			}
		}
		else 
		{
			$objective_trackers = null;
		}
	
		$pre_condition_rules = $rules->get_precondition_rules();
		foreach($pre_condition_rules as $pre_condition_rule)
		{
			//$action = $pre_condition_rule->get_action();
			$rules = $pre_condition_rule->get_conditions();

			foreach($rules as $rule)
			{
				switch($rule)
				{
					case "satisfied":
						if(is_array($objective_trackers))
						{
							foreach($objective_trackers as $objective_tracker)
							{
								if($objective_tracker->get_status() == 'completed')
									return false;
							}
						}
						else
						{ 
							foreach($tracker_data['trackers'] as $tracker)
							{ 
								if($tracker->get_status() == 'completed')
									return false;
							}
						}
				}
			}
			
		}
		
		return true;
		
	}
	
	function get_objects()
	{
		return $this->objects;
	}
	
	function get_current_object()
	{
		return $this->current_object;
	}
	
	function get_current_cloi()
	{
		return $this->current_cloi;
	}
	
	function get_current_tracker()
	{
		return $this->current_tracker;
	}

	private function get_url($current_step)
	{
		return sprintf($this->urlFmt, $current_step);
	}
	
	private function get_progress_url()
	{
		return str_replace('&step=%s','', $this->urlFmt) . '&lp_action=view_progress';
	}
	
	/**
	 * Renders the menu as a tree
	 * @return string The HTML formatted tree
	 */
	function render_as_tree()
	{
		$renderer = new TreeMenuRenderer();
		$this->render($renderer, 'sitemap'); 
		return $renderer->toHTML();
	}
	
	function get_progress()
	{
		return ($this->taken_steps / ($this->count_steps())) * 100;
	}
	
	function count_steps()
	{
		return $this->step - 1;
	}
	
	function get_breadcrumbs()
	{
		$this->render($this->array_renderer, 'urhere');
		$breadcrumbs = $this->array_renderer->toArray();
		$trail = new BreadcrumbTrail(false);
		$used_urls = array();
		foreach ($breadcrumbs as $crumb)
		{
			if(!in_array($crumb['url'], $used_urls))
			{
				$trail->add(new Breadcrumb($crumb['url'], strip_tags($crumb['title'])));
				$used_urls[] = $crumb['url'];
			}
		}
		return $trail;
	}
}