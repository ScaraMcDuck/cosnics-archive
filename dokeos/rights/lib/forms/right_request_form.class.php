<?php
require_once Path :: get_library_path() . 'html/formvalidator/FormValidator.class.php';

class RoleRequestForm extends FormValidator 
{
    const REQUEST_CONTENT = 'REQUEST_CONTENT';
    
    private $parameter = array();
    
    function RoleRequestForm($parameters = null)
    {
        parent :: __construct('right_request', 'post', $parameters['form_action']);
        
        $this->parameters = $parameters;
        
        $this->build_request_form();
    }
    
    function set_parameter($parameter_name, $value)
    {
        if(!isset($this->parameters))
        {
            $this->parameters = array();
        }
        
        $this->parameters[$parameter_name] = $value;
    }
    
    function build_request_form()
    {
        $this->addElement('textarea', self :: REQUEST_CONTENT, Translation :: get('RightRequestContent'), array('style' => 'width:500px;height:200px;'));
        $this->addRule(self :: REQUEST_CONTENT, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Send'), array('class' => 'positive update'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
		
		$this->setDefaults();
    }
    
    function print_form_header()
    {
        echo '<div class="row">';
        echo '<div class="formw">';
        echo '<div style="width:500px;text-align:justify">';
        
        $explanation = 'RightRequestExplanationCurrentRights';
        if(isset($this->parameters[RightsManagerRightRequesterComponent :: IS_NEW_USER]) && $this->parameters[RightsManagerRightRequesterComponent :: IS_NEW_USER] == true)
        {
            $explanation = 'RightRequestExplanationCurrentRightsNewUser';
        }
        
        echo '<p>' . Translation :: translate($explanation) . '</p>';
        
        $this->print_roles_list();
        $this->print_groups_list();
        
        echo '<p>' . Translation :: translate('RightRequestExplanationFillForm') . '</p>';
        
        echo '</div></div></div>';
    }
    
    function print_roles_list()
    {
        if(isset($this->parameters[RightsManagerRightRequesterComponent :: USER_CURRENT_ROLES])
            && count($this->parameters[RightsManagerRightRequesterComponent :: USER_CURRENT_ROLES]) > 0)
        {
             echo '<h4>' . Translation :: translate('Roles') . '</h4>';
            
            $roles  = $this->parameters[RightsManagerRightRequesterComponent :: USER_CURRENT_ROLES];
            
            /*
             * Display current user roles 
             */
            echo '<ul>';
            foreach ($roles as $role) 
            {
            	echo '<li>' . $role->get_name() . '</li>';
            }
            echo '</ul>';
        }
    }
    
    function print_groups_list()
    {
        if(isset($this->parameters[RightsManagerRightRequesterComponent :: USER_CURRENT_GROUPS])
            && count($this->parameters[RightsManagerRightRequesterComponent :: USER_CURRENT_GROUPS]) > 0)
        {
            echo '<h4>' . Translation :: translate('Groups') . '</h4>';
            
            $groups = $this->parameters[RightsManagerRightRequesterComponent :: USER_CURRENT_GROUPS];
            
            /*
             * Display current user groups 
             */
            
            echo '<ul>';
            foreach ($groups as $group) 
            {
            	echo '<li>' . $group->get_name() . '</li>';
            }
            echo '</ul>';
        }
    }
    
    function print_request_successfully_sent()
    {
        echo '<div class="row">';
        echo '<div class="formw">';
        echo '<div style="width:500px;text-align:justify">';
        
        echo '<p>' . Translation :: translate('RightRequestSuccessfullySent') . '</p>';
        
        echo '</div></div></div>';
    }
    
    function print_request_sending_error()
    {
        echo '<div class="row">';
        echo '<div class="formw">';
        echo '<div style="width:500px;text-align:justify">';
        
        echo '<p>' . Translation :: translate('RightRequestSendingError') . '</p>';
        
        echo '</div></div></div>';
    }
}
?>