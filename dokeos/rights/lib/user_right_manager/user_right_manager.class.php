<?php
/**
 * @package admin
 * @subpackage package_manager
 * @author Hans De Bisschop
 */
require_once Path :: get_common_path() . 'sub_manager.class.php';
require_once Path :: get_rights_path() . 'lib/user_right_manager/user_right_manager_component.class.php';

class UserRightManager extends SubManager
{
    const PARAM_USER_RIGHT_ACTION = 'action';
    const PARAM_USER = 'user';
    const PARAM_SOURCE = 'source';
    const PARAM_LOCATION = 'location';

    const ACTION_BROWSE_USER_RIGHTS = 'browse';
    const ACTION_SET_USER_RIGHTS = 'set';

    function UserRightManager($rights_manager)
    {
        parent :: __construct($rights_manager);

        $rights_template_action = Request :: get(self :: PARAM_USER_RIGHT_ACTION);
        if ($rights_template_action)
        {
            $this->set_parameter(self :: PARAM_USER_RIGHT_ACTION, $rights_template_action);
        }
    }

    function run()
    {
        $rights_template_action = $this->get_parameter(self :: PARAM_USER_RIGHT_ACTION);

        switch ($rights_template_action)
        {
            case self :: ACTION_BROWSE_USER_RIGHTS :
                $component = UserRightManagerComponent :: factory('Browser', $this);
                break;
            case self :: ACTION_SET_USER_RIGHTS :
                $component = UserRightManagerComponent :: factory('Setter', $this);
                break;
            default :
                $component = UserRightManagerComponent :: factory('Browser', $this);
                break;
        }

        $component->run();
    }

    function get_application_component_path()
    {
        return Path :: get_rights_path() . 'lib/user_right_manager/component/';
    }

	function retrieve_locations($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_locations($condition, $offset, $count, $order_property, $order_direction);
	}

	function count_locations($conditions = null)
	{
		return $this->get_parent()->count_locations($conditions);
	}

	function retrieve_location($location_id)
	{
		return $this->get_parent()->retrieve_location($location_id);
	}

	function retrieve_user_right_location($right_id, $user_id, $location_id)
	{
		return $this->get_parent()->retrieve_user_right_location($right_id, $user_id, $location_id);
	}
}
?>