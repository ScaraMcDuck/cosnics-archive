<?php
/**
 * @package application.webconferencing.webconferencing.component
 */
require_once dirname(__FILE__).'/../webconferencing_manager.class.php';
require_once dirname(__FILE__).'/../webconferencing_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/webconference_form.class.php';

/**
 * Component to edit an existing webconference object
 * @author Stefaan Vanbillemont
 */
class WebconferencingManagerWebconferenceUpdaterComponent extends WebconferencingManagerComponent
{
/**
 * Runs this component and displays its output.
 */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(WebconferencingManager :: PARAM_ACTION => WebconferencingManager :: ACTION_BROWSE_WEBCONFERENCES)), Translation :: get('BrowseWebconferences')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateWebconference')));

        $webconference = $this->retrieve_webconference(Request :: get(WebconferencingManager :: PARAM_WEBCONFERENCE));
        $form = new WebconferenceForm(WebconferenceForm :: TYPE_EDIT, $webconference, $this->get_url(array(WebconferencingManager :: PARAM_WEBCONFERENCE => $webconference->get_id())), $this->get_user());

        if($form->validate())
        {
            $success = $form->update_webconference();
            $this->redirect($success ? Translation :: get('WebconferenceUpdated') : Translation :: get('WebconferenceNotUpdated'), !$success, array(WebconferencingManager :: PARAM_ACTION => WebconferencingManager :: ACTION_BROWSE_WEBCONFERENCES));
        }
        else
        {
            $this->display_header($trail);
            $form->display();
            $this->display_footer();
        }
    }
}
?>