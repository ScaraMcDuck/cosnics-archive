<?php
/**
 * $Id: rights_editor.class.php 17557 2009-01-07 11:32:28Z Scara84 $
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../reservations_manager_component.class.php';
require_once dirname(__FILE__).'/../../reservations_rights.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';

/**
 * Repository manager component to edit the rights for the learning objects in
 * the repository.
 */
class ReservationsManagerRightsEditorComponent extends ReservationsManagerComponent
{
	private $location;
    private $message;
    private $submessage;
    
    const PARAM_COMPONENT_ACTION = 'rights_action';
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
        
		$this->type = Request :: get('type');
		$this->id = Request :: get('id');
		
		$this->location = ReservationsRights :: get_location_by_identifier($this->type, $this->id);
        
		$component_action = $_GET[self :: PARAM_COMPONENT_ACTION];        
		
		switch($component_action)
		{
			case 'edit':
				$this->edit_right();
				break;			
			case 'inherit':                
				$this->inherit_location();
				break;
			default :
				$this->show_rights_list();
		}		

	}
	
	function get_rights_table_html()
	{
		$rdm = RightsDataManager :: get_instance();		
		
		$location = $this->location;        
		
		// TODO: When PHP 5.3 gets released, replace this by $class :: get_available_rights()
	    $reflect = new ReflectionClass('ReservationsRights');
	    $rights = $reflect->getConstants();
	    // TODO: When PHP 5.3 gets released, replace this by $class :: get_available_rights()
	    
		$rights_array = array();
		
		$html = array();
		
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
		
		$roles = $rdm->retrieve_roles();
        if(isset($location))
        {            
            $locked_parent = $location->get_locked_parent();
        }
        else
        {
            echo 'No location.';
        }
		
		
		while ($role = $roles->next_result())
		{
			$html[] = '<div style="padding: 5px; border-bottom: 1px solid #DDDDDD;">';
			$html[] = '<div style="float: left; width: 50%;">'. $role->get_name() .'</div>';
			$html[] = '<div style="float: right; width: 40%;">';
			
			foreach ($rights_array as $id => $name)
			{
				$html[] = '<div id="r_'. $id .'_'. $role->get_id() .'_'. $location->get_id() .'" style="float: left; width: 24%; text-align: center;">';
				if (isset($locked_parent))
				{
					$value = $rdm->retrieve_role_right_location($id, $role->get_id(), $locked_parent->get_id())->get_value();
					$html[] = '<a href="'. $this->get_url(array('application' => $this->application, 'location' => $locked_parent->get_id())) .'">' . ($value == 1 ? '<img src="'. Theme :: get_img_path('') .'action_setting_true_locked.png" title="'. Translation :: get('LockedTrue') .'" />' : '<img src="'. Theme :: get_img_path('') .'action_setting_false_locked.png" title="'. Translation :: get('LockedFalse') .'" />') . '</a>';
				}
				else
				{
					$value = $rdm->retrieve_role_right_location($id, $role->get_id(), $location->get_id())->get_value();
					
					if (!$value)
					{
						if ($location->inherits())
						{
							$inherited_value = RightsDokeosUtilities :: is_allowed_for_role($role->get_id(), $id, $location, $location->get_application());
							
							if ($inherited_value)
							{
								$html[] = '<a class="setRight" href="'. $this->get_url(array(self :: PARAM_COMPONENT_ACTION => 'edit', 'application' => $this->application, 'role_id' => $role->get_id(), 'right_id' => $id, 'location' => $location->get_id())) .'">' . '<div class="rightInheritTrue"></div></a>';
							}
							else
							{
								$html[] = '<a class="setRight" href="'. $this->get_url(array(self :: PARAM_COMPONENT_ACTION => 'edit', 'application' => $this->application, 'role_id' => $role->get_id(), 'right_id' => $id, 'location' => $location->get_id())) .'">' . '<div class="rightFalse"></div></a>';
							}
						}
						else
						{
							$html[] = '<a class="setRight" href="'. $this->get_url(array(self :: PARAM_COMPONENT_ACTION => 'edit', 'application' => $this->application, 'role_id' => $role->get_id(), 'right_id' => $id, 'location' => $location->get_id())) .'">' . '<div class="rightFalse"></div></a>';
						}
					}
					else
					{
						$html[] = '<a class="setRight" href="'. $this->get_url(array(self :: PARAM_COMPONENT_ACTION => 'edit', 'application' => $this->application, 'role_id' => $role->get_id(), 'right_id' => $id, 'location' => $location->get_id())) .'">' . '<div class="rightTrue"></div></a>';
					}
				}
				$html[] = '</div>';
			}
			
			$html[] = '</div>';
			$html[] = '<div style="clear: both;"></div>';
			$html[] = '</div>';
			$html[] = '<div style="clear: both;"></div>';
		}
		
		$html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/rights_ajax.js' .'"></script>';
		$html[] = '</div>';
		
		return implode("\n", $html);
	}
	
	function get_modification_links()
    {
 		$tb_data = array();

	 	$location = $this->location;       
        
        if(isset($location))
        {
            if (!$location->is_root())
			{
				if ($location->inherits())
				{                    
					$tb_data[] = array(
						'href' => $this->get_url(array(self :: PARAM_COMPONENT_ACTION => 'inherit', 'application' => 'reservations','type' => $this->type, 'id' => $this->id)),
						'label' => Translation :: get('LocationNoInherit'),
						'img' => Theme :: get_theme_path() . 'action_setting_false_inherit.png'
					);     
				}
				else
				{                    
					$tb_data[] = array(
						'href' => $this->get_url(array(self :: PARAM_COMPONENT_ACTION => 'inherit', 'application' => 'reservations','type' => $this->type, 'id' => $this->id)),
						'label' => Translation :: get('LocationInherit'),
						'img' => Theme :: get_theme_path() . 'action_setting_true_inherit.png'
					);     
				}
			}           
        }
        else
        {
            echo 'No location.';
        }

    	$ab = new ActionBarRenderer($tb_data);
    	return $ab->as_html() . '<br />';
    	
	}
	
	function edit_right()
	{
        
		$role = $_GET['role_id'];
		$right = $_GET['right_id'];
		$location =  $this->location;
        
        $success = RightsDokeosUtilities :: invert_role_right_location($right, $role, $location);

        $this->redirect('url', Translation :: get($success == true ? 'RightUpdated' : 'RightUpdateFailed'), ($success == true ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_EDIT_RIGHTS,'application' => 'reservations','type' => $this->type, 'id' => $this->id));
       		
	}
	
	function inherit_location()
	{        
		$location = $this->location;        
		$success = RightsDokeosUtilities :: switch_location_inherit($location);
        
        //$this->redirect('url', Translation :: get($success == true ? 'RightUpdated' : 'RightUpdateFailed'), ($success == true ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_EDIT_RIGHTS,'application' => 'reservations','reservations' => $this->reservationsID, 'reservations' => $this->reservationsID,'reservations_category_id' => $this->categoryID));
        $this->redirect('url', Translation :: get($success == true ? 'RightUpdated' : 'RightUpdateFailed'), ($success == true ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_EDIT_RIGHTS,'application' => 'reservations','type' => $this->type, 'id' => $this->id));
	}
	
	function show_rights_list()
	{        
		$trail = new BreadcrumbTrail();
		
		$admin = new Admin();
		$trail->add(new Breadcrumb($admin->get_link(array(Admin :: PARAM_ACTION => Admin :: ACTION_ADMIN_BROWSER)), Translation :: get('PlatformAdmin')));
		
		if($this->type == 'category')
			$trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_CATEGORIES)), Translation :: get('View categories')));
		else
			$trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_ITEMS)), Translation :: get('View items')));
								
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_EDIT_RIGHTS)), Translation :: get('EditRights')));
        			
		$this->display_header($trail);
        echo $this->submessage .'<br/><br/>';
		echo $this->get_modification_links();            
		echo $this->get_rights_table_html();            
		echo RightsDokeosUtilities :: get_rights_legend();
		$this->display_footer();
	}
    
}
?>