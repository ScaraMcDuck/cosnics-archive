<?php
/**
 * @package application.lib.profiler.profiler_manager
 */
require_once dirname(__FILE__).'/../admin_manager.class.php';
require_once dirname(__FILE__).'/../admin_manager_component.class.php';

class AdminManagerSystemAnnouncementHiderComponent extends AdminManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[AdminManager :: PARAM_SYSTEM_ANNOUNCEMENT_ID];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$publication = $this->get_parent()->retrieve_system_announcement_publication($id);

				$publication->toggle_visibility();
				if (!$publication->update())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedPublicationVisibilityNotToggled';
				}
				else
				{
					$message = 'SelectedPublicationsVisibilityNotToggled';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedPublicationVisibilityToggled';
				}
				else
				{
					$message = 'SelectedPublicationsVisibilityToggled';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => AdminManager :: ACTION_BROWSE_SYSTEM_ANNOUNCEMENTS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoPublicationSelected')));
		}
	}
}
?>