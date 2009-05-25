<?php
/**
 * @author Michael Kyndt
 */
require_once Path :: get_reporting_path(). 'lib/reporting_template.class.php';
class CourseStudentTrackerReportingTemplate extends ReportingTemplate
{
	function CourseStudentTrackerReportingTemplate($parent,$id,$params)
	{
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("UserTracking"),
            array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));

        parent :: __construct($parent,$id,$params);
	}

    /**
     * @see ReportingTemplate -> get_properties()
     */
    public static function get_properties()
    {
        $properties[ReportingTemplateRegistration :: PROPERTY_TITLE] = 'CourseStudentTrackerReportingTemplateTitle';
        $properties[ReportingTemplateRegistration :: PROPERTY_PLATFORM] = 0;
        $properties[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION] = 'CourseStudentTrackerReportingTemplateDescription';

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
        $params = $this->params;

        /*$url =array();
        $url[Tool :: PARAM_ACTION] = 'view_reporting_template';
        $url['template_name'] = $classname;
        foreach($params as $key => $param)
        {
            $url[$key] = $param;
        }
        unset($url['parent']);*/
        $url_params = '';
        unset($params['parent']);
        foreach($params as $key => $param)
        {
            $url_params = $url_params . '&template_parameters['.$key.']='.$param;
        }
        $url_params = $url_params . '&tool_action=view_reporting_template';

        $html[] = '<div class="reporting_center">';
        $html[] = Translation :: get('CourseStudentTrackerReportingTemplateTitle').' | ';
        $html[] = '<a href="'.$params['url'].$url_params.'&template_name=CourseTrackerReportingTemplate'.'" />'.Translation :: get('CourseTrackerReportingTemplateTitle').'</a>';
        $html[] = '</div><br />';
        //show visible blocks
        $html[] = $this->get_visible_reporting_blocks();

    	//template footer
        $html[] = $this->get_footer();

    	return implode("\n", $html);
    }
}
?>