<?php
/**
 * @package application.lib.profiler.profile_publication_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once Path :: get_user_path() . 'lib/user.class.php';
require_once dirname(__FILE__) . '/../system_announcement_publication.class.php';

class DefaultSystemAnnouncementPublicationTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultSystemAnnouncementPublicationTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return ProfileTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(SystemAnnouncementPublication :: PROPERTY_LEARNING_OBJECT_ID);
        $columns[] = new ObjectTableColumn(SystemAnnouncementPublication :: PROPERTY_PUBLISHED);
        return $columns;
    }
}
?>