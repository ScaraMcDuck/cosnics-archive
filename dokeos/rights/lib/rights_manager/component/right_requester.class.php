<?php
require_once Path :: get_rights_path() . 'lib/forms/right_request_form.class.php';
require_once Path :: get_rights_path() . 'lib/data_manager/database.class.php';
require_once Path :: get_common_path() . 'mail/mail.class.php';
require_once Path :: get_library_path() . 'security/security.class.php';

class RightsManagerRightRequesterComponent extends RightsManagerComponent
{
    const USER_CURRENT_ROLES  = 'USER_CURRENT_ROLES';
    const USER_CURRENT_GROUPS = 'USER_CURRENT_GROUPS';
    const IS_NEW_USER         = 'IS_NEW_USER';
    
    const PARAM_IS_NEW_USER   = 'newUser';
    
    /**
	 * Runs this component and displays its output.
	 */
	function run()
	{
	    $user = $this->get_user();
	        
        if(isset($user))
        {
    	    $trail = new BreadcrumbTrail();
    	    $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_REQUEST_RIGHT)), Translation :: get('RightRequest')));
    	    
    	    $parameters = array();
    	    
    	    $parameters['form_action'] = $this->get_url(array(self :: PARAM_IS_NEW_USER => '1'));
    	    
    	    $new_profile = Request :: get(self :: PARAM_IS_NEW_USER);
    	    if(isset($new_profile))
    	    {
    	        $parameters[self :: IS_NEW_USER] = true;
    	    }
    	    
    	    $form = new RoleRequestForm($parameters);
    	    
    	    if($form->validate())
    	    {
    	        $this->display_header($trail);
    	        
    	        $data = $form->getSubmitValues();
    	        
    	        //set the Translation language to the platform default language for the email to the Dokeos administrator
    	        $traductor = Translation :: get_instance();
    	        $traductor->set_language(PlatformSetting :: get_instance()->get('platform_language'));
    	        
    	        $admin_email    = PlatformSetting :: get_instance()->get('administrator_email');
    	        $email_content  = Security :: remove_XSS($data[RoleRequestForm :: REQUEST_CONTENT]);
    	        $email_title    = $traductor->get('RightRequestEmailTitle');
    	        $email_user     = $user->get_email(); 
    	        $email_username = $user->get_lastname() . ' ' . $user->get_firstname(); 
    	        $user_id        = $user->get_id();
    	        $email_body     = $traductor->get('RightRequestEmailBody');
    	        $email_body     = sprintf($email_body, $email_username, $user_id, $email_content);
    	        
    	        //reset the Translation language to the user preference
    	        $traductor->set_language($user->get_language());
    	        
    	        $mail = Mail :: factory($email_title, $email_body, $admin_email, $admin_email, array($email_user));
    	        if($mail->send())
    	        {
    	            $form->print_request_successfully_sent();
    	        }
    	        else
    	        {
    	            $form->print_request_sending_error();
    	        }
    	        
    	        $this->display_footer();
    	    }
    	    else
    	    {
    	        /*
    	         * display request form
    	         * 
    	         * - get user's current roles and groups (to display them to the user) 
    	         */
    	       
	            $this->display_header($trail);

	            $roles = array();
	            $groups = array();
    	        $user_roles  = $user->get_roles();
                $user_groups = $user->get_user_groups();
                
                $gdm = DatabaseGroupDataManager :: get_instance();
                while($user_group = $user_groups->next_result())
        		{
        			$group_id = $user_group->get_group_id();
        			$group = $gdm->retrieve_group($group_id);
        			
        			//$group may be null if no FK exists in the DB 
        			if(isset($group))
        			{
        			    $groups[] = $group;
        			}
        		}
        		
                $rdm = DatabaseRightsDataManager :: get_instance();
        		while($user_role = $user_roles->next_result())
        		{
        			$role_id = $user_role->get_role_id();
        			$role = $rdm->retrieve_role($role_id);
        			
        			//$role may be null if no FK exists in the DB
        			if(isset($role))
        			{
        			    $roles[] = $role;
        			}
        		}
                
    	        $form->set_parameter(self :: USER_CURRENT_ROLES, $roles);
    	        $form->set_parameter(self :: USER_CURRENT_GROUPS, $groups);
    	        
    	        $form->print_form_header();
    	        $form->display();
    	        
    	        $this->display_footer();
            }
	    }
	    else
        {
            Display :: not_allowed();
        }
	}
}
?>