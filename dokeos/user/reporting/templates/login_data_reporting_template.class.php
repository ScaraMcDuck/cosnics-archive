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
class LoginDataReportingTemplate extends ReportingTemplate
{
	function LoginDataReportingTemplate($parent=null)
	{
        $this->parent = $parent;
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("Browsers"),0);
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("Countries"),0);
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("Os"),0);
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("Providers"),0);
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("Referers"),0);
	}

    /**
     * @see ReportingTemplate -> get_properties()
     */
    public static function get_properties()
    {
        $properties['name'] = 'Login Data';
        $properties['platform'] = 1;
        $properties['description'] = 'The template description';

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
        $html[] = $this->get_menu();

        //show visible blocks
        $html[] = $this->get_visible_reporting_blocks();

    	//template footer
        $html[] = $this->get_footer();

    	return implode("\n", $html);
    }
}
?>