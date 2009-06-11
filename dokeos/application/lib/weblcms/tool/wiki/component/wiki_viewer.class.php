<?php

require_once dirname(__FILE__) . '/../wiki_tool.class.php';
require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_repository_path() . 'lib/complex_display/complex_display.class.php';

class WikiToolViewerComponent extends WikiToolComponent
{
    private $cd;
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		//$this->display_header(new breadcrumbTrail());
        $this->set_parameter(Tool :: PARAM_ACTION, WikiTool :: ACTION_VIEW_WIKI);
        $this->cd = ComplexDisplay :: factory($this, 'wiki');
        $this->cd->set_root_lo(WebLcmsDataManager :: get_instance()->retrieve_learning_object_publication(Request :: get('pid'))->get_learning_object());
        //$this->display_header($this->get_breadcrumbtrail());
        $this->cd->run();
        $this->display_footer();
    }

    function get_breadcrumbtrail()
    {
        $trail = new BreadcrumbTrail();
        if(Request :: get('tool_action') != null)
        {
            $trail->add(new BreadCrumb($this->get_url(array('tool_action' => Request :: get('tool_action'), 'display_action' => Request :: get('display_action'), 'pid' => Request :: get('pid'))),$this->cd->get_root_lo()->get_title()));
        }
        if(Request :: get('display_action') != null)
        {
            if(Request :: get('cid') != null)
            {
                $cloi = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_item(Request :: get('cid'));
                $trail->add(new BreadCrumb($this->get_url(array('tool_action' => Request :: get('tool_action'), 'pid' => Request :: get('pid'), 'display_action' => Request :: get('display_action'), 'cid' => Request :: get('cid'))),RepositoryDataManager :: get_instance()->retrieve_learning_object($cloi->get_ref())->get_title()));
            }
            if(Request :: get('display_action') == 'discuss' || Request :: get('display_action') == 'history')
            $trail->add(new BreadCrumb($this->get_url(array('tool_action' => Request :: get('tool_action'), 'pid' => Request :: get('pid'), 'display_action' => Request :: get('display_action'), 'cid' => Request :: get('cid'))), Translation :: get(ucfirst(Request :: get('display_action')))));
//            if(Request :: get('display_action') == 'page_statistics' || Request :: get('display_action') == 'statistics')
//            $trail->add(new BreadCrumb($this->get_url(array('tool_action' => Request :: get('tool_action'), 'pid' => Request :: get('pid'), 'display_action' => Request :: get('display_action'), 'cid' => Request :: get('cid'))), Translation :: get('Reportiiiiing')));
        }
        return $trail;
    }
}
?>
