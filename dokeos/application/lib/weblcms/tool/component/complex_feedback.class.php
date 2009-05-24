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

		$object = $_GET['object'];
		$this->pub = new LearningObjectRepoViewer($this, 'feedback', true);
		$this->pub->set_parameter(Tool :: PARAM_ACTION, Tool :: ACTION_FEEDBACK_CLOI);
		if(isset($_GET['pid']))
            $this->pub->set_parameter('pid', $_GET['pid']);

		if(isset($_GET['cid']))
			$this->pub->set_parameter('cid', $_GET['cid']);

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
			$this->cid = $_GET['cid'];
			$this->pid = $_GET['pid'];

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
            $this->redirect(Translation :: get('FeedbackAdded'), '', array(Tool :: PARAM_ACTION => Request :: get('cid')!=null?'discuss':'view_item', Request :: get('cid')!=null?'cid':'pid' => $this->cid, 'pid' => $this->pid));



		}
    }
}
?>
