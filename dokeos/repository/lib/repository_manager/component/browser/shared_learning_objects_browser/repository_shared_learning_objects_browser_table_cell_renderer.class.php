<?php
/**
 * $Id: repository_browser_table_cell_renderer.class.php 21940 2009-07-09 09:45:03Z scara84 $
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__) . '/repository_shared_learning_objects_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../../learning_object_table/default_shared_learning_objects_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../../learning_object.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class RepositorySharedLearningObjectsBrowserTableCellRenderer extends DefaultSharedLearningObjectsTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function RepositorySharedLearningObjectsBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $learning_object)
    {
        if ($column === RepositorySharedLearningObjectsBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($learning_object);
        }

        switch ($column->get_name())
        {
            case LearningObject :: PROPERTY_TYPE :
                return '<a href="' . htmlentities($this->browser->get_type_filter_url($learning_object->get_type())) . '">' . parent :: render_cell($column, $learning_object) . '</a>';
            case LearningObject :: PROPERTY_TITLE :
                $title = parent :: render_cell($column, $learning_object);
                $title_short = DokeosUtilities :: truncate_string($title, 53, false);
                return '<a href="' . htmlentities($this->browser->get_learning_object_viewing_url($learning_object)) . '" title="' . $title . '">' . $title_short . '</a>';
            case LearningObject :: PROPERTY_MODIFICATION_DATE :
                return Text :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), $learning_object->get_modification_date());
            case LearningObject :: PROPERTY_OWNER_ID:
                return UserDataManager :: get_instance()->retrieve_user($learning_object->get_owner_id())->get_fullname();
        }
        return parent :: render_cell($column, $learning_object);
    }

    /**
     * Gets the action links to display
     * @param LearningObject $learning_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($learning_object)
    {
            $toolbar_data = array();

            if($this->browser->has_right($learning_object->get_id(), RepositoryRights :: VIEW_RIGHT))
                $toolbar_data[] = array('href' => $this->browser->get_learning_object_viewing_url($learning_object), 'label' => Translation :: get('View'), 'img' => Theme :: get_common_image_path() . 'action_visible.png');
            else
                $toolbar_data[] = array('img' => Theme :: get_common_image_path() . 'action_visible_na.png');
            if($this->browser->has_right($learning_object->get_id(), RepositoryRights :: USE_RIGHT))
                $toolbar_data[] = array('href' => $this->browser->get_publish_learning_object_url($learning_object), 'img' => Theme :: get_common_image_path() . 'action_publish.png', 'label' => Translation :: get('Publish'));
            else
                $toolbar_data[] = array('img' => Theme :: get_common_image_path() . 'action_publish_na.png');
            if($this->browser->has_right($learning_object->get_id(), RepositoryRights :: REUSE_RIGHT))
                $toolbar_data[] = array('href' => $this->browser->get_reuse_learning_object_url($learning_object), 'label' => Translation :: get('ReUse'), 'img' => Theme :: get_common_image_path() . 'action_reuse.png');
            else
                $toolbar_data[] = array('img' => Theme :: get_common_image_path() . 'action_reuse_na.png');

            if ($learning_object->is_complex_learning_object())
            {
                $toolbar_data[] = array('href' => $this->browser->get_browse_complex_learning_object_url($learning_object), 'img' => Theme :: get_common_image_path() . 'action_browser.png', 'label' => Translation :: get('BrowseComplex'));
            }

            return DokeosUtilities :: build_toolbar($toolbar_data);
    }
}
?>