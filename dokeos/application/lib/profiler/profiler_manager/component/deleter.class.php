<?php
/**
 * @package application.lib.profiler.profiler_manager
 */
require_once dirname(__FILE__).'/../profiler.class.php';
require_once dirname(__FILE__).'/../profilercomponent.class.php';

class ProfilerDeleterComponent extends ProfilerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[Profiler :: PARAM_PROFILE_ID];
		$failures = 0;
		
		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}
			
			foreach ($ids as $id)
			{
				$publication = $this->get_parent()->retrieve_profile_publication($id);
				
				if (!$publication->delete())
				{
					$failures++;
				}
			}
			
			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedPublicationNotDeleted';
				}
				else
				{
					$message = 'SelectedPublicationsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedPublicationDeleted';
				}
				else
				{
					$message = 'SelectedPublicationsDeleted';
				}
			}
			
			$this->redirect(null, Translation :: get_lang($message), ($failures ? true : false), array(Profiler :: PARAM_ACTION => Profiler :: ACTION_BROWSE_PROFILES));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get_lang('NoPublicationSelected')));
		}
	}
}
?>