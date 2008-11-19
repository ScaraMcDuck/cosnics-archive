<?php
/**
 * @package application.lib.profiler.publisher
 */
require_once Path :: get_application_library_path() . 'publisher/component/multipublisher.class.php';
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';
require_once dirname(__FILE__).'/../calendar_event_publication_form.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';

require_once Path :: get_application_library_path() . 'publisher/component/publication_candidate_table/publication_candidate_table.class.php';

/**
 * This class represents a profile publisher component which can be used
 * to preview a learning object in the learning object publisher.
 */
class CalendarEventPublisherMultipublisherComponent extends PublisherMultipublisherComponent
{
	function get_publications_form()
	{
		$ids = $_POST[PublicationCandidateTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
		
		$html = array();
		
		if (count($ids) > 0)
		{
			$condition = new InCondition(LearningObject :: PROPERTY_ID, $ids);
			$learning_objects = RepositoryDataManager :: get_instance()->retrieve_learning_objects(null, $condition);
			//DokeosUtilities :: order_learning_objects_by_title($learning_objects);
			
			$html[] = '<div class="learning_object padding_10">';
			$html[] = '<div class="title">'. Translation :: get('SelectedLearningObjects') .'</div>';
			$html[] = '<div class="description">';
			$html[] = '<ul class="attachments_list">';
			
			while($learning_object = $learning_objects->next_result())
			{
				$html[] = '<li><img src="'.Theme :: get_common_img_path().'treemenu_types/'.$learning_object->get_type().'.png" alt="'.htmlentities(Translation :: get(LearningObject :: type_to_class($learning_object->get_type()).'TypeName')).'"/> '.$learning_object->get_title().'</li>';
			}
			
			$html[] = '</ul>';
			$html[] = '</div>';
			$html[] = '</div>';
		}
		
		$form = new CalendarEventPublicationForm(CalendarEventPublicationForm :: TYPE_MULTI, $ids, $this->get_user(),$this->get_url($this->get_parameters()));
		if ($form->validate())
		{
			$publication = $form->create_learning_object_publications();
			
			if (!$publication)
			{
				$message = Translation :: get('ObjectNotPublished');
			}
			else
			{
				$message = Translation :: get('ObjectPublished');
			}
			
			$this->redirect($message, (!$publication ? true : false), array(PersonalCalendar :: PARAM_ACTION => PersonalCalendar :: ACTION_BROWSE_CALENDAR));
		}
		else
		{
			$html[] = $form->toHtml();
		}
		
		return implode("\n", $html);
	}
}
?>