<?php
/**
 * @package user.usermanager
 */
require_once dirname(__FILE__).'/../rights_manager.class.php';
require_once dirname(__FILE__).'/../rights_manager_component.class.php';
require_once dirname(__FILE__).'/../../rights_data_manager.class.php';
require_once dirname(__FILE__).'/../../rights_utilities.class.php';

class RightsManagerEditorComponent extends RightsManagerComponent
{
	private $application;
	private $location;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$this->application = Request :: get('application');		
		$this->location = Request :: get('location');
		$component_action = $_GET[RightsManager :: PARAM_COMPONENT_ACTION];
		
		switch($component_action)
		{
			case 'edit':
				$this->edit_right();
				break;
			default :
				$this->show_rights_list();
		}
	}
	
	function edit_right()
	{
		$role_id = $_GET['role_id'];
		$right_id = $_GET['right_id'];
		$location_id =  $this->location_id;
		
		if (isset($role_id) && isset($right_id) && isset($location_id))
		{
			$rolerightlocation = $this->retrieve_role_right_location($right_id, $role_id, $location_id);
			$value = $rolerightlocation->get_value();
			if ($value == 0)
			{
				$rolerightlocation->set_value('1');
			}
			else
			{
				$rolerightlocation->set_value('0');
			}
			$success = $rolerightlocation->update();
			
			$this->redirect('url', Translation :: get($success == true ? 'RightUpdated' : 'RightUpdateFailed'), ($success == true ? false : true), array(RightsManager :: PARAM_ACTION => RightsManager :: ACTION_EDIT_RIGHTS, 'location_id' =>$location_id));
		}
	}
	
	function show_rights_list()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(RightsManager :: PARAM_ACTION => RightsManager :: ACTION_EDIT_RIGHTS)), Translation :: get('RolesAndRights')));
		$trail->add(new Breadcrumb($this->get_url(array(RightsManager :: PARAM_ACTION => RightsManager :: ACTION_EDIT_RIGHTS)), Translation :: get('EditRights')));
		
		if (!isset($this->application) && !isset($this->location))
		{
			$this->display_header($trail);
			$this->display_warning_message(Translation :: get('SelectApplication'));
		}
		else
		{
			if (!isset($this->application))
			{
				$this->application = 'admin';
			}
			
			$rights_tree = RightsUtilities :: get_tree($this->application);
			
			if (!isset($this->location))
			{
				$root = $rights_tree->getRoot();
				$this->location = $root['id'];
			}
			
			$parents = $rights_tree->getParents($this->location);
			
			foreach($parents as $parent)
			{
				$trail->add(new Breadcrumb($this->get_url(array('location' => $parent['id'])), $parent['name']));
			}
			
			$this->display_header($trail);
			echo $this->get_rights_table_html();
			echo $this->get_relations();
		}
		
		$this->display_footer();
	}
	
	function get_rights_table_html()
	{
		$application = $this->application;
		$location = $this->retrieve_location($this->location);
		
		$base_path = (Application :: is_application($application) ? (Path :: get_application_path() . 'lib/' . $application . '/') : (Path :: get(SYS_PATH). $application . '/lib/'));
		$class = $application . '_rights.class.php';
		$file = $base_path . $class;
		
		require_once($file);
		
		// TODO: When PHP 5.3 gets released, replace this by $class :: get_available_rights()
	    $reflect = new ReflectionClass(Application :: application_to_class($application) . 'Rights');
	    $rights = $reflect->getConstants();
	    // TODO: When PHP 5.3 gets released, replace this by $class :: get_available_rights()
	    
		$rights_array = array();
		
		$html = array();
		
		$html[] = DokeosUtilities :: add_block_hider();
		$html[] = DokeosUtilities :: build_block_hider('rights_legend');
		$html[] = '<div class="learning_object" style="background-image: url('.Theme :: get_common_image_path().'place_legend.png);">';
		$html[] = '<div class="title">'. Translation :: get('Legend') .'</div>';
		$html[] = '<ul class="rights_legend">';
		$html[] = '<li>'. Theme :: get_common_image('action_setting_true', 'png', Translation :: get('True')) .'</li>';
		$html[] = '<li>'. Theme :: get_common_image('action_setting_false', 'png', Translation :: get('False')) .'</li>';
		$html[] = '<li>'. Theme :: get_common_image('action_setting_true_locked', 'png', Translation :: get('LockedTrue')) .'</li>';
		$html[] = '<li>'. Theme :: get_common_image('action_setting_false_locked', 'png', Translation :: get('LockedFalse')) .'</li>';
		$html[] = '<li>'. Theme :: get_common_image('action_setting_true_inherit', 'png', Translation :: get('InheritedTrue')) .'</li>';
		$html[] = '<li>'. Theme :: get_common_image('action_setting_false_inherit', 'png', Translation :: get('InheritedFalse')) .'</li>';
		$html[] = '</ul>';
		$html[] = '</div>';
		$html[] = DokeosUtilities :: build_block_hider();
		
		$html[] = '<div style="margin-bottom: 10px;">';
		$html[] = '<div style="padding: 5px; border-bottom: 1px solid #DDDDDD;">';
		$html[] = '<div style="float: left; width: 50%;"></div>';
		$html[] = '<div style="float: right; width: 40%;">';
		
		foreach($rights as $right_name => $right_id)
		{
			$real_right_name = DokeosUtilities :: underscores_to_camelcase(strtolower($right_name));			
			$html[] = '<div style="float: left; width: 24%; text-align: center;">'. Translation :: get($real_right_name) .'</div>';
			$rights_array[$right_id] = $right_name;
		}
		
		$html[] = '</div>';
		$html[] = '<div style="clear: both;"></div>';
		$html[] = '</div>';
		$html[] = '<div style="clear: both;"></div>';
		
		$roles = $this->retrieve_roles();
		
		while ($role = $roles->next_result())
		{
			$html[] = '<div style="padding: 5px; border-bottom: 1px solid #DDDDDD;">';
			$html[] = '<div style="float: left; width: 50%;">'. Translation :: get($role->get_name()) .'</div>';
			$html[] = '<div style="float: right; width: 40%;">';
			
			foreach ($rights_array as $id => $name)
			{
				$html[] = '<div style="float: left; width: 24%; text-align: center;">';
				$value = $this->is_allowed($id, $role->get_id(), $location->get_id());
				$html[] = '<a href="'. $this->get_url(array(RightsManager :: PARAM_COMPONENT_ACTION => 'edit', 'role_id' => $role->get_id(), 'right_id' => $id, 'location' => $location->get_id())) .'">' . ($value == 1 ? '<img src="'. Theme :: get_common_image_path() .'action_setting_true.png" />' : '<img src="'. Theme :: get_common_image_path() .'action_setting_false.png" />') . '</a>';
				$html[] = '</div>';
			}
			
			$html[] = '</div>';
			$html[] = '<div style="clear: both;"></div>';
			$html[] = '</div>';
			$html[] = '<div style="clear: both;"></div>';
		}
		
		$html[] = '</div>';
		
		return implode("\n", $html);
	}
	
	function get_applications()
	{
		$application = $this->application;
		
		$html = array();
		
		$html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/application.js' .'"></script>';
		$html[] = '<div class="configure">';
			
		$the_applications = Application :: load_all();
		$the_applications = array_merge(array('admin', 'tracking', 'repository', 'user', 'group', 'rights', 'home', 'menu'), $the_applications);
					
		foreach ($the_applications as $the_application)
		{
			if (isset($application) && $application == $the_application)
			{
				$html[] = '<div class="application_current">';
			}
			else
			{
				$html[] = '<div class="application">';
			}
			
			$application_name = Translation :: get(DokeosUtilities :: underscores_to_camelcase($the_application)); 
			
			$html[] = '<a href="'. $this->get_url(array(RightsManager :: PARAM_ACTION => RightsManager :: ACTION_EDIT_RIGHTS, RightsManager :: PARAM_APPLICATION => $the_application)) .'">';
			$html[] = '<img src="'. Theme :: get_image_path('admin') . 'place_' . $the_application .'.png" border="0" style="vertical-align: middle;" alt="' . $application_name . '" title="' . $application_name . '"/><br />'. $application_name;
			$html[] = '</a>';
			$html[] = '</div>';
		}
		
		$html[] = '</div>';
		$html[] = '<div style="clear: both;"></div>';

		return implode("\n", $html);
	}
	
	function get_relations()
	{
		$html = array();
		
		$html[] = DokeosUtilities :: add_block_hider();
		$html[] = DokeosUtilities :: build_block_hider('location_relations');
		$html[] = '<div style="width: 100%; height: 400px; border: 1px solid #E0E0E0; background-color: #EEEEEE;"></div>';
		$html[] = DokeosUtilities :: build_block_hider();
		
		return implode("\n", $html);
	}
	
	function display_header($trail)
	{
		$this->get_parent()->display_header($trail);
		echo $this->get_applications();
		echo '<div class="clear">&nbsp;</div>';
	}
}