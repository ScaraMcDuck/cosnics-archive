<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__).'/reporting_tool_component.class.php';

class ReportingTool extends Tool
{
	const ACTION_VIEW_REPORT = 'view';
    const ACTION_EXPORT_REPORT = 'export';
	
	/**
	 * Inherited.
	 */
	function run()
	{
		$action = $this->get_action();
		$component = parent :: run();
		
		if($component) return;
		
		switch ($action)
		{
			case self :: ACTION_VIEW_REPORT :
				$component = ReportingToolComponent :: factory('Viewer', $this);
				break;
            case self :: ACTION_EXPORT_REPORT :
                $component = ReportingToolComponent :: factory('Exporter', $this);
                break;
			default :
				$component = ReportingToolComponent :: factory('Viewer', $this);
		}
		$component->run();
	}
}
?>