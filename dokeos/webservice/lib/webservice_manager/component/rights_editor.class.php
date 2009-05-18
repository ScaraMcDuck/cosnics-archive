<?php
/**
 * $Id: rights_editor.class.php 17557 2009-01-07 11:32:28Z Scara84 $
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../webservice_manager_component.class.php';
require_once dirname(__FILE__).'/../../webservice_rights.class.php';

/**
 * Repository manager component to edit the rights for the learning objects in
 * the repository.
 */
class WebserviceManagerRightsEditorComponent extends WebserviceManagerComponent
{
	private $location;
    private $webserviceID;
    private $categoryID;
    private $message;
    private $submessage;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
        
        $this->webserviceID = Request :: get(WebserviceManager :: PARAM_WEBSERVICE_ID);
        if(!$this->webserviceID)
        {           
           $this->categoryID = Request :: get(WebserviceManager :: PARAM_WEBSERVICE_CATEGORY_ID);
          
           if ($this->categoryID == null)
           {
               
               $this->location = WebserviceRights :: get_root();
               $this->message = Translation :: get('EditRightsForAllProvidedWebservices');
                
           }
           else
           {              
               $this->location = WebserviceRights :: get_location_by_identifier('webservice_category', $this->categoryID);
               $this->message = Translation :: get('Edit rights');
               $this->submessage = Translation :: get('SetRightsForThe') . " " .$this->location->get_location() ." ". Translation :: get('Category');
           }
           
        }
        else
        {            
            $this->location = WebserviceRights :: get_location_by_identifier('webservice', $this->webserviceID);
            $this->message = Translation :: get('EditRights ');
            $this->submessage = Translation :: get('SetRightsForWebservice ') .$this->location->get_location() ;
        }
        
		$component_action = $_GET[WebserviceManager :: PARAM_COMPONENT_ACTION];        
		
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
	    $reflect = new ReflectionClass('WebserviceRights');
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
			$html[] = '<div style="float: left; width: 50%;">'. Translation :: get($role->get_name()) .'</div>';
			$html[] = '<div style="float: right; width: 40%;">';
			
			foreach ($rights_array as $id => $name)
			{
				$html[] = '<div id="r_'. $id .'_'. $role->get_id() .'_'. $location->get_id() .'" style="float: left; width: 24%; text-align: center;">';
				if (isset($locked_parent))
				{
					$value = $rdm->retrieve_role_right_location($id, $role->get_id(), $locked_parent->get_id())->get_value();
					$html[] = '<a href="'. $this->get_url(array('application' => $this->application, 'location' => $locked_parent->get_id())) .'">' . ($value == 1 ? '<img src="'. Theme :: get_common_image_path() .'action_setting_true_locked.png" title="'. Translation :: get('LockedTrue') .'" />' : '<img src="'. Theme :: get_common_image_path() .'action_setting_false_locked.png" title="'. Translation :: get('LockedFalse') .'" />') . '</a>';
				}
				else
				{
					$value = $rdm->retrieve_role_right_location($id, $role->get_id(), $location->get_id())->get_value();
					
					if (!$value)
					{
						if ($location->inherits())
						{
							$inherited_value = RightsUtilities :: is_allowed_for_role($role->get_id(), $id, $location, $location->get_application());
							
							if ($inherited_value)
							{
								$html[] = '<a class="setRight" href="'. $this->get_url(array(WebserviceManager :: PARAM_COMPONENT_ACTION => 'edit', 'application' => $this->application, 'role_id' => $role->get_id(), 'right_id' => $id, 'location' => $location->get_id())) .'">' . '<div class="rightInheritTrue"></div></a>';
							}
							else
							{
								$html[] = '<a class="setRight" href="'. $this->get_url(array(WebserviceManager :: PARAM_COMPONENT_ACTION => 'edit', 'application' => $this->application, 'role_id' => $role->get_id(), 'right_id' => $id, 'location' => $location->get_id())) .'">' . '<div class="rightFalse"></div></a>';
							}
						}
						else
						{
							$html[] = '<a class="setRight" href="'. $this->get_url(array(WebserviceManager :: PARAM_COMPONENT_ACTION => 'edit', 'application' => $this->application, 'role_id' => $role->get_id(), 'right_id' => $id, 'location' => $location->get_id())) .'">' . '<div class="rightFalse"></div></a>';
						}
					}
					else
					{
						$html[] = '<a class="setRight" href="'. $this->get_url(array(WebserviceManager :: PARAM_COMPONENT_ACTION => 'edit', 'application' => $this->application, 'role_id' => $role->get_id(), 'right_id' => $id, 'location' => $location->get_id())) .'">' . '<div class="rightTrue"></div></a>';
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
        
        $toolbar = new Toolbar();        
		$location = $this->location;       
        
        if(isset($location))
        {
            if (!$location->is_root())
			{
				if ($location->inherits())
				{                    
					$toolbar->add_item(new ToolbarItem(Translation :: get('LocationNoInherit'), Theme :: get_common_image_path() . 'action_setting_false_inherit.png', $this->get_url(array(WebserviceManager :: PARAM_COMPONENT_ACTION => 'inherit', 'application' => 'webservice', 'webservice' => $this->webserviceID, 'webservice_category_id' => $this->categoryID))));
				}
				else
				{                    
					$toolbar->add_item(new ToolbarItem(Translation :: get('LocationInherit'), Theme :: get_common_image_path() . 'action_setting_true_inherit.png', $this->get_url(array(WebserviceManager :: PARAM_COMPONENT_ACTION => 'inherit', 'application' => 'webservice', 'webservice' => $this->webserviceID, 'webservice_category_id' => $this->categoryID))));
				}
			}           
        }
        else
        {
            echo 'No location.';
        }
		
		return $toolbar->as_html();
	}
	
	function edit_right()
	{
        
		$role = $_GET['role_id'];
		$right = $_GET['right_id'];
		$location =  $this->location;
        
        $success = RightsUtilities :: invert_role_right_location($right, $role, $location);

        $this->redirect('url', Translation :: get($success == true ? 'RightUpdated' : 'RightUpdateFailed'), ($success == true ? false : true), array(WebserviceManager :: PARAM_ACTION => WebserviceManager :: ACTION_MANAGE_ROLES,'application' => 'webservice','webservice' => $this->webserviceID, 'webservice_category_id' => $this->categoryID));
       		
	}
	
	function inherit_location()
	{        
		$location = $this->location;        
		$success = RightsUtilities :: switch_location_inherit($location);
        
        //$this->redirect('url', Translation :: get($success == true ? 'RightUpdated' : 'RightUpdateFailed'), ($success == true ? false : true), array(WebserviceManager :: PARAM_ACTION => WebserviceManager :: ACTION_MANAGE_ROLES,'application' => 'webservice','webservice' => $this->webserviceID, 'webservice' => $this->webserviceID,'webservice_category_id' => $this->categoryID));
        if($this->webserviceID == null)
        {
            $this->redirect('url', Translation :: get($success == true ? 'RightUpdated' : 'RightUpdateFailed'), ($success == true ? false : true), array(WebserviceManager :: PARAM_ACTION => WebserviceManager :: ACTION_MANAGE_ROLES,'application' => 'webservice', 'webservice_category_id' => $this->categoryID));
        }
        else
        {
            $this->redirect('url', Translation :: get($success == true ? 'RightUpdated' : 'RightUpdateFailed'), ($success == true ? false : true), array(WebserviceManager :: PARAM_ACTION => WebserviceManager :: ACTION_MANAGE_ROLES,'application' => 'webservice', 'webservice' => $this->webserviceID));
        }
	}
	
	function show_rights_list()
	{        
		$trail = new BreadcrumbTrail();
        $admin = new AdminManager();
        $trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(WebserviceManager :: PARAM_ACTION => WebserviceManager :: ACTION_BROWSE_WEBSERVICES)), Translation :: get('Webservices')));
		$trail->add(new Breadcrumb($this->get_url(array(WebserviceManager :: PARAM_ACTION => WebserviceManager :: ACTION_BROWSE_WEBSERVICES)), Translation :: get('BrowseWebservices')));
        $trail->add(new Breadcrumb($this->get_url(array(WebserviceManager :: PARAM_ACTION => WebserviceManager :: ACTION_MANAGE_ROLES, WebserviceManager::PARAM_WEBSERVICE_ID => Request :: get(WebserviceManager::PARAM_WEBSERVICE_ID))), $this->message));
        
        			
			$this->display_header($trail);
            echo $this->submessage .'<br/><br/>';
			echo $this->get_modification_links();            
			echo $this->get_rights_table_html();            
			echo RightsUtilities :: get_rights_legend();
			$this->display_footer();
	}
    
}
?>