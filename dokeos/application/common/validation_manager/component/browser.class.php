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


class ValidationManagerBrowserComponent extends ValidationManagerComponent {

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


       
        $validations = $this->retrieve_validations($this->pid,$this->cid,$application);
        
        //$nofeedbacks = AdminDataManager::get_instance()->count_feedback_publications($this->pid, $this->cid, $application);



        while($validation = $validations->next_result())
        {
            
            echo $this->render_validation($validation);

        }


    }

    function render_validation($object)
    {

        
        $html = array();
        $html[] = '<div class="learning_object" style="background-image: url('.Theme :: get_common_image_path() . 'action_confirm.png);">';
        $html[] = '<div class="title">'. $this->render_title($object) ;
        //$html[] = '<span class="publication_info">';
        //$html[] = $this->render_publication_information($feedback);
        //$html[] = '</span>';
        $html[] ='</div>';
        $html[] = self::TITLE_MARKER;
        $html[] = $this->get_description($object);
       /* if ($this->get_user()->get_id() == $feedback->get_owner_id())
        {
            $html[] = '<div class="publication_actions">';
            $html[] = $this->render_delete_action($object);
            $html[] = $this->render_update_action($feedback);
            $html[] = '</div>';
        }*/
        $html[] = '</div>';


        return implode("\n", $html);

    }

    function get_description($object)
    {
        $description =  $this->format_date($object->get_validated());;
        $parsed_description = BbcodeParser :: get_instance()->parse($description);
        return '<div class="description">' . $parsed_description . '</div>';
    }

    function render_title($object)
    {
        $html = array();
        $user = UserManager::retrieve_user($object->get_owner());
        $title = Translation::get('ValidationFrom').' : '. $user->get_username().' '.$user->get_firstname();
        $html[] = htmlentities($title);
        return implode("\n",$html);
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

    function render_publication_information($feedback)
    {
        $user = UserManager::retrieve_user($feedback->get_owner_id());
        $html = array();
        $html[] = '(';
        $html[] = $user->get_username();;
        $html[] = $user->get_firstname();;
        $html[] = ' - ';
        $html[] = $this->format_date($feedback->get_creation_date());
        $html[] = ')';
        return implode("\n", $html);
    }

    function format_date($date)
    {
        $date_format = '%B %d, %Y at %I:%M %p';//Translation :: get('dateTimeFormatLong');
        return Text :: format_locale_date($date_format,$date);
    }

}
?>
