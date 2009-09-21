<?php
/**
 * $Id: repository_browser_table_cell_renderer.class.php 21940 2009-07-09 09:45:03Z scara84 $
 * @package repository.repositorymanager
 */
/**
 * Cell rendere for the learning object browser table
 */
class TemplateBrowserTableCellRenderer extends DefaultLearningObjectTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function TemplateBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $learning_object)
    {
        if ($column === RepositoryBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($learning_object);
        }

        switch ($column->get_name())
        {
            case LearningObject :: PROPERTY_TITLE :
                $title = parent :: render_cell($column, $learning_object);
                $title_short = DokeosUtilities :: truncate_string($title, 53, false);
                return $title_short;
            case LearningObject :: PROPERTY_MODIFICATION_DATE :
                return Text :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), $learning_object->get_modification_date());
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
        return null;
    }
}
?>