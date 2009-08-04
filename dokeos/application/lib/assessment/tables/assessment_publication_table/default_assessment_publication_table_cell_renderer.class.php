<?php
/**
 * @package assessment.tables.assessment_publication_table
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../assessment_publication.class.php';

/**
 * Default cell renderer for the assessment_publication table
 *
 * @author Sven Vanpoucke
 * @author 
 */
class DefaultAssessmentPublicationTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultAssessmentPublicationTableCellRenderer()
	{
	}

	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param AssessmentPublication $assessment_publication - The assessment_publication
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $assessment_publication)
	{
		$learning_object = $assessment_publication->get_publication_object();
		
		switch ($column->get_name())
		{
            case LearningObject :: PROPERTY_TITLE :
                return $learning_object->get_title();
            case LearningObject :: PROPERTY_DESCRIPTION :
                return DokeosUtilities :: truncate_string($learning_object->get_description(), 200);
			case AssessmentPublication :: PROPERTY_FROM_DATE :
				return $assessment_publication->get_from_date();
			case AssessmentPublication :: PROPERTY_TO_DATE :
				return $assessment_publication->get_to_date();
			case AssessmentPublication :: PROPERTY_PUBLISHER :
				return $assessment_publication->get_publisher();
			default :
				return '&nbsp;';
		}
	}

	function render_id_cell($object)
	{
		return $object->get_id();
	}
}
?>