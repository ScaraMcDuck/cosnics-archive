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

require_once Path::get_repository_path().'/lib/learning_object/feedback/feedback_form.class.php';

class FeedbackManagerUpdaterComponent extends FeedbackManagerComponent {

    function run ()
    {
        $id = Request::get('updateitem');
        $pid = Request :: get('pid');
        $user_id =Request :: get('user_id');
		$cid = Request :: get('cid');
        $action = Request :: get ('action');

        $url = $this->get_url(array('pid' => $pid, 'cid' => $cid , 'user_id' => $user_id , 'action' => $action , FeedbackManager::PARAM_ACTION => FeedbackManager::ACTION_UPDATE_FEEDBACK, 'updateitem' => $id));

        $rdm = RepositoryDataManager ::get_instance();
        $object = $rdm-> retrieve_learning_object($id);
     
       $form = LearningObjectForm :: factory(LearningObjectForm::TYPE_EDIT, $object, 'editfeedback', 'post',$url,null, null, false);

        if($form->validate())
		{
            $success = $form->update_learning_object();
            
            $rdm->update_learning_object($form->get_learning_object());
            
            $this->redirect($success ? Translation :: get('FeedbackUpdated') : Translation :: get('FeedbackNotUpdated'), !$success, array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_USER_ID => $this->get_user_id(), 'pid' => $pid, 'cid' => $cid,'action' => 'feedback'));
			
		} else
        {

            $form->display();
        }
    }

}
?>
