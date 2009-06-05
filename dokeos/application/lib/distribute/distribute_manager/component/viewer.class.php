<?php
/**
 * @package application.lib.profiler.profiler_manager
 */
require_once dirname(__FILE__).'/../distribute_manager.class.php';
require_once dirname(__FILE__).'/../distribute_manager_component.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object_display.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';

class DistributeManagerViewerComponent extends DistributeManagerComponent
{
    private $action_bar;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(DistributeManager :: PARAM_ANNOUNCEMENT_DISTRIBUTION);

        if ($id)
        {
            $announcement_distribution = $this->retrieve_announcement_distribution($id);

            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => DistributeManager :: ACTION_BROWSE_ANNOUNCEMENT_DISTRIBUTIONS)), Translation :: get('Distribute')));
            $trail->add(new Breadcrumb($this->get_url(array(DistributeManager :: PARAM_ANNOUNCEMENT_DISTRIBUTION => $id)), $announcement_distribution->get_distribution_object()->get_title()));
            $trail->add_help('distribute general');

            $this->action_bar = $this->get_action_bar();
            $output = $this->get_distribution_as_html($announcement_distribution);

            $this->display_header($trail);
            echo '<br /><a name="top"></a>';
            echo $this->action_bar->as_html() . '<br />';
            echo '<div id="action_bar_browser">';
            echo $output;
            echo '</div>';

            $this->display_footer();
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoAnnouncementDistributionSelected')));
        }
    }

    function get_distribution_as_html($announcement_distribution)
    {
        $learning_object = $announcement_distribution->get_distribution_object();
        $display = LearningObjectDisplay :: factory($learning_object);
        $html = array();

        $html[] = $display->get_full_html();

//        $back = $this->get_url();
//        $edit_url = $this->get_publication_editing_url($event);
//        $delete_url = $this->get_publication_deleting_url($event);
//
//        $this->action_bar->add_common_action(new ToolbarItem(Translation :: get('Back'), Theme :: get_common_image_path().'action_prev.png', $back));
//        $this->action_bar->add_common_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', $edit_url));
//        $this->action_bar->add_common_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path().'action_delete.png', $delete_url));

        return implode("\n",$html);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        return $action_bar;
    }
}
?>