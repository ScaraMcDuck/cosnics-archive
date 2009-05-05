<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__).'/../reporting_tool_component.class.php';
require_once Path :: get_reporting_path().'lib/reporting_exporter.class.php';

class ReportingToolExporterComponent extends ReportingToolComponent
{
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

        }else if(isset($ti))
        {
            $rte->export_template($ti,$export,$params);
        }
	}
}
?>