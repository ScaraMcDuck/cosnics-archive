<?php
/**
 */
require_once dirname(__FILE__).'/../reservations_manager.class.php';
require_once dirname(__FILE__).'/../../quota_box.class.php';
require_once dirname(__FILE__).'/../reservations_manager_component.class.php';

/**
 * Component to delete a category
 */
class ReservationsManagerQuotaBoxDeleterComponent extends ReservationsManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{ 
		$ids = Request :: get(ReservationsManager :: PARAM_QUOTA_BOX_ID);
		
		if (!$this->get_user())
		{
			$this->display_header(null);
			Display :: display_error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		if($ids)
		{ 
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}
			
			$bool = true;
			$db = ReservationsDataManager :: get_instance();
			
			foreach($ids as $id)
			{
    			$box = new QuotaBox();
    			$box->set_id($id);
    			$bool &= $box->delete();
			}
			
			if(count($ids) == 1)
				$message = $bool ? 'QuotaBoxDeleted' : 'QuotaBoxNotDeleted';
			else
				$message = $bool ? 'QuotaBoxesDeleted' : 'QuotaBoxesNotDeleted';
			
			
			$this->redirect(Translation :: get($message), ($bool ? false : true), 
				array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_QUOTA_BOXES));
		}
		else
		{
			$this->display_header();
			$this->display_error_message(Translation :: get("NoObjectSelected"));
			$this->display_footer();
		}
	}

}
?>