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

        $classname = Request :: get(ReportingManager::PARAM_TEMPLATE_NAME);

        $params = Reporting :: get_params($this);

        $trail = new BreadcrumbTrail();
        $trail->add_help('courses reporting');

        if(Request :: get('pcattree') != null && Request :: get('pcattree') > 0)
        $this->add_pcattree_breadcrumbs(Request :: get('pcattree'),$trail);

        if(Request :: get('pid') != null && Request :: get('template_name')!='CourseStudentTrackerReportingTemplate' && Request :: get('template_name')!='CourseTrackerReportingTemplate')
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => 'view', 'display_action' => 'view', Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'))), WebLcmsDataManager :: get_instance()->retrieve_learning_object_publication(Request :: get('pid'))->get_learning_object()->get_title()));

        if(!empty($params['user_id']) && Request :: get('template_name') == 'CourseStudentTrackerDetailReportingTemplate')
        {
            $user = DatabaseUserDataManager :: get_instance()->retrieve_user($params['user_id']);
            $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => 'user_details', 'users' => $params['user_id'])), $user->get_firstname().' '.$user->get_lastname()));
        }

        if(Request :: get('cid') != null)
        {
            $cloi = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_item(Request :: get('cid'));
            $wp = RepositoryDataManager :: get_instance()->retrieve_learning_object($cloi->get_ref());
            $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => 'view', 'display_action' => 'view_item', Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'), Tool :: PARAM_COMPLEX_ID => Request :: get('cid'))),$wp->get_title()));

         }

        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool ::ACTION_VIEW_REPORTING_TEMPLATE, Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'), Tool :: PARAM_COMPLEX_ID => Request :: get('cid'), 'template_name' => Request :: get('template_name'))), Translation :: get('Reporting')));

        $this->display_header($trail, true);
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
