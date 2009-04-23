<?php
/**
 * @author Michael Kyndt
 */
require_once Path :: get_reporting_path(). 'lib/reporting_template.class.php';
class CourseTrackerReportingTemplate extends ReportingTemplate
{
	function CourseTrackerReportingTemplate($parent=null)
	{
        $this->parent = $parent;
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsAverageLearningpathScore"),
            array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsAverageExerciseScore"),
            array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsLastAccessToTools"),
            array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
	}

    /**
     * @see ReportingTemplate -> get_properties()
     */
    public static function get_properties()
    {
        $properties[ReportingTemplateRegistration :: PROPERTY_TITLE] = 'CourseTrackerReportingTemplateTitle';
        $properties[ReportingTemplateRegistration :: PROPERTY_PLATFORM] = 0;
        $properties[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION] = 'CourseTrackerReportingTemplateDescription';

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
        $html[] = '<div class="reporting_center">';
        $url = ReportingManager :: get_reporting_template_registration_url('CourseStudentTrackerReportingTemplate',$params);
        $html[] = '<a href="'.$url.'" />'.Translation :: get('CourseStudentTrackerReportingTemplateTitle').'</a> | ';
        $url = ReportingManager :: get_reporting_template_registration_url('CourseTrackerReportingTemplate',$params);
        $html[] = Translation :: get('CourseTrackerReportingTemplateTitle');
        $html[] = '</div><br />';

        //show visible blocks
        $html[] = '<div style="margin-left:auto;margin-right:auto;">';
        $html[] = $this->get_visible_reporting_blocks();
        $html[] = '</div>';

    	//template footer
        $html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/reporting_hover.js' .'"></script>';
        $html[] = $this->get_footer();

    	return implode("\n", $html);
    }
}
?>