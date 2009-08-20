<?php
require_once Path :: get_rights_path() . 'lib/rights_template_manager/rights_template_manager.class.php';
require_once Path :: get_rights_path() . 'lib/rights_template_manager/rights_template_manager_component.class.php';
require_once Path :: get_rights_path() . 'lib/rights_utilities.class.php';

class RightsTemplateManagerSetterComponent extends RightsTemplateManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$rights_template = Request :: get('rights_template_id');
		$right = Request :: get('right_id');
		$location_id =  Request :: get(RightsTemplateManager :: PARAM_LOCATION);
		$location = $this->retrieve_location($location_id);

		if (isset($rights_template) && isset($right) && isset($location))
		{
		    $success = RightsUtilities :: invert_rights_template_right_location($right, $rights_template, $location->get_id());
		    $this->redirect(Translation :: get($success == true ? 'RightUpdated' : 'RightUpdateFailed'), ($success == true ? false : true), array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_CONFIGURE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_SOURCE => $location->get_application(), RightsTemplateManager :: PARAM_LOCATION => $location->get_id()));
		}
		else
		{
		    $this->display_error_page(htmlentities(Translation :: get('NoLocationSelected')));
		}
	}
}
?>