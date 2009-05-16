<?php
/**
 * @package application.lib.profiler.profiler_manager
 */
require_once dirname(__FILE__).'/../personal_calendar_manager.class.php';
require_once dirname(__FILE__).'/../personal_calendar_manager_component.class.php';
require_once dirname(__FILE__).'/../../renderer/personal_calendar_mini_month_renderer.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_display.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';

class PersonalCalendarManagerEditorComponent extends PersonalCalendarManagerComponent
{	
    private $folder;
    private $publication;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {

        $user = $this->get_user();

        if (!$user->is_platform_admin())
        {
            Display :: not_allowed();
            exit;
        }

        $id = $_GET[PersonalCalendarManager :: PARAM_CALENDAR_EVENT_ID];

        if ($id)
        {
            $calendar_event_publication = $this->retrieve_calendar_event_publication($id);

            $learning_object = $calendar_event_publication->get_publication_object();

            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb($this->get_url(array(PersonalCalendarManager :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR)), Translation :: get('PersonalCalendar')));
            $trail->add(new Breadcrumb($this->get_url(array(PersonalCalendarManager :: PARAM_ACTION => PersonalCalendarManager :: ACTION_VIEW_PUBLICATION,PersonalCalendarManager :: PARAM_CALENDAR_EVENT_ID => $id)), $learning_object->get_title()));
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Edit')));

            $form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_EDIT, $learning_object, 'edit', 'post', $this->get_url(array(PersonalCalendarManager :: PARAM_ACTION => PersonalCalendarManager :: ACTION_EDIT_PUBLICATION, PersonalCalendarManager :: PARAM_CALENDAR_EVENT_ID => $calendar_event_publication->get_id())));
            if( $form->validate())
            {
                $success = $form->update_learning_object();
                if($form->is_version())
                {
                    $publication->set_learning_object($learning_object->get_latest_version());
                    $publication->update();
                }

                $this->redirect('url', Translation :: get(($success ? 'CalendarEventPublicationUpdated' : 'CalendarEventPublicationNotUpdated')), ($success ? false : true), array(PersonalCalendarManager :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR));
            }
            else
            {
                $this->display_header($trail);
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoCalendarEventPublicationSelected')));
        }
    }
}
?>