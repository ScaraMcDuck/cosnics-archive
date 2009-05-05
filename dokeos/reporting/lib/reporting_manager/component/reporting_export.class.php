<?php
/**
 * @author Michael
 */
require_once Path :: get_reporting_path().'lib/reporting.class.php';
require_once Path :: get_reporting_path().'lib/reporting_formatter.class.php';
require_once Path :: get_library_path().'export/export.class.php';
require_once Path :: get_reporting_path().'lib/reporting_exporter.class.php';

class ReportingManagerReportingExportComponent extends ReportingManagerComponent {
    function run()
    {
        $rte = new ReportingExporter($this);

        if(isset($_GET[ReportingManager::PARAM_REPORTING_BLOCK_ID]))
            $rbi = $_GET[ReportingManager::PARAM_REPORTING_BLOCK_ID];
        else if(isset($_GET[ReportingManager::PARAM_TEMPLATE_ID]))
            $ti = $_GET[ReportingManager::PARAM_TEMPLATE_ID];

        $params = $_GET[ReportingManager::PARAM_TEMPLATE_FUNCTION_PARAMETERS];

        $_SESSION[ReportingManager::PARAM_REPORTING_PARENT] = $this;

        $export = $_GET[ReportingManager::PARAM_EXPORT_TYPE];

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
