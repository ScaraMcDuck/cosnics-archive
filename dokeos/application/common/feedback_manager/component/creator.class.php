<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of updaterclass
 *
 * @author pieter
 */

require_once Path :: get_application_library_path(). 'repo_viewer/repo_viewer.class.php';

class FeedbackManagerCreatorComponent extends FeedbackManagerComponent {

    function run()
    {

        $pid = Request :: get('pid');
        $user_id =Request :: get('user_id');
        $cid = Request :: get('cid');
        $action = Request :: get ('action');
        $application = $this->get_parent()->get_application();
        $object = Request :: get('object');

        $pub = new RepoViewer($this, 'feedback', true);

        $actions = array('pid'=>$pid , 'cid' =>$cid , 'user_id' => $user_id , 'action' => 'feedback' ,FeedbackManager::PARAM_ACTION => FeedbackManager::ACTION_CREATE_FEEDBACK);
       
            foreach($actions as $type => $actie)
            {
                $pub->set_parameter($type, $actie);
            }
       

        if(!isset($object))
        {
            $html[] =  $pub->as_html();
            
        }
        else
        {
           
            $fb = new FeedbackPublication();
            $fb->set_application($application);
            $fb->set_cid($cid);
            $fid = $object;//$this->adm->get_next_feedback_id();
            $fb->set_fid($fid);
            $fb->set_pid($pid);
            $fb->create();

            $message = 'FeedbackCreated';
            echo $action;
            $this->redirect(Translation :: get($message), false, array(Application :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO,'pid' => $pid, 'cid' => $cid , 'user_id' => $user_id, 'action' => $action ));
          
        }


        echo implode("\n",$html);

    }
}
?>
