<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of browserclass
 *
 * @author Pieter Hens
 */

require_once dirname(__FILE__).'/../feedback_form.class.php';
//require_once Path :: get_application_path().'/lib/portfolio/portfolio_manager/portfolio_manager.class.php';

class FeedbackManagerBrowserComponent extends FeedbackManagerComponent {

    const TITLE_MARKER = '<!-- /title -->';
    const DESCRIPTION_MARKER = '<!-- /description -->';

    private $pid;
    private $cid;
    private $user_id;
    private $action;


    function run (){

        $this->pid = Request :: get('pid');
        $this->user_id =Request :: get('user_id');
        $this->cid = Request :: get('cid');
        $this->action = Request :: get ('action');
        $application = $this->get_parent()->get_application();

        $url = $this->get_url(array('pid' => $this->pid, 'cid' => $this->cid , 'user_id' => $this->user_id , 'action' => $this->action));
        $form = new FeedbackForm($url);

        if($form->validate())
        {

            $success = $form->create_feedback($this->get_user()->get_id(),$this->pid,$this->cid,$application);
            $this->redirect($success?"":Translation::get('FeedbackNotCreated'), $success?null:true, array('pid' => $this->pid, 'cid' => $this->cid , 'user_id' => $this->user_id , 'action' => $this->action));

        }
        else
        {

            echo $this->render_create_action();

            $feedbackpublications = $this->retrieve_feedback_publications($this->pid,$this->cid,$application);

            while($feedback = $feedbackpublications->next_result())
            {

                echo $this->render_feedback($feedback);

            }

            // form to enter feedbacK
            $form->display();
        }
    }

    function render_feedback($object)
    {

        $id = $object->get_fid();
        $feedback = RepositoryDataManager :: get_instance()->retrieve_learning_object($id);
        
            $html = array();
            $html[] = '<div class="learning_object" style="background-image: url('.Theme :: get_common_image_path() . 'learning_object/' .$feedback->get_icon_name().($feedback->is_latest_version() ? '' : '_na').'.png);">';
            $html[] = '<div class="title">'. htmlentities($feedback->get_title()) .'</div>';
            $html[] = self::TITLE_MARKER;
            $html[] = $this->get_description($feedback);
            if ($this->get_user()->get_id() == $feedback->get_owner_id())
            {
                $html[] = '<div class="publication_actions">';
                $html[] = $this->render_delete_action($object);
                $html[] = $this->render_update_action($feedback);
                $html[] = '</div>';
            }
            $html[] = '</div>';
        

        return implode("\n", $html);

    }

    function get_description($object)
    {
        $description = $object->get_description();
        $parsed_description = BbcodeParser :: get_instance()->parse($description);
        return '<div class="description">' . $parsed_description . '</div>';
    }



    function render_delete_action($object)
    {
        $delete_url = $this->get_url(array(FeedbackManager::PARAM_ACTION => FeedbackManager::ACTION_DELETE_FEEDBACK,'pid' => $this->pid, 'cid' => $this->cid , 'user_id' => $this->user_id , 'action' => $this->action, 'deleteitem' => $object->get_id()));
        $delete_link = '<a href="'.$delete_url.'" onclick="return confirm(\''.addslashes(htmlentities(Translation :: get('ConfirmYourChoice'))).'\');"><img src="'.Theme :: get_common_image_path().'action_delete.png"  alt=""/></a>';
        return $delete_link;
    }

    function render_update_action($object)
    {
        $update_url = $this->get_url(array(FeedbackManager::PARAM_ACTION => FeedbackManager::ACTION_UPDATE_FEEDBACK,'pid' => $this->pid, 'cid' => $this->cid , 'user_id' => $this->user_id , 'action' => $this->action, 'updateitem' => $object->get_id()));
        $update_link = '<a href="'.$update_url.'"><img src="'.Theme :: get_common_image_path().'action_edit.png"  alt=""/></a>';
        return $update_link;
    }

    function render_create_action()
    {
        $create_url = $this->get_url(array(FeedbackManager::PARAM_ACTION => FeedbackManager::ACTION_CREATE_FEEDBACK,'pid' => $this->pid, 'cid' => $this->cid , 'user_id' => $this->user_id , 'action' => $this->action));
        $create_link = '<a style="float: right" href="'.$create_url.'"><img src="'.Theme :: get_common_image_path().'action_create.png"  alt=""/>'.Translation::get('CreateFeedback').'</a><br><br>';
        return $create_link;
    }


}
?>
