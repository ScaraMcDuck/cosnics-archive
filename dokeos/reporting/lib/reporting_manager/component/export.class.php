<?php
/**
 * @author Michael
 */
require_once Path :: get_reporting_path().'lib/reporting.class.php';
require_once Path :: get_reporting_path().'lib/reporting_formatter.class.php';
require_once Path :: get_library_path().'export/export.class.php';
require_once Path :: get_reporting_path().'lib/reporting_exporter.class.php';

class ReportingManagerExportComponent extends ReportingManagerComponent {
    function run()
    {
        $rte = new ReportingExporter($this);

        if(Request :: get(ReportingManager::PARAM_REPORTING_BLOCK_ID))
            $rbi = Request :: get(ReportingManager::PARAM_REPORTING_BLOCK_ID);
        else if(Request :: get(ReportingManager::PARAM_TEMPLATE_ID))
            $ti = Request :: get(ReportingManager::PARAM_TEMPLATE_ID);

        $params = Request :: get(ReportingManager::PARAM_TEMPLATE_FUNCTION_PARAMETERS);

        $_SESSION[ReportingManager::PARAM_REPORTING_PARENT] = $this;

        $export = Request :: get(ReportingManager::PARAM_EXPORT_TYPE);

        if(isset($rbi))
        {
            $rte->export_reporting_block($rbi,$export,$params);
        }else if(isset($ti))
        {
            $rte->export_template($ti,$export,$params);
        }
    }//run
}
?>
