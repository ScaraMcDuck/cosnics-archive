<?php
/**
 * @author Michael Kyndt
 * @todo:
 * Template configuration:
 * Able to change name, description etc
 * 2 listboxes: one with available reporting blocks for the app, one with
 * reporting blocks already in template.
 */
require_once Path :: get_reporting_path(). 'lib/reporting_template.class.php';
class CourseUserReportingTemplate extends ReportingTemplate
{
	function CourseUserReportingTemplate($parent=null)
	{
        $this->parent = $parent;
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("UserInformation"),ReportingTemplate :: REPORTING_BLOCK_VISIBLE);
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("UserPlatformStatistics"),ReportingTemplate :: REPORTING_BLOCK_VISIBLE);
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("CourseInformation"),ReportingTemplate :: REPORTING_BLOCK_VISIBLE);
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("CourseUserLearningpathInformation"),ReportingTemplate :: REPORTING_BLOCK_VISIBLE);
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("CourseUserExerciseInformation"),ReportingTemplate :: REPORTING_BLOCK_VISIBLE);
	}

    /**
     * @see ReportingTemplate -> get_properties()
     */
    public static function get_properties()
    {
        //name vervangen door title
        $properties['title'] = Translation :: get('CourseUserReportingTemplateTitle');
        $properties['platform'] = 0;
        $properties['description'] = Translation :: get('CourseUserReportingTemplateDescription');

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

        //show visible blocks
        $html[] = $this->get_visible_reporting_blocks();

    	//template footer
        $html[] = $this->get_footer();

    	return implode("\n", $html);
    }
}
?>