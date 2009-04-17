<?php
/**
 * @author Michael Kyndt
 */
require_once Path :: get_reporting_path(). 'lib/reporting_template.class.php';
require_once Path :: get_reporting_path().'lib/reporting_manager/reporting_manager.class.php';
class PublicationDetailReportingTemplate extends ReportingTemplate
{
	function PublicationDetailReportingTemplate($parent=null)
	{
        $this->parent = $parent;
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsPublicationDetail"),
            array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsPublicationAccess"),
            array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsPublicationUserAccess"),
            array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
	}

    /**
     * @see ReportingTemplate -> get_properties()
     */
    public static function get_properties()
    {
        $properties[ReportingTemplateRegistration :: PROPERTY_TITLE] = 'PublicationDetailReportingTemplateTitle';
        $properties[ReportingTemplateRegistration :: PROPERTY_PLATFORM] = 0;
        $properties[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION] = 'PublicationDetailReportingTemplateDescription';

        return $properties;
    }

    /**
     * @see ReportingTemplate -> to_html()
     */
    function to_html()
    {
    	//template header
        $html[] = $this->get_header();

        $html[] = '<div align="center">';
        //show visible blocks
        $html[] = $this->get_visible_reporting_blocks();
        $html[] = '</div>';
    	//template footer
        $html[] = $this->get_footer();

    	return implode("\n", $html);
    }
}
?>