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
            	
            	if($assessment_publication->get_hidden())
            	{
            		return '<span style="color: #999999;">' . $learning_object->get_title() . '</span>';
            	}
            	
                return $learning_object->get_title();
            case LearningObject :: PROPERTY_DESCRIPTION :
				$description = DokeosUtilities :: truncate_string($learning_object->get_description(), 200);
            	
            	if($assessment_publication->get_hidden())
            	{
            		return '<span style="color: #999999;">' . $description . '</span>';
            	}
            	
                return $description;
            case LearningObject :: PROPERTY_TYPE :
                $type = Translation :: get($learning_object->get_type());
                if($type == 'assessment')
                {
                	$type = $learning_object->get_assessment_type();
                }
                	
				if($assessment_publication->get_hidden())
            	{
            		return '<span style="color: #999999;">' . $type . '</span>';
            	}
                
              	return $type;
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