<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of deleterclas
 *
 * @author pieter
 */
class FeedbackManagerDeleterComponent extends FeedbackManagerComponent{
    function run()
    {
        //fouten opvang en id dynamisch ophalen
        //$id = Request :: get(FeedbackPublication :: PROPERTY_ID);

        $pid = Request :: get('pid');
        $user_id =Request :: get('user_id');
        $cid = Request :: get('cid');
        $action = Request :: get ('action');

        $id = Request :: get('deleteitem');

        if (!$this->get_user())
        {
            $this->display_header($this->get_breadcrumb_trail());
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit;
        }

        $FeedbackPublication = $this->retrieve_feedback_publication($id);

        dump( $FeedbackPublication);

        if ($FeedbackPublication->delete()){

            $message = 'FeedbackDeleted';
            $succes = true;
        }

        else
        {
            $message = 'FeedbackNotDeleted';
            $succes =false;
        }
         $this->redirect(Translation :: get($message), succes?false:true, array(Application :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO,'pid' => $pid, 'cid' => $cid , 'user_id' => $user_id , 'action' => $action ));
    }
}
?>
