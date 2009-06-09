<?php
/**
 * @package application.forum.forum.component
 */
require_once dirname(__FILE__).'/../forum_manager.class.php';
require_once dirname(__FILE__).'/../forum_manager_component.class.php';
require_once dirname(__FILE__).'/../../publisher/forum_publication_publisher.class.php';
require_once dirname(__FILE__).'/../../forms/forum_publication_form.class.php';
require_once Path :: get_application_library_path(). 'repo_viewer/repo_viewer.class.php';

/**
 * Component to create a new forum_publication object
 * @author Sven Vanpoucke & Michael Kyndt
 */
class ForumManagerCreatorComponent extends ForumManagerComponent
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_BROWSE)), Translation :: get('BrowseForum')));
        $trail->add(new Breadcrumb($this->get_url(array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_BROWSE)), Translation :: get('BrowseForumPublications')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateForumPublication')));

        $object = Request :: get('object');
        $pub = new RepoViewer($this, 'forum', true);

        if(!isset($object))
        {
            $html[] =  $pub->as_html();
        }
        else
        {
            $publisher = new ForumPublicationPublisher($pub);
            $publisher->publish($object);
        }

        $this->display_header($trail);

        echo implode("\n", $html);
        echo '<div style="clear: both;"></div>';
        $this->display_footer();
    }
}
?>