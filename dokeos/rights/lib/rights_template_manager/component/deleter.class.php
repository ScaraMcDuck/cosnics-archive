<?php
require_once Path :: get_rights_path() . 'lib/rights_template_manager/rights_template_manager.class.php';
require_once Path :: get_rights_path() . 'lib/rights_template_manager/rights_template_manager_component.class.php';

class RightsTemplateManagerDeleterComponent extends RightsTemplateManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = Request :: get(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ID);
		$failures = 0;

		if (!empty($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$rights_template = $this->retrieve_rights_template($id);

				if (!$rights_template->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedRightsTemplateDeleted';
				}
				else
				{
					$message = 'SelectedRightsTemplateDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedRightsTemplatesDeleted';
				}
				else
				{
					$message = 'SelectedRightsTemplatesDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_BROWSE_RIGHTS_TEMPLATES));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoRightsTemplateSelected')));
		}
	}
}
?>