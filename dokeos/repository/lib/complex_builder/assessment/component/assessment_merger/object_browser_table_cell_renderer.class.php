<?php
/**
 * $Id: repository_browser_table_cell_renderer.class.php 21940 2009-07-09 09:45:03Z scara84 $
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__) . '/object_browser_table_column_model.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_table/default_learning_object_table_cell_renderer.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class ObjectBrowserTableCellRenderer extends DefaultLearningObjectTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param ObjectManagerBrowserComponent $browser
     */
    function ObjectBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $learning_object)
    {
        if ($column === ObjectBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($learning_object);
        }

        switch ($column->get_name())
        {
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
    	$toolbar_data[] = array(
       		'href' => $this->browser->get_question_selector_url($learning_object->get_id()), 
       		'label' => Translation :: get('SelectQuestion'), 
       		'img' => Theme :: get_common_image_path() . 'action_right.png'
       	);
       
       	return DokeosUtilities :: build_toolbar($toolbar_data);
    }
}
?>