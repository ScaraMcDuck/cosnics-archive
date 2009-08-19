<?php
/**
 * @author: Michael Kyndt
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once Path :: get_reporting_path().'lib/reporting_template_registration.class.php';
/**
 * TODO: Add comment
 */
class DefaultReportingTemplateRegistrationTableCellRenderer implements ObjectTableCellRenderer
{
/**
 * Constructor
 */
    function DefaultReportingTemplateRegistrationTableCellRenderer()
    {
    }
    /**
     * Renders a table cell
     * @param LearningObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $learning_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $reporting_template_registration)
    {
        switch ($column->get_name())
        {
            case ReportingTemplateRegistration :: PROPERTY_APPLICATION:
                return Translation :: get(DokeosUtilities::underscores_to_camelcase($reporting_template_registration->get_application()));
            case ReportingTemplateRegistration :: PROPERTY_TITLE :
                return Translation :: get($reporting_template_registration->get_title());
            case ReportingTemplateRegistration :: PROPERTY_DESCRIPTION :
                $description = strip_tags($reporting_template_registration->get_description());
                $description = DokeosUtilities::truncate_string($description, 50);
                return Translation :: get($description);
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