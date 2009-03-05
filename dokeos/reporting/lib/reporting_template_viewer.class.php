<?php
require_once ('reporting_data_manager.class.php');

class ReportingTemplateViewer {
    
    static function render_reporting_template($reporting_template_name)
    {
    	$rpdm = ReportingDataManager :: get_instance();
    	$reporting_template = $rpdm->retrieve_reporting_template_by_name($reporting_template_name);
    	
    	return $reporting_template->to_html();
    }
}
?>