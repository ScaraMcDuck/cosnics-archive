<?php
/**
 * @author Michael Kyndt
 */
require_once Path :: get_reporting_path(). 'lib/reporting_template.class.php';
require_once Path :: get_reporting_path().'lib/reporting_manager/reporting_manager.class.php';
class CourseReportingTemplate extends ReportingTemplate
{
	function CourseReportingTemplate($parent=null)
	{
        $this->parent = $parent;
	}

    /**
     * @see ReportingTemplate -> get_properties()
     */
    public static function get_properties()
    {
        $properties[ReportingTemplateRegistration :: PROPERTY_TITLE] = 'CourseReportingTemplateTitle';
        $properties[ReportingTemplateRegistration :: PROPERTY_PLATFORM] = 0;
        $properties[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION] = 'CourseReportingTemplateDescription';

        return $properties;
    }

    /**
     * @see ReportingTemplate -> to_html()
     */
    function to_html()
    {
    	//template header
        $html[] = $this->get_header();

        $params = $_GET[ReportingManager::PARAM_TEMPLATE_FUNCTION_PARAMETERS];

        $html[] = '<div align="center">';
        $url = ReportingManager :: get_reporting_template_registration_url('CourseLearnerTrackerReportingTemplate',$params);
        $html[] = '<a href="'.$url.'" />'.Translation :: get('LearnerTracker').'</a> | ';
        $url = ReportingManager :: get_reporting_template_registration_url('CourseTrainingTrackerReportingTemplate',$params);
        $html[] = '<a href="'.$url.'" />'.Translation :: get('TrainingTracker').'</a>';
        $html[] = '</div>';

        //@todo show above templates in this div
        $html[] = '<div id="content"></div>';
        //template menu
        //$html[] = $this->get_menu();

        //show visible blocks
        //$html[] = $this->get_visible_reporting_blocks();

    	//template footer
        $html[] = $this->get_footer();

    	return implode("\n", $html);
    }
}
?>