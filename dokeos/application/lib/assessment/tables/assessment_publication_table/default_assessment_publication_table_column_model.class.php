<?php
/**
 * @package assessment.tables.assessment_publication_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once dirname(__FILE__).'/../../assessment_publication.class.php';

/**
 * Default column model for the assessment_publication table
 *
 * @author Sven Vanpoucke
 * @author 
 */
class DefaultAssessmentPublicationTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultAssessmentPublicationTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 1);
	}

	/**
	 * Gets the default columns for this model
	 * @return Array(ObjectTableColumn)
	 */
	private static function get_default_columns()
	{
        $rdm = RepositoryDataManager :: get_instance();
        $learning_object_alias = $rdm->get_database()->get_alias(LearningObject :: get_table_name());
		
		$columns = array();
        $columns[] = new ObjectTableColumn(LearningObject :: PROPERTY_TITLE, true, $learning_object_alias);
        $columns[] = new ObjectTableColumn(LearningObject :: PROPERTY_DESCRIPTION, true, $learning_object_alias);
        $columns[] = new ObjectTableColumn(LearningObject :: PROPERTY_TYPE, true, $learning_object_alias);
		//$columns[] = new ObjectTableColumn(AssessmentPublication :: PROPERTY_FROM_DATE);
		//$columns[] = new ObjectTableColumn(AssessmentPublication :: PROPERTY_TO_DATE);
//		$columns[] = new ObjectTableColumn(AssessmentPublication :: PROPERTY_PUBLISHER);

		return $columns;
	}
}
?>