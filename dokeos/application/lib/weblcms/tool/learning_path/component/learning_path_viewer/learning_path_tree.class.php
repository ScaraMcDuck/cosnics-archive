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
	private $lp_tracker;
	
	/**
	 * The string passed to sprintf() to format category URLs
	 */
	private $urlFmt;

	private $current_object;
	private $current_cloi;
	private $current_tracker;
	private $taken_steps = 0;
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
	function LearningPathTree($lp_id, $current_step, $url_format, $lp_tracker)
	{
		$this->current_step = $current_step;
		$this->lp_id = $lp_id;
		$this->urlFmt = $url_format;
		$this->lp_tracker = $lp_tracker;
		
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
		
		$lp_tracker->set_progress($this->get_progress());
		$lp_tracker->update();
	}
	
	function get_menu($lp_id)
	{
		$menu = array();
		$datamanager = $this->dm;
		$lo = $datamanager->retrieve_learning_object($lp_id);
		$lp_item = array();
		$lp_item['title'] = $lo->get_title();
		//$menu_item['url'] = $this->get_url($lp_id);
	
		$sub_menu_items = $this->get_menu_items($lp_id);
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
	 
	private function get_menu_items($parent)
	{
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $parent);
		$datamanager = $this->dm;
		$objects = $datamanager->retrieve_complex_learning_object_items($condition);
		
		while (($object = $objects->next_result()))
		{
			$lo = $datamanager->retrieve_learning_object($object->get_ref());
			
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
				$sub_menu_items = $this->get_menu_items($object->get_ref());
			
			if(count($sub_menu_items) > 0)
			{
				$menu_item['sub'] = $sub_menu_items;
			}
			else
			{
				$menu_item['url'] = $this->get_url($this->step);
				$menu_item[OptionsMenuRenderer :: KEY_ID] = $this->step;
				
				$conditions = array();
				$conditions[] = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_LP_VIEW_ID, $this->lp_tracker->get_id());
				$conditions[] = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_LP_ITEM_ID, $object->get_id());
				$condition = new AndCondition($conditions);
				
				$dummy = new WeblcmsLpiAttemptTracker();
				$trackers = $dummy->retrieve_tracker_items($condition);
				$lpi_tracker = $trackers[0];
				
				if($lpi_tracker && $lpi_tracker->get_status() == 'completed')
				{
					$menu_item['title'] = $menu_item['title'] . ' <span style="position: relative; top: 3px;">' . 
										  Theme :: get_common_image('status_ok_mini') . '</span> ' ;
					$this->taken_steps++;
				}
				
				if($this->step == $this->current_step)
				{
					$this->current_cloi = $object;
					$this->current_object = $lo;
					$this->current_tracker = $lpi_tracker;
				}
				
				$this->objects[] = array('object' => $lo, 'tracker' => $lpi_tracker);
				
				$this->step++;
			}

			$menu[] = $menu_item;
		}
		
		return $menu;
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
		return htmlentities(sprintf($this->urlFmt, $current_step));
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
	
	function count_steps()
	{
		return $this->step - 1;
	}
	
	function get_progress()
	{
		return ($this->taken_steps / ($this->step - 1)) * 100;
	}
	
	function get_breadcrumbs()
	{
		$this->render($this->array_renderer, 'urhere');
		$breadcrumbs = $this->array_renderer->toArray();
		$trail = new BreadcrumbTrail(false);
		foreach ($breadcrumbs as $crumb)
		{
			$trail->add(new Breadcrumb($crumb['url'], strip_tags($crumb['title'])));
		}
		return $trail;
	}
}