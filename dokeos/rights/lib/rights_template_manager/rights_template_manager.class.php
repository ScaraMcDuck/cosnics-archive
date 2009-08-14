<?php
/**
 * @package admin
 * @subpackage package_manager
 * @author Hans De Bisschop
 */
require_once Path :: get_common_path() . 'sub_manager.class.php';
require_once Path :: get_admin_path() . 'lib/package_manager/package_manager_component.class.php';

class RightsTemplateManager extends SubManager
{
	const PARAM_RIGHTS_TEMPLATE_ID = 'template';
    const PARAM_RIGHTS_TEMPLATE_ACTION = 'action';
    
    const ACTION_BROWSE_RIGHTS_TEMPLATES = 'browse';
    const ACTION_EDIT_RIGHTS_TEMPLATE = 'edit';
    const ACTION_DELETE_RIGHTS_TEMPLATES = 'delete';
    const ACTION_CREATE_RIGHTS_TEMPLATE = 'create';

    function RightsTemplateManager($rights_manager)
    {
        parent :: __construct($rights_manager);
        
        $rights_template_action = Request :: get(self :: PARAM_TEMPLATE_ACTION);
        if ($rights_template_action)
        {
            $this->set_parameter(self :: PARAM_TEMPLATE_ACTION, $rights_template_action);
        }
    }

    function run()
    {
        $rights_template_action = $this->get_parameter(self :: PARAM_TEMPLATE_ACTION);
        
        switch ($package_action)
        {
            case self :: ACTION_BROWSE_RIGHTS_TEMPLATES :
                $component = RightsTemplateManagerComponent :: factory('Browser', $this);
                break;
            case self :: ACTION_EDIT_RIGHTS_TEMPLATE :
                $component = RightsTemplateManagerComponent :: factory('Editor', $this);
                break;
            case self :: ACTION_DELETE_RIGHTS_TEMPLATES :
                $component = RightsTemplateManagerComponent :: factory('Deleter', $this);
                break;
            case self :: ACTION_CREATE_RIGHTS_TEMPLATE :
                $component = RightsTemplateManagerComponent :: factory('Creator', $this);
                break;
            default :
                $component = RightsTemplateManagerComponent :: factory('Browser', $this);
                break;
        }
        
        $component->run();
    }

    function get_application_component_path()
    {
        return Path :: get_rights_path() . 'lib/rights_rights_template_manager/component/';
    }
    
    function get_rights_template_deleting_url($rights_template)
    {
        return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_RIGHTS_TEMPLATES, self :: PARAM_RIGHTS_TEMPLATE_ID => $rights_template->get_id()));
    }

    function get_rights_template_editing_url($rights_template)
    {
        return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_EDIT_RIGHTS_TEMPLATES, self :: PARAM_RIGHTS_TEMPLATE_ID => $rights_template->get_id()));
    }
}
?>