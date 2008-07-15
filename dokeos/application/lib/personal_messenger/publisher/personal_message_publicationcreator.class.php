<?php
/**
 * @package application.lib.profiler.publisher
 */
require_once Path :: get_application_library_path() . 'publisher/component/publicationcreator.class.php';
require_once dirname(__FILE__).'/../personal_message_publication_form.class.php';
/**
 * This class represents a profile publisher component which can be used
 * to create a new learning object before publishing it.
 */
class PersonalMessagePublisherPublicationCreatorComponent extends PublisherPublicationCreatorComponent
{
	/**
	 * Gets the form to publish the learning object.
	 * @return string|null A HTML-representation of the form. When the
	 * publication form was validated, this function will send header
	 * information to redirect the end user to the location where the
	 * publication was made.
	 */
//	function get_publication_form($objectID, $new = false)
//	{
//		$out = ($new ? Display :: display_normal_message(htmlentities(Translation :: get('ObjectCreated')), true) : '');
//		$tool = $this->get_parent()->get_parent();
//		$object = RepositoryDataManager :: get_instance()->retrieve_learning_object($objectID);
//
//		$pid = $_GET[PersonalMessenger :: PARAM_PERSONAL_MESSAGE_ID];
//		$publication = null;
//		if (isset($pid))
//		{
//			$publication = PersonalMessengerDataManager :: get_instance()->retrieve_personal_message_publication($pid);
//		}
//
//		$form = new PersonalMessagePublicationForm($object, $publication, $this->get_user(),$this->get_url(array (PersonalMessagePublisher :: PARAM_ID => $object->get_id())));
//		if ($form->validate())
//		{
//			$failures = 0;
//			if ($form->create_learning_object_publication())
//			{
//				$message = 'PersonalMessageSent';
//			}
//			else
//			{
//				$failures++;
//				$message = 'PersonalMessageNotSent';
//			}
//			$this->redirect(null, Translation :: get($message), ($failures ? true : false), array(PersonalMessenger :: PARAM_ACTION => PersonalMessenger :: ACTION_BROWSE_MESSAGES, PersonalMessenger :: PARAM_FOLDER => PersonalMessenger :: ACTION_FOLDER_OUTBOX));
//		}
//		else
//		{
//			$out .= LearningObjectDisplay :: factory($object)->get_full_html();
//			$out .= $form->toHtml();
//		}
//		return $out;
//	}

	function get_publication_form($learning_object_id, $new = false)
	{
		$out = ($new ? Display :: display_normal_message(htmlentities(Translation :: get('LearningObjectCreated')), true) : '');
		$tool = $this->get_parent()->get_parent();
		$learning_object = RepositoryDataManager :: get_instance()->retrieve_learning_object($learning_object_id);
		$edit = $_GET[Publisher :: PARAM_EDIT];
		$user = $_GET[PersonalMessenger :: PARAM_USER_ID];
		
		$form = new PersonalMessagePublicationForm($learning_object, $this->get_user(),$this->get_url(array (PersonalMessagePublisher :: PARAM_ID => $learning_object->get_id())));
		if ($form->validate() || ($edit && $user))
		{
			$failures = 0;
			if ($form->create_learning_object_publication(array($user)))
			{
				$message = 'ProfilePublished';
			}
			else
			{
				$failures++;
				$message = 'ProfileNotPublished';
			}
			
			//$this->redirect(null, Translation :: get($message), ($failures ? true : false), array(PersonalMessenger :: PARAM_ACTION => PersonalMessenger :: ACTION_BROWSE_MESSAGES));
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