<?php
/**
 * @package application.personal_messenger.personal_messenger_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */

require_once dirname(__FILE__).'/../personal_messenger.class.php';
require_once dirname(__FILE__).'/../personalmessengercomponent.class.php';

class PersonalMessengerMarkerComponent extends PersonalMessengerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[PersonalMessenger :: PARAM_PERSONAL_MESSAGE_ID];
		$mark_type = $_GET[PersonalMessenger :: PARAM_MARK_TYPE];
		$failures = 0;
		
		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}
			
			foreach ($ids as $id)
			{
				$publication = $this->get_parent()->retrieve_personal_message_publication($id);
				if ($mark_type == PersonalMessenger :: PARAM_MARK_SELECTED_READ)
				{
					$publication->set_status(0);
				}
				elseif($mark_type == PersonalMessenger :: PARAM_MARK_SELECTED_UNREAD)
				{
					$publication->set_status(1);
				}
				
				if (!$publication->update())
				{
					$failures++;
				}
			}
			
			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedPublicationNotUpdated';
				}
				else
				{
					$message = 'SelectedPublicationsNotUpdated';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedPublicationUpdated';
				}
				else
				{
					$message = 'SelectedPublicationsUpdated';
				}
			}
			
			$this->redirect(null, get_lang($message), ($failures ? true : false), array(PersonalMessenger :: PARAM_ACTION => PersonalMessenger :: ACTION_BROWSE_MESSAGES));
		}
		else
		{
			$this->display_error_page(htmlentities(get_lang('NoPublicationSelected')));
		}
	}
}
?>