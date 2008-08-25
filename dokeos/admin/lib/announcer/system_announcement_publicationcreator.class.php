<?php
/**
 * @package application.lib.profiler.publisher
 */
require_once Path :: get_application_library_path() . 'publisher/component/publicationcreator.class.php';
require_once dirname(__FILE__).'/../system_announcement_form.class.php';
/**
 * This class represents a profile publisher component which can be used
 * to create a new learning object before publishing it.
 */
class SystemAnnouncerPublicationCreatorComponent extends PublisherPublicationCreatorComponent
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
		$out = ($new ? Display :: display_normal_message(htmlentities(Translation :: get('LearningObjectCreated')), true) : '');
		$tool = $this->get_parent()->get_parent();
		$learning_object = RepositoryDataManager :: get_instance()->retrieve_learning_object($learning_object_id);
		
		$form = new ProfilePublicationForm($learning_object, $this->get_user(),$this->get_url(array (ProfilePublisher :: PARAM_ID => $learning_object->get_id())));
		if ($form->validate())
		{
			$failures = 0;
			if ($form->create_learning_object_publication())
			{
				$message = 'SystemAnnouncementPublished';
			}
			else
			{
				$failures++;
				$message = 'SystemAnnouncementNotPublished';
			}
			
			$this->redirect(null, Translation :: get($message), ($failures ? true : false), array(Profiler :: PARAM_ACTION => Profiler :: ACTION_BROWSE_PROFILES));
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