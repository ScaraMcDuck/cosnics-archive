<?php
require_once dirname(__FILE__).'/../class_group_manager.class.php';
require_once dirname(__FILE__).'/../class_group_manager_component.class.php';
require_once dirname(__FILE__).'/../../class_group_data_manager.class.php';
require_once dirname(__FILE__).'/class_group_rel_user_browser/class_group_rel_user_browser_table.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';
require_once Path :: get_user_path() . 'lib/users_data_manager.class.php';

class ClassGroupManagerViewerComponent extends ClassGroupManagerComponent
{
	private $classgroup;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		
		$id = $_GET[ClassGroupManager :: PARAM_CLASSGROUP_ID];
		if ($id)
		{
			$this->classgroup = $this->retrieve_classgroup($id);
			$classgroup = $this->classgroup;
			
			if (!$this->get_user()->is_platform_admin()) 
			{
				Display :: display_not_allowed();
			}
			
			$admin = new Admin();
			$trail->add(new Breadcrumb($admin->get_link(array(Admin :: PARAM_ACTION => Admin :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
			$trail->add(new Breadcrumb($this->get_url(array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_BROWSE_CLASSGROUPS)), Translation :: get('ClassGroupList')));
			$trail->add(new Breadcrumb($this->get_url(array(ClassGroupManager :: PARAM_CLASSGROUP_ID => $id)), $classgroup->get_name()));
			
			$this->display_header($trail, false);
			
			$this->display_user_search_form();
			
			echo '<div class="clear"></div><div class="learning_object" style="background-image: url('. Theme :: get_common_img_path() .'place_classgroup.png);">';
			echo '<div class="title">'. Translation :: get('Description') .'</div>';
			echo $classgroup->get_description();
			echo '</div>';
			
			echo '<div class="learning_object" style="background-image: url('. Theme :: get_common_img_path() .'place_users.png);">';
			echo '<div class="title">'. Translation :: get('Users') .'</div>';
			$table = new ClassGroupRelUserBrowserTable($this, array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_VIEW_CLASSGROUP, ClassGroupManager :: PARAM_CLASSGROUP_ID => $id), $this->get_condition());
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
		$conditions[] = new EqualityCondition(ClassGroupRelUser :: PROPERTY_CLASSGROUP_ID, $_GET[ClassGroupManager :: PARAM_CLASSGROUP_ID]);
		
		$user_search_condition = $this->get_user_search_condition();
		if($user_search_condition)
		{
			$userconditions = array();
			
			$users = UsersDataManager :: get_instance()->retrieve_users($user_search_condition);
			while($user = $users->next_result())
			{
				$userconditions[] = new EqualityCondition(ClassGroupRelUser :: PROPERTY_USER_ID, $user->get_id());
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
		
		$condition = new EqualityCondition(ClassGroupRelUser :: PROPERTY_CLASSGROUP_ID, $classgroup->get_id());
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