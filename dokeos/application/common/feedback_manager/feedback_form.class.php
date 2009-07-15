<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of feeback_text_formclass
 *
 * @author pieter
 */

require_once PATH :: get_admin_path().'/lib/feedback_publication.class.php';

class FeedbackForm extends FormValidator {

    private $adm;

    function FeedbackForm($action) {
        parent :: __construct('feedback_form', 'post',$action);
        $this->build_text_form();

        $this->adm = AdminDataManager :: get_instance();


    }



    function build_text_form()
    {
        $this->add_html_editor( FeedbackPublication :: PROPERTY_TEXT, 'comment', 'required');
        $this->addRule( FeedbackPublication :: PROPERTY_TEXT, Translation :: get('ThisFieldIsRequired'), 'required');
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function create_feedback($owner,$pid,$cid,$application){
        $feedback = new Feedback();
       
        $feedback->set_id($fid);
        $description = $this->exportValues(FeedbackPublication :: PROPERTY_TEXT);
        $feedback->set_description($description[FeedbackPublication :: PROPERTY_TEXT]);
        $feedback->set_title('Fast feedback');
        $feedback->set_owner_id($owner);

      //  $feedback->set_owner_id(1);
        $feedback->set_parent_id(0);
        $feedback->set_icon('informative');
        $feedback->create();
        $fb = new FeedbackPublication();
        //$values = $this->exportValues();
        $fb->set_application($application);
        $fb->set_cid($cid);
        $fid = $feedback->get_id();//$this->adm->get_next_feedback_id();
        $fb->set_fid($fid);
        $fb->set_pid($pid);
        
        //echo 'pid -> '.$pid.' - cid ->'.$cid;
        return $fb->create();
    }

}
?>
