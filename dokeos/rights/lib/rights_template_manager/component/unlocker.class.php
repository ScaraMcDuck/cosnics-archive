<?php
require_once Path :: get_rights_path() . 'lib/rights_template_manager/rights_template_manager.class.php';
require_once Path :: get_rights_path() . 'lib/rights_template_manager/rights_template_manager_component.class.php';

class RightsTemplateManagerUnlockerComponent extends RightsTemplateManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = Request :: get(RightsTemplateManager :: PARAM_LOCATION);
		$failures = 0;

		if (!empty($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$location = $this->retrieve_location($id);
				$location->unlock();

				if (!$location->update())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedLocationNotUnlocked';
				}
				else
				{
					$message = 'SelectedLocationsNotUnlocked';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedLocationUnlocked';
				}
				else
				{
					$message = 'SelectedLocationsUnlocked';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_CONFIGURE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_SOURCE => $location->get_application(), RightsTemplateManager :: PARAM_LOCATION => $location->get_id()));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoLocationSelected')));
		}
	}
}
?>