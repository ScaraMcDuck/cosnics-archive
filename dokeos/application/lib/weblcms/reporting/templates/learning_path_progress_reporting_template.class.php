<?php
/**
 * @author Michael Kyndt
 */
require_once Path :: get_reporting_path(). 'lib/reporting_template.class.php';
require_once Path :: get_reporting_path().'lib/reporting_manager/reporting_manager.class.php';
class LearningPathProgressReportingTemplate extends ReportingTemplate
{
	private $object;
	
	function LearningPathProgressReportingTemplate($parent,$id,$object)
	{
        parent :: __construct($parent,$id);
        $this->object = $object;
        
		$this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsLearningPathProgress"),
            array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
	}

    /**
     * @see ReportingTemplate -> get_properties()
     */
    public static function get_properties()
    {
        $properties[ReportingTemplateRegistration :: PROPERTY_TITLE] = 'LearningPathProgressReportingTemplateTitle';
        $properties[ReportingTemplateRegistration :: PROPERTY_PLATFORM] = 0;
        $properties[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION] = 'LearningPathProgressReportingTemplateDescription';

        return $properties;
    }

    /**
     * @see ReportingTemplate -> to_html()
     */
    function to_html()
    {
    	//template header
        $html[] = $this->get_header();

        if(Request :: get('cid'))
        {
        	$display = LearningObjectDisplay :: factory($this->object);
        	$html[] = $display->get_full_html();
        }
        
        $html[] = '<div class="reporting_center">';
        //show visible blocks
        $html[] = $this->get_visible_reporting_blocks();
        $html[] = '</div>';
    	//template footer
        $html[] = $this->get_footer();

    	return implode("\n", $html);
    }
}
?>