<?php
require_once dirname(__FILE__).'/../group_manager.class.php';
require_once dirname(__FILE__).'/../group_manager_component.class.php';
require_once dirname(__FILE__).'/../../group_data_manager.class.php';
require_once dirname(__FILE__).'/group_rel_user_browser/group_rel_user_browser_table.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';
require_once Path :: get_user_path() . 'lib/user_data_manager.class.php';

class GroupManagerViewerComponent extends GroupManagerComponent
{
	private $classgroup;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		
		$id = $_GET[GroupManager :: PARAM_GROUP_ID];
		if ($id)
		{
			$this->classgroup = $this->retrieve_classgroup($id);
			$classgroup = $this->classgroup;
			
			if (!$this->get_user()->is_platform_admin()) 
			{
				Display :: display_not_allowed();
			}
			
			$admin = new AdminManager();
			$trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
			$trail->add(new Breadcrumb($this->get_url(array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS)), Translation :: get('GroupList')));
			$trail->add(new Breadcrumb($this->get_url(array(GroupManager :: PARAM_GROUP_ID => $id)), $classgroup->get_name()));
			
			$this->display_header($trail, false);
			
			$this->display_user_search_form();
			
			echo '<div class="clear"></div><div class="learning_object" style="background-image: url('. Theme :: get_common_img_path() .'place_classgroup.png);">';
			echo '<div class="title">'. Translation :: get('Description') .'</div>';
			echo $classgroup->get_description();
			echo '</div>';
			
			echo '<div class="learning_object" style="background-image: url('. Theme :: get_common_img_path() .'place_users.png);">';
			echo '<div class="title">'. Translation :: get('Users') .'</div>';
			$table = new GroupRelUserBrowserTable($this, array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => $id), $this->get_condition());
			echo $table->as_html();
			echo '</div>';
			
			echo $this->build_toolbar();
			
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
		
		$user_search_condition = $this->get_user_search_condition();
		if($user_search_condition)
		{
			$userconditions = array();
			
			$users = UserDataManager :: get_instance()->retrieve_users($user_search_condition);
			while($user = $users->next_result())
			{
				$userconditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_USER_ID, $user->get_id());
			}
			
			if(count($userconditions))
				$conditions[] = new OrCondition($userconditions);
				
		}

		$condition = new AndCondition($conditions);
		
		return $condition;
	}
	
	function build_toolbar()
	{
		$classgroup = $this->classgroup;
		$toolbar_data = array();

		$toolbar_data[] = array(
			'href' => $this->get_classgroup_editing_url($classgroup),
			'label' => Translation :: get('Edit'),
			'img' => Theme :: get_common_img_path().'action_edit.png',
			'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
		);
		
		$toolbar_data[] = array(
			'href' => $this->get_classgroup_suscribe_user_browser_url($classgroup),
			'label' => Translation :: get('AddUsers'),
			'img' => Theme :: get_common_img_path().'action_subscribe.png',
			'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
		);
		
		$condition = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $classgroup->get_id());
		$users = $this->retrieve_classgroup_rel_users($condition);
		$visible = ($users->size() > 0);
		
		if($visible)
		{
			$toolbar_data[] = array(
				'href' => $this->get_classgroup_emptying_url($classgroup),
				'label' => Translation :: get('Truncate'),
				'img' => Theme :: get_common_img_path().'action_recycle_bin.png',
				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
			);
		}
		else
		{
			$toolbar_data[] = array(
				'label' => Translation :: get('TruncateNA'),
				'img' => Theme :: get_common_img_path().'action_recycle_bin_na.png',
				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
			);
		}
		
		return DokeosUtilities :: build_toolbar($toolbar_data, array(), 'margin-top: 1em;');
	}
}
?>