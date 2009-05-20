<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of reporting_template_viewerclass
 *
 * @author Soliber
 */
require_once Path :: get_reporting_path().'lib/reporting.class.php';
require_once Path :: get_reporting_path().'lib/reporting_template_viewer.class.php';

class ToolAccessDetailsViewerComponent extends ToolComponent
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $rtv = new ReportingTemplateViewer($this);

        $classname = $_GET[ReportingManager::PARAM_TEMPLATE_NAME];

        $params = Reporting :: get_params($this);

        $trail = new BreadcrumbTrail();
        
        if(Request :: get('pcattree') != null && Request :: get('pcattree') > 0)
        $this->add_pcattree_breadcrumbs(Request :: get('pcattree'),$trail);
        
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => 'view', Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'))), WebLcmsDataManager :: get_instance()->retrieve_learning_object_publication(Request :: get('pid'))->get_learning_object()->get_title()));
        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => 'view_reporting_template', Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'), 'template_name' => Request :: get('template_name'))), Translation :: get('Reporting')));
        $this->display_header($trail);
        $rtv->show_reporting_template_by_name($classname, $params);
        $this->display_footer();
    }

    private function add_pcattree_breadcrumbs($pcattree,&$trail)
    {
        $cat = WebLcmsDataManager :: get_instance()->retrieve_learning_object_publication_category($pcattree);
        $categories[] = $cat;
        while ($cat->get_parent()!=0)
        {
            $cat = WebLcmsDataManager :: get_instance()->retrieve_learning_object_publication_category($cat->get_parent());
            $categories[] = $cat;
        }
        $categories = array_reverse($categories);
        foreach($categories as $categorie)
        {
            $trail->add(new Breadcrumb($this->get_url(array('pcattree' => $categorie->get_id())), $categorie->get_name()));
        }
    }
}
?>
