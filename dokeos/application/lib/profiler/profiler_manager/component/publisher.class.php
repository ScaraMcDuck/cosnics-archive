<?php
/**
 * @package application.lib.profiler.profiler_manager
 */
require_once dirname(__FILE__) . '/../profiler_manager.class.php';
require_once dirname(__FILE__) . '/../profiler_manager_component.class.php';
require_once dirname(__FILE__) . '/../../publisher/profile_publisher.class.php';
require_once Path :: get_application_library_path() . 'repo_viewer/repo_viewer.class.php';

class ProfilerManagerPublisherComponent extends ProfilerManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => ProfilerManager :: ACTION_BROWSE_PROFILES)), Translation :: get('MyProfiler')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('PublishProfile')));
        $trail->add_help('profiler general');
        
        $object = Request :: get('object');
        $pub = new RepoViewer($this, 'profile', true);
        
        if (! isset($object))
        {
            $html[] = $pub->as_html();
        }
        else
        {
            //$html[] = 'LearningObject: ';
            $publisher = new ProfilePublisher($pub);
            $html[] = $publisher->get_publications_form($object);
        }
        
        $this->display_header($trail);
        echo implode("\n", $html);
        echo '<div style="clear: both;"></div>';
        $this->display_footer();
    }
}
?>