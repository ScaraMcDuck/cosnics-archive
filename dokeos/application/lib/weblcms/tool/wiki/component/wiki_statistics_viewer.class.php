<?php

/*
 * This is the component that allows the user view all statisctics about a wiki.
 * 
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

require_once dirname(__FILE__) . '/../wiki_tool.class.php';
require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once Path :: get_repository_path().'/lib/complex_display/complex_display.class.php';

class WikiToolStatisticsViewerComponent extends WikiToolComponent
{
    function run()
	{
		/*
         *  We use the Reporting Tool, for more information about it, please read the information provided in the reporting class
         */

        $params = array();
        $params[ReportingManager :: PARAM_COURSE_ID] = Request :: get('course_id');
        $params['pid'] = Request :: get('pid');
        $url = $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_VIEW_REPORTING_TEMPLATE, 'template_name' => 'WikiReportingTemplate', Tool :: PARAM_PUBLICATION_ID => Request :: get('pid')));
        header('location: '.$url);
    }
}
?>