<?php

/*
 * This is the component that allows the user view all statisctics about a wiki.
 * 
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

require_once dirname(__FILE__).'/wiki_page_table/wiki_page_table.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class WikiDisplayWikiStatisticsViewerComponent extends WikiDisplayComponent
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