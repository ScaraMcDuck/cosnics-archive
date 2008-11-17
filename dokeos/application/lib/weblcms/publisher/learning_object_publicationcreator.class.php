<?php
/**
 * @package application.lib.profiler.publisher
 */
require_once Path :: get_application_library_path() . 'publisher/component/publicationcreator.class.php';
require_once dirname(__FILE__).'/../learning_object_publication_form.class.php';
require_once dirname(__FILE__).'/../tool/tool.class.php';
/**
 * This class represents a profile publisher component which can be used
 * to publish learning objects passed to it.
 */
class LearningObjectPublisherPublicationCreatorComponent extends PublisherPublicationCreatorComponent
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
		$out = ($new ? Display :: display_normal_message(htmlentities(Translation :: get('ObjectCreated')), true) : '');
		$object = RepositoryDataManager :: get_instance()->retrieve_learning_object($learning_object_id);
		
		$form = new LearningObjectPublicationForm($object, $this->get_parent(), $this->get_parent()->with_mail_option(), $this->get_parent()->get_course());
		if ($form->validate())
		{ 
			$publication = $form->create_learning_object_publication();
			// TODO: Use a function for this.
			//$parameters['action'] = Tool::ACTION_SHOW_NORMAL_MESSAGE;
			$parameters['pcattree'] = $publication->get_category_id();
			
			// Redirect to location where the publication was made
			
			if (!$publication)
			{
				$message = Translation :: get('ObjectNotPublished');
			}
			else
			{
				$message = Translation :: get('ObjectPublished');
			}

			
			if($publication->get_tool() == 'introduction')
				$redirect_parms = array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_VIEW_COURSE);
			else
				$redirect_parms = array(Tool :: PARAM_ACTION, Publisher :: PARAM_ACTION);
		
			$this->redirect($message, (!$publication ? true : false), $parameters, $redirect_parms);
			
			// n case headers were allready sent, we simply show the confirmation message here
			$out .= Display::display_normal_message(Translation :: get('ObjectPublished'),true);
		}
		else
		{
			$out .= LearningObjectDisplay :: factory($object)->get_full_html();
			$out .= $form->toHtml();
			return $out;
		}
		
	}
	
}
?>