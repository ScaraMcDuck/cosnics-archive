<?php
/**
 * @package application.lib.profiler.profiler_manager
 */
require_once dirname(__FILE__).'/../profiler.class.php';
require_once dirname(__FILE__).'/../profiler_component.class.php';
require_once dirname(__FILE__).'/../../profile_publication_form.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_display.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';

class ProfilerEditorComponent extends ProfilerComponent
{	
    private $folder;
    private $publication;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Profiler :: PARAM_ACTION => Profiler::ACTION_BROWSE_PROFILES)), Translation :: get('MyProfiler')));

        $user = $this->get_user();

        if (!$user->is_platform_admin())
        {
            Display :: not_allowed();
            exit;
        }

        $id = $_GET[Profiler :: PARAM_PROFILE_ID];

        if ($id)
        {
            $profile_publication = $this->retrieve_profile_publication($id);

            $learning_object = $profile_publication->get_publication_object();

            $trail->add(new Breadcrumb($this->get_url(array(Profiler :: PARAM_ACTION => Profiler :: ACTION_VIEW_PUBLICATION, Profiler :: PARAM_PROFILE_ID => $id)), $learning_object->get_title()));
            $trail->add(new Breadcrumb($this->get_url(array(Profiler :: PARAM_PROFILE_ID => $id)), Translation :: get('Edit')));

            $form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_EDIT, $learning_object, 'edit', 'post', $this->get_url(array(Profiler :: PARAM_ACTION => Profiler :: ACTION_EDIT_PUBLICATION, Profiler :: PARAM_PROFILE_ID => $profile_publication->get_id())));
            if( $form->validate())
            {
                $success = $form->update_learning_object();
                if($form->is_version())
                {
                    $publication->set_learning_object($learning_object->get_latest_version());
                    $publication->update();
                }

                $condition = new EqualityCondition(ProfilerCategory :: PROPERTY_PARENT, 0);
                $cats = ProfilerDataManager :: get_instance()->retrieve_categories($condition);

                if ($cats->size() > 0)
                {
                    $publication_form = new ProfilePublicationForm(ProfilePublicationForm :: TYPE_SINGLE, $profile_publication->get_publication_object(),$this->get_user(), $this->get_url(array(Profiler :: PARAM_ACTION => Profiler :: ACTION_EDIT_PUBLICATION, Profiler :: PARAM_PROFILE_ID => $profile_publication->get_id(), 'validated' => '1')));
                    $publication_form->set_profile_publication($profile_publication);

                    if( $publication_form->validate())
                    {
                        $success = $publication_form->update_learning_object_publication();
                        $this->redirect('url', Translation :: get(($success ? 'ProfilePublicationUpdated' : 'ProfilePublicationNotUpdated')), ($success ? false : true), array(Profiler :: PARAM_ACTION => Profiler :: ACTION_BROWSE_PROFILES));
                    }
                    else
                    {
                        $this->display_header($trail);
                        echo LearningObjectDisplay :: factory($profile_publication->get_publication_object())->get_full_html();
                        $publication_form->display();
                        $this->display_footer();
                        exit;
                    }
                }
                else
                {
                    $this->redirect('url', Translation :: get(($success ? 'ProfilePublicationUpdated' : 'ProfilePublicationNotUpdated')), ($success ? false : true), array(Profiler :: PARAM_ACTION => Profiler :: ACTION_BROWSE_PROFILES));
                }
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