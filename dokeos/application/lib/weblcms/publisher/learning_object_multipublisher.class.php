<?php
/**
 * @package application.lib.profiler.publisher
 */
require_once Path :: get_application_library_path() . 'publisher/component/multipublisher.class.php';
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object_display.class.php';
require_once dirname(__FILE__).'/../learning_object_publication_form.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';

/**
 * This class represents a profile publisher component which can be used
 * to preview a learning object in the learning object publisher.
 */
class LearningObjectPublisherMultipublisherComponent extends PublisherMultipublisherComponent
{
	function get_publications_form($ids)
	{
		$html = array();
		
		$html[] = '// Learning object titles go here ...<br /><br />';

		$form = new LearningObjectPublicationForm(LearningObjectPublicationForm :: TYPE_MULTI, $ids, $this->get_parent(), $this->get_parent()->with_mail_option(), $this->get_parent()->get_course());
		if ($form->validate())
		{
			$publication = $form->create_learning_object_publications();
			
			$parameters = array();
			//$parameters['pcattree'] = $publication->get_category_id();
			
			if (!$publication)
			{
				$message = Translation :: get('ObjectNotPublished');
			}
			else
			{
				$message = Translation :: get('ObjectPublished');
			}
			
			//$this->redirect($message, (!$publication ? true : false), $parameters);
		}
		else
		{
			$html[] = $form->toHtml();
		}
		
		return implode("\n", $html);
	}
}
?>