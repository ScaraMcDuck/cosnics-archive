<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/../tool.class.php';
require_once dirname(__FILE__) . '/../tool_component.class.php';
require_once Path :: get_reporting_path().'lib/reporting_template_viewer.class.php';

class ToolReportingTemplateViewerComponent extends ToolComponent
{
    function run()
    {
        $rtv = new ReportingTemplateViewer($this);

        if(isset($_GET[ReportingManager::PARAM_TEMPLATE_NAME]))
            $classname = $_GET[ReportingManager::PARAM_TEMPLATE_NAME];
        else
            $classname = 'PublicationDetailReportingTemplate';

        $params_session = $_SESSION[ReportingManager::PARAM_TEMPLATE_FUNCTION_PARAMETERS];
        $params_get = $_GET[ReportingManager::PARAM_TEMPLATE_FUNCTION_PARAMETERS];

        foreach ($params_session as $key => $value) {
            $params[$key] = $value;
        }

        foreach ($params_get as $key => $value) {
            $params[$key] = $value;
        }

        if(!isset($params[ReportingManager::PARAM_COURSE_ID]))
        $params[ReportingManager::PARAM_COURSE_ID] = Request :: get('course');

        $trail = new BreadcrumbTrail();
        /*
         * Quick and dirty solution for wiki breadcrumbs. 
         * This can go as soon as a better way is found.
         */
        if(Request :: get('tool')=='wiki')
        {
	    	if(isset($_SESSION['wiki_title']))
	        {
	            $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI, Tool :: PARAM_PUBLICATION_ID => $_SESSION['wiki_id'])), DokeosUtilities::truncate_string($_SESSION['wiki_title'],20)));
	        }
	        if(isset($_SESSION['wiki_page_title']))
	        {
	            $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI_PAGE, Tool :: PARAM_PUBLICATION_ID => $_SESSION['wiki_id'], Tool :: PARAM_COMPLEX_ID => $_SESSION['wiki_page_id'])), DokeosUtilities::truncate_string($_SESSION['wiki_page_title'],20)));
	        }
        }
        
        $trail->add(new Breadcrumb(ReportingManager::get_reporting_template_registration_url_content($this,$classname,$params),$classname));

        if(isset($_GET['pid']))
            $params['pid'] = $_GET['pid'];

        $_SESSION[ReportingManager::PARAM_TEMPLATE_FUNCTION_PARAMETERS] = $params;

        $this->display_header($trail);
        $rtv->show_reporting_template_by_name($classname, $params);
        $this->display_footer();
    }
}
?>