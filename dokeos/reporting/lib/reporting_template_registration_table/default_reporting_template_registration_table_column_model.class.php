<?php
/**
 * @author: Michael Kyndt
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once Path :: get_reporting_path().'lib/reporting_template_registration.class.php';

/**
 * TODO: Add comment
 */
class DefaultReportingTemplateRegistrationTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultReportingTemplateRegistrationTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 1);
	}
	/**
	 * Gets the default columns for this model
	 * @return LearningObjectTableColumn[]
	 */
	private static function get_default_columns()
	{
		$columns = array();
        $columns[] = new ObjectTableColumn(ReportingTemplateRegistration :: PROPERTY_APPLICATION, true);
		$columns[] = new ObjectTableColumn(ReportingTemplateRegistration :: PROPERTY_TITLE, true);
		$columns[] = new ObjectTableColumn(ReportingTemplateRegistration :: PROPERTY_DESCRIPTION, true);
		return $columns;
	}
}
?>