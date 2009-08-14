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
	const PARAM_TEMPLATE = 'template';
    const PARAM_TEMPLATE_ACTION = 'action';
    
    const ACTION_BROWSE_TEMPLATES = 'browse';
    const ACTION_EDIT_TEMPLATE = 'edit';
    const ACTION_DELETE_TEMPLATES = 'delete';
    const ACTION_CREATE_TEMPLATE = 'create';

    function RightsTemplateManager($rights_manager)
    {
        parent :: __construct($rights_manager);
        
        $template_action = Request :: get(self :: PARAM_TEMPLATE_ACTION);
        if ($template_action)
        {
            $this->set_parameter(self :: PARAM_TEMPLATE_ACTION, $template_action);
        }
    }

    function run()
    {
        $template_action = $this->get_parameter(self :: PARAM_TEMPLATE_ACTION);
        
        switch ($package_action)
        {
            case self :: ACTION_BROWSE_TEMPLATES :
                $component = RightsTemplateManagerComponent :: factory('Browser', $this);
                break;
            default :
                $component = RightsTemplateManagerComponent :: factory('Browser', $this);
                break;
        }
        
        $component->run();
    }

    function get_application_component_path()
    {
        return Path :: get_rights_path() . 'lib/rights_template_manager/component/';
    }
}
?>