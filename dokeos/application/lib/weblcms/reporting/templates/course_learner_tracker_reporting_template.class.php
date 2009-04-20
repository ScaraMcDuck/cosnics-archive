<?php
/**
 * @author Michael Kyndt
 */
require_once Path :: get_reporting_path(). 'lib/reporting_template.class.php';
class CourseLearnerTrackerReportingTemplate extends ReportingTemplate
{
	function CourseLearnerTrackerReportingTemplate($parent=null)
	{
        $this->parent = $parent;
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("UserTracking"),
            array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
	}

    /**
     * @see ReportingTemplate -> get_properties()
     */
    public static function get_properties()
    {
        $properties[ReportingTemplateRegistration :: PROPERTY_TITLE] = 'CourseLearnerTrackerReportingTemplateTitle';
        $properties[ReportingTemplateRegistration :: PROPERTY_PLATFORM] = 0;
        $properties[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION] = 'CourseLearnerTrackerReportingTemplateDescription';

        return $properties;
    }

    /**
     * @see ReportingTemplate -> to_html()
     */
    function to_html()
    {
    	//template header
        $html[] = $this->get_header();

        //template menu
        //$html[] = $this->get_menu();
        $params = $_GET[ReportingManager::PARAM_TEMPLATE_FUNCTION_PARAMETERS];
        $html[] = '<div align="center">';
        $url = ReportingManager :: get_reporting_template_registration_url('CourseLearnerTrackerReportingTemplate',$params);
        //$html[] = '<a href="'.$url.'" />'.Translation :: get('LearnerTracker').'</a> | ';
        $html[] = Translation :: get('CourseLearnerTrackerReportingTemplateTitle').' | ';
        $url = ReportingManager :: get_reporting_template_registration_url('CourseTrainingTrackerReportingTemplate',$params);
        $html[] = '<a href="'.$url.'" />'.Translation :: get('CourseTrainingTrackerReportingTemplateTitle').'</a>';
        $html[] = '</div><br />';
        //show visible blocks
        $html[] = $this->get_visible_reporting_blocks();

    	//template footer
        $html[] = $this->get_footer();

    	return implode("\n", $html);
    }
}
?>