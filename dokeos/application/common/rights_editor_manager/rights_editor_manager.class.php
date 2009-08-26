<?php
/**
 * @package application/common
 * @subpackage rights_editor_manager
 * @author Sven Vanpoucke
 */
require_once Path :: get_common_path() . 'sub_manager.class.php';
require_once dirname(__FILE__) . '/rights_editor_manager_component.class.php';

class RightsEditorManager extends SubManager
{
    const PARAM_RIGHTS_EDITOR_ACTION = 'action';

    const ACTION_BROWSE_RIGHTS = 'browse';
    const ACTION_SET_USER_RIGHTS = 'set_user_rights';
    const ACTION_SET_GROUP_RIGHTS = 'set_group_rights';

    private $location;
    
    function RightsEditorManager($parent, $location)
    {
        parent :: __construct($parent);

        $this->location = $location;
       
        $rights_editor_action = Request :: get(self :: PARAM_RIGHTS_EDITOR_ACTION);
        if ($rights_editor_action)
        {
            $this->set_parameter(self :: PARAM_RIGHTS_EDITOR_ACTION, $rights_editor_action);
        }
    }

    function run()
    {
        $parent = $this->get_parameter(self :: PARAM_RIGHTS_EDITOR_ACTION);

        switch ($parent)
        {
            case self :: ACTION_BROWSE_RIGHTS :
                $component = RightsEditorManagerComponent :: factory('Browser', $this);
                break;
            case self :: ACTION_SET_USER_RIGHTS :
                $component = RightsEditorManagerComponent :: factory('UserRightsSetter', $this);
                break;
            case self :: ACTION_SET_GROUP_RIGHTS :
                $component = RightsEditorManagerComponent :: factory('GroupRightsSetter', $this);
                break;
            default :
                $component = RightsEditorManagerComponent :: factory('Browser', $this);
                break;
        }

        $component->run();
    }

    function get_application_component_path()
    {
        return Path :: get_application_library_path() . 'rights_editor_manager/component/';
    }
    
    function get_location()
    {
    	return $this->location;
    }
    
	function get_available_rights()
	{
		return $this->get_parent()->get_available_rights();
	}
}
?>