<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/menu_item_browser/menu_item_browser_table.class.php';
require_once dirname(__FILE__).'/../menu_manager.class.php';
require_once dirname(__FILE__).'/../menu_manager_component.class.php';
require_once dirname(__FILE__).'/../../menu_item_form.class.php';
require_once dirname(__FILE__).'/../../menu_item_menu.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

/**
 * Weblcms component allows the user to manage course categories
 */
class MenuManagerSorterComponent extends MenuManagerComponent
{
	private $category;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		global $this_section;
		$this_section='platform_admin';
		
		$trail = new BreadcrumbTrail();
        $admin = new AdminManager();
        $trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(MenuManager :: PARAM_ACTION => MenuManager :: ACTION_SORT_MENU)), Translation :: get('Menu')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('MenuSort')));

		$user = $this->get_user();
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}
		
		$this->category = $_GET[MenuManager :: PARAM_CATEGORY];
		$component_action = $_GET[MenuManager :: PARAM_COMPONENT_ACTION];
		
		switch($component_action)
		{
			case 'edit':
				$this->edit_menu_item();
				break;
			case 'delete':
				$this->delete_menu_item();
				break;
			case 'add':
				$this->add_menu_item();
				break;
			case 'move':
				$this->move_menu_item();
				break;
			default :
				$this->show_menu_item_list();
		}
	}
	
	private $action_bar;
	
	function show_menu_item_list()
	{
		$this->action_bar = $this->get_action_bar();
		
		$parameters = $this->get_parameters(true);
		
		$table = new MenuItemBrowserTable($this, $parameters, $this->get_condition());
		
		$trail = new BreadcrumbTrail();
        $admin = new AdminManager();
        $trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(MenuManager :: PARAM_ACTION => MenuManager :: ACTION_SORT_MENU)), Translation :: get('Menu')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('SortMenuManagerCategories')));
		
		$this->display_header($trail, false);
		
		echo $this->action_bar->as_html();
		
		echo '<div style="float: left; width: 12%; overflow:auto;">';
		echo $this->get_menu()->render_as_tree();
		echo '</div>';
		echo '<div style="float: right; width: 85%;">';
		echo $table->as_html();
		echo '</div>';
		$this->display_footer();
	}
	
	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		$category = (isset($this->category) ? $this->category : 0);
		$action_bar->set_search_url($this->get_url(array('category' => $category)));
		
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path().'action_create.png', $this->get_menu_item_creation_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array('category' => $category)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

		$action_bar->set_help_action(HelpManager :: get_tool_bar_help_item('menu manager'));
		return $action_bar;
	}
	
	function move_menu_item()
	{
		$direction = $_GET[MenuManager :: PARAM_DIRECTION];
		$category = $_GET[MenuManager :: PARAM_CATEGORY];
		
		if (isset($direction) && isset($category))
		{
			$move_category = $this->retrieve_menu_item($category);
			$sort = $move_category->get_sort();
			$next_category = $this->retrieve_menu_item_at_sort($move_category->get_category(), $sort, $direction);
			
			if ($direction == 'up')
			{
				$move_category->set_sort($sort-1);
				$next_category->set_sort($sort);
			}
			elseif($direction == 'down')
			{
				$move_category->set_sort($sort+1);
				$next_category->set_sort($sort);
			}
			
			if ($move_category->update() && $next_category->update())
			{
				$success = true;
			}
			else
			{
				$success = false;
			}
			
			$this->redirect('url', Translation :: get($success ? 'MenuManagerCategoryMoved' : 'MenuManagerCategoryNotMoved'), ($success ? false : true), array(MenuManager :: PARAM_COMPONENT_ACTION => MenuManager :: ACTION_COMPONENT_BROWSE_CATEGORY, MenuManager :: PARAM_CATEGORY => $move_category->get_category()));
		}
		else
		{
			$this->show_menu_item_list();
		}
	}
	
	function add_menu_item()
	{
		$menucategory = new MenuItem();
		
		$menucategory->set_application('');
		$menucategory->set_category(0);
		
		$form = new MenuItemForm(MenuItemForm :: TYPE_CREATE, $menucategory, $this->get_url(array(MenuManager :: PARAM_COMPONENT_ACTION => MenuManager :: ACTION_COMPONENT_ADD_CATEGORY)));
		
		if($form->validate())
		{
			$success = $form->create_menu_item();
			$this->redirect('url', Translation :: get($success ? 'MenuManagerCategoryAdded' : 'MenuManagerCategoryNotAdded'), ($success ? false : true), array(MenuManager :: PARAM_CATEGORY => $form->get_menu_item()->get_category()));
		}
		else
		{
			$trail = new BreadcrumbTrail();
            $admin = new AdminManager();
        $trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
			$trail->add(new Breadcrumb($this->get_url(array(MenuManager :: PARAM_ACTION => MenuManager :: ACTION_SORT_MENU)), Translation :: get('Menu')));
			$trail->add(new Breadcrumb($this->get_url(), Translation :: get('AddMenuManagerCategory')));
		
			$this->display_header($trail, false);
			echo '<div style="float: left; width: 12%; overflow:auto;">';
			echo $this->get_menu()->render_as_tree();
			echo '</div>';
			echo '<div style="float: right; width: 85%;">';
			$form->display();
			echo '</div>';
			$this->display_footer();
		}
	}
		
	function edit_menu_item()
	{
		$menucategory = $this->retrieve_menu_item($this->category);
		
		$form = new MenuItemForm(MenuItemForm :: TYPE_EDIT, $menucategory, $this->get_url(array(MenuManager :: PARAM_COMPONENT_ACTION => MenuManager :: ACTION_COMPONENT_EDIT_CATEGORY, MenuManager :: PARAM_CATEGORY => $menucategory->get_id())));
		
		if($form->validate())
		{
			$success = $form->update_menu_item();
			$this->redirect('url', Translation :: get($success ? 'MenuManagerCategoryUpdated' : 'MenuManagerCategoryNotUpdated'), ($success ? false : true), array(MenuManager :: PARAM_CATEGORY => $form->get_menu_item()->get_category()));
		}
		else
		{
			$trail = new BreadcrumbTrail();
            $admin = new AdminManager();
        $trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
			$trail->add(new Breadcrumb($this->get_url(array(MenuManager :: PARAM_ACTION => MenuManager :: ACTION_SORT_MENU)), Translation :: get('Menu')));
			$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateMenuManagerCategory')));
			
			$this->display_header($trail, false);
			echo '<div style="float: left; width: 12%; overflow:auto;">';
			echo $this->get_menu()->render_as_tree();
			echo '</div>';
			echo '<div style="float: right; width: 85%;">';
			$form->display();
			echo '</div>';
			$this->display_footer();
		}
	}
	
	function delete_menu_item()
	{
		$menu_item_id = $_GET[MenuManager :: PARAM_CATEGORY];
		$parent = 0;
		$failures = 0;
		
		if (!empty ($menu_item_id))
		{
			if (!is_array($menu_item_id))
			{
				$menu_item_id = array ($menu_item_id);
			}
			
			foreach ($menu_item_id as $id)
			{
				$category = $this->retrieve_menu_item($id);
				$parent = $category->get_category();
				
				if (!$category->delete())
				{
					$failures++;
				}
			}
			
			if ($failures)
			{
				if (count($menu_item_id) == 1)
				{
					$message = 'SelectedCategoryNotDeleted';
				}
				else
				{
					$message = 'SelectedCategoriesNotDeleted';
				}
			}
			else
			{
				if (count($menu_item_id) == 1)
				{
					$message = 'SelectedCategoryDeleted';
				}
				else
				{
					$message = 'SelectedCategoriesDeleted';
				}
			}
			
			$this->redirect('url', Translation :: get($message), ($failures ? true : false), array(MenuManager :: PARAM_COMPONENT_ACTION => MenuManager :: ACTION_COMPONENT_BROWSE_CATEGORY, MenuManager :: PARAM_CATEGORY => $parent));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoMenuManagerCategorySelected')));
		}
	}
	
	function get_condition()
	{
		$condition = null;
		$category = (isset($this->category) ? $this->category : 0);
		$condition = new EqualityCondition(MenuItem :: PROPERTY_CATEGORY, $category);
		
		$search = $this->action_bar->get_query();
		if(isset($search) && $search != '')
		{
			$conditions[] = $condition;
			$conditions[] = new LikeCondition(MenuItem :: PROPERTY_TITLE, $search);
			$condition = new AndCondition($conditions);
		}
		
		return $condition;
	}
	
	function get_menu()
	{
		if (!isset ($this->menu))
		{	
			/*$extra_items_after = array ();
			
			$create = array ();
			$create['title'] = Translation :: get('Add');
			$create['url'] = $this->get_menu_item_creation_url();
			$create['class'] = 'create';
			$extra_items_after[] = & $create;*/
			
			$temp_replacement = '__CATEGORY__';
			$url_format = $this->get_url(array(MenuManager :: PARAM_ACTION => MenuManager :: ACTION_SORT_MENU, MenuManager :: PARAM_CATEGORY => $temp_replacement));
			$url_format = str_replace($temp_replacement, '%s', $url_format);
			$this->menu = new MenuItemMenu($this->category, $url_format, null, null);
			
			$component_action = $_GET[MenuManager :: PARAM_COMPONENT_ACTION];
			
			if ($component_action == MenuManager :: ACTION_COMPONENT_ADD_CATEGORY)
			{
				$this->menu->forceCurrentUrl($this->get_menu_item_creation_url(), true);
			}
//			elseif(!isset($this->category))
//			{
//				$this->menu->forceCurrentUrl($this->get_menu_home_url(), true);
//			}
		}
		return $this->menu;
	}
	
	function get_menu_home_url()
	{
		return $this->get_url(array (MenuManager :: PARAM_ACTION => MenuManager :: ACTION_SORT_MENU));
	}
}
?>