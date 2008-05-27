<?php
/**
 * @package application.personal_messenger.personal_messenger_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
 
require_once dirname(__FILE__).'/../personal_messenger.class.php';
require_once dirname(__FILE__).'/../personal_messenger_component.class.php';

class PersonalMessengerDeleterComponent extends PersonalMessengerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[PersonalMessenger :: PARAM_PERSONAL_MESSAGE_ID];
		$failures = 0;
		$folder = $_GET[PersonalMessenger :: PARAM_FOLDER];
		
		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}
			
			foreach ($ids as $id)
			{
				$publication = $this->get_parent()->retrieve_personal_message_publication($id);
				
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
			
			$this->redirect(null, Translation :: get($message), ($failures ? true : false), array(PersonalMessenger :: PARAM_ACTION => PersonalMessenger :: ACTION_BROWSE_MESSAGES, PersonalMessenger :: PARAM_FOLDER => $folder));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoPublicationSelected')));
		}
	}
}
?>