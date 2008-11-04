<?php
require_once dirname(__FILE__).'/../group_manager.class.php';
require_once dirname(__FILE__).'/../group_manager_component.class.php';
require_once dirname(__FILE__).'/../../group_data_manager.class.php';
require_once dirname(__FILE__).'/group_rel_user_browser/group_rel_user_browser_table.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';
require_once Path :: get_user_path() . 'lib/user_data_manager.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';

class GroupManagerViewerComponent extends GroupManagerComponent
{
	private $group;
	private $ab;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		
		$id = $_GET[GroupManager :: PARAM_GROUP_ID];
		if ($id)
		{
			$this->group = $this->retrieve_group($id);
			$group = $this->group;
			
			if (!$this->get_user()->is_platform_admin()) 
			{
				Display :: display_not_allowed();
			}
			
			$admin = new AdminManager();
			$trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('PlatformAdmin')));
			$trail->add(new Breadcrumb($this->get_url(array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS)), Translation :: get('GroupList')));
			$trail->add(new Breadcrumb($this->get_url(array(GroupManager :: PARAM_GROUP_ID => $id)), $group->get_name()));
			
			$this->display_header($trail, false);
			$this->ab = $this->get_action_bar();
			echo $this->ab->as_html() . '<br />';
			
			echo '<div class="clear"></div><div class="learning_object" style="background-image: url('. Theme :: get_common_img_path() .'place_group.png);">';
			echo '<div class="title">'. Translation :: get('Description') .'</div>';
			echo $group->get_description();
			echo '</div>';
			
			echo '<div class="learning_object" style="background-image: url('. Theme :: get_common_img_path() .'place_users.png);">';
			echo '<div class="title">'. Translation :: get('Users') .'</div>';
			$table = new GroupRelUserBrowserTable($this, array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => $id), $this->get_condition());
			echo $table->as_html();
			echo '</div>';
			
			$this->display_footer();
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
		}
	}
	
	function get_condition()
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $_GET[GroupManager :: PARAM_GROUP_ID]);
		
		$query = $this->ab->get_query();
		
		if(isset($query) && $query != '')
		{
			$or_conditions[] = new LikeCondition(User :: PROPERTY_FIRSTNAME, $query);
			$or_conditions[] = new LikeCondition(User :: PROPERTY_LASTNAME, $query);
			$or_conditions[] = new LikeCondition(User :: PROPERTY_USERNAME, $query);
			$condition = new OrCondition($or_conditions);
			
			$users = UserDataManager :: get_instance()->retrieve_users($condition);
			while($user = $users->next_result())
			{ 
				$userconditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_USER_ID, $user->get_id());
			}
			
			if(count($userconditions))
				$conditions[] = new OrCondition($userconditions);
			else
				$conditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_USER_ID, 0);
				
		}

		$condition = new AndCondition($conditions); 
		
		return $condition;
	}
	
	function get_action_bar()
	{
		$group = $this->group;
		
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		
		$action_bar->set_search_url($this->get_url(array(GroupManager :: PARAM_GROUP_ID => $group->get_id())));
		
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_img_path().'action_browser.png', $this->get_url(array(GroupManager :: PARAM_GROUP_ID => $group->get_id())), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_img_path().'action_edit.png', $this->get_group_editing_url($group), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_img_path().'action_delete.png', $this->get_group_delete_url($group), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('AddUsers'), Theme :: get_common_img_path().'action_subscribe.png', $this->get_group_suscribe_user_browser_url($group), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		
		$condition = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $group->get_id());
		$users = $this->retrieve_group_rel_users($condition);
		$visible = ($users->size() > 0);
		
		if($visible)
		{
			$toolbar_data[] = array(
				'href' => $this->get_group_emptying_url($group),
				'label' => Translation :: get('Truncate'),
				'img' => Theme :: get_common_img_path().'action_recycle_bin.png',
				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
			);
			$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Truncate'), Theme :: get_common_img_path().'action_recycle_bin.png', $this->get_group_emptying_url($group), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}
		else
		{
			$toolbar_data[] = array(
				'label' => Translation :: get('TruncateNA'),
				'img' => Theme :: get_common_img_path().'action_recycle_bin_na.png',
				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
			);
			$action_bar->add_tool_action(new ToolbarItem(Translation :: get('TruncateNA'), Theme :: get_common_img_path().'action_recycle_bin_na.png', null, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}
		
		
		
		return $action_bar;
	}

}
?>