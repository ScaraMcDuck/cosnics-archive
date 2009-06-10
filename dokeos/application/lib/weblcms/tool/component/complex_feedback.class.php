<?php

require_once dirname(__FILE__) . '/../tool.class.php';
require_once dirname(__FILE__) . '/../tool_component.class.php';
require_once dirname(__FILE__).'/../../learning_object_repo_viewer.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object/feedback/feedback.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_pub_feedback.class.php';

class ToolComplexFeedbackComponent extends ToolComponent
{
    private $pub;
    private $pid;
    private $cid;
    private $fid;

	function run()
	{
        $trail = new BreadcrumbTrail();
        $trail->add_help('courses general');

		$object = Request :: get('object');
		$this->pub = new LearningObjectRepoViewer($this, 'feedback', true);
		$this->pub->set_parameter(Tool :: PARAM_ACTION, Tool :: ACTION_FEEDBACK_CLOI);

        switch(Request :: get('tool'))
        {
            case 'learning_path':
                $tool_action = 'view_clo';
                break;
            default:
                $tool_action = 'view';
                break;
        }

		if(Request :: get('pid'))
        {
            $this->pub->set_parameter('pid', Request :: get('pid'));
            $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => $tool_action, 'display_action' => 'view', Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'))), WebLcmsDataManager :: get_instance()->retrieve_learning_object_publication(Request :: get('pid'))->get_learning_object()->get_title()));
        }

		if(Request :: get('cid'))
		{
            $this->pub->set_parameter('cid', Request :: get('cid'));
            $cloi = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_item(Request :: get('cid'));
            $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => $tool_action, 'display_action' => 'view_item', Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'), Tool :: PARAM_COMPLEX_ID => $cloi->get_id())), RepositoryDataManager :: get_instance()->retrieve_learning_object($cloi->get_ref())->get_title()));
        }

        if(Request :: get('tool') == 'wiki' || Request :: get('tool') == 'learning_path')
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => $tool_action, 'display_action' => 'discuss', Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'), Tool :: PARAM_COMPLEX_ID => Request :: get('cid'))), Translation :: get('Discuss')));
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_FEEDBACK_CLOI, 'pid' => Request :: get('pid'), 'cid' => Request :: get('cid'))), Translation :: get('AddFeedback')));

        if(!isset($object))
		{
			$html[] = '<p><a href="' . $this->get_url() . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			$html[] =  $this->pub->as_html();
			$this->display_header($trail, true);
			echo implode("\n",$html);
			$this->display_footer();
		}
		else
		{
			$feedback = new Feedback();
			$feedback->set_id($object);
            $this->fid = $feedback->get_id();
			$this->cid = Request :: get('cid');
			$this->pid = Request :: get('pid');

            /*
             * change in the feedback, create new tabel linking the feedback object to the wiki_page
             */

            //$rdm = RepositoryDataManager :: get_instance();
            $learning_object_pub_feedback = new LearningObjectPubFeedback();
            if(isset($this->cid))
                $learning_object_pub_feedback->set_cloi_id($this->cid);
            else
                $learning_object_pub_feedback->set_cloi_id(0);

            if(isset($this->pid))
                $learning_object_pub_feedback->set_publication_id($this->pid);
            else
                $learning_object_pub_feedback->set_publication_id(0);

            if(isset($this->fid))
                $learning_object_pub_feedback->set_feedback_id($this->fid);
            else
                $learning_object_pub_feedback->set_feedback_id(0);

            $learning_object_pub_feedback->create();
            if(Request :: get('tool') == 'wiki' || Request :: get('tool') == 'learning_path')
            $this->redirect(Translation :: get('FeedbackAdded'), '', array(Tool :: PARAM_ACTION => Request :: get('tool')=='learning_path'?'view_clo':'view', 'display_action' => Request :: get('cid')!=null?'discuss':'view_item', Request :: get('cid')!=null?'cid':'pid' => $this->cid, 'pid' => $this->pid));
            else
            $this->redirect(Translation :: get('FeedbackAdded'), '', array(Tool :: PARAM_ACTION => Request :: get('cid')!=null?'discuss':'view_item', Request :: get('cid')!=null?'cid':'pid' => $this->cid, 'pid' => $this->pid));



		}
    }
}
?>
