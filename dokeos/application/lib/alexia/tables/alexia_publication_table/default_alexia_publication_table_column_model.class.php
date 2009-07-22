<?php
/**
 * @package application.lib.alexiar.alexia_publication_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object.class.php';
require_once dirname(__FILE__) . '/../../alexia_publication.class.php';

class DefaultAlexiaPublicationTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultAlexiaPublicationTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return AlexiaTableColumn[]
     */
    private static function get_default_columns()
    {
        $rdm = RepositoryDataManager :: get_instance();
        $learning_object_alias = $rdm->get_database()->get_alias(LearningObject :: get_table_name());

        $columns = array();
        $columns[] = new ObjectTableColumn(LearningObject :: PROPERTY_TITLE, true, $learning_object_alias);
        $columns[] = new ObjectTableColumn(LearningObject :: PROPERTY_DESCRIPTION, true, $learning_object_alias);
        return $columns;
    }
}
?>