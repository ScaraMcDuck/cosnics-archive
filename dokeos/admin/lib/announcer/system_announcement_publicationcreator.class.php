<?php
/**
 * @package application.lib.profiler.publisher
 */
require_once dirname(__FILE__).'/../system_announcement_publication_form.class.php';
/**
 * This class represents a profile publisher component which can be used
 * to create a new learning object before publishing it.
 */
class SystemAnnouncerPublicationCreator
{
	/**
	 * Gets the form to publish the learning object.
	 * @return string|null A HTML-representation of the form. When the
	 * publication form was validated, this function will send header
	 * information to redirect the end user to the location where the
	 * publication was made.
	 */
	function get_publication_form($learning_object_id, $new = false)
	{
		$out = ($new ? Display :: normal_message(htmlentities(Translation :: get('LearningObjectCreated')), true) : '');
		//$tool = $this->get_parent()->get_parent();
		$learning_object = RepositoryDataManager :: get_instance()->retrieve_learning_object($learning_object_id);
		
		$form_action_parameters = array_merge($this->get_parameters(), array (SystemAnnouncer :: PARAM_ID => $learning_object->get_id()));
		$form = new SystemAnnouncementPublicationForm(SystemAnnouncementPublicationForm :: TYPE_SINGLE, $learning_object, $this->get_user(), $this->get_url($form_action_parameters));
		if ($form->validate())
		{
			if ($form->create_learning_object_publication())
			{
				$success = true;
				$message = 'SystemAnnouncementPublished';
			}
			else
			{
				$success = false;
				$message = 'SystemAnnouncementNotPublished';
			}
			
			$this->redirect(Translation :: get($message), (!$success ? true : false), array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_BROWSE_SYSTEM_ANNOUNCEMENTS), array(Publisher :: PARAM_ACTION));
		}
		else
		{
			$out .= LearningObjectDisplay :: factory($learning_object)->get_full_html();
			$out .= $form->toHtml();
		}
		return $out;
	}
}
?>