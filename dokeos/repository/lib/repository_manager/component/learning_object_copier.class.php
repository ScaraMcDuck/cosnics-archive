<?php
/**
 * $Id: browser.class.php 22148 2009-07-16 12:54:53Z vanpouckesven $
 * @package repository.repositorymanager
 *
 * @author Sven Vanpoucke
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';

class RepositoryManagerContentObjectCopierComponent extends RepositoryManagerComponent
{
	/**
     * Runs this component and displays its output.
     */
    function run()
    {
    	$lo_ids = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
    	$target_user = Request :: get(RepositoryManager :: PARAM_TARGET_USER);
    	
    	if(!is_array($lo_ids))
    	{
    		$lo_ids = array($lo_ids);
    	}
    	
    	if(count($lo_ids) == 0 || !isset($target_user))
    	{
    		$this->display_header();
    		$this->display_error_message(Translation :: get('ContentObjectAndTargetUserRequired'));
    		$this->display_footer();
    	}
    	
    	$failed = 0;
    	
    	foreach($lo_ids as $lo_id)
    	{
    		$lo = $this->retrieve_content_object($lo_id);
    		$lo->set_owner_id($target_user);
    		$lo->set_parent_id(0);
    		if(!$lo->create())
    		{
    			$failed++;
    		}
    		
    		if($lo->is_complex_content_object())
    		{
    			$this->copy_complex_children($lo_id, $lo->get_id(), $target_user);
    		}
    	}
    	
    	if(count($lo_ids) > 0)
    	{
    	 	if($failed == 0)
    	 		$message = Translation :: get('ContentObjectsCopied');
    	 	elseif($failed > 0 && $failed < count($lo_ids))
    	 		$message = Translation :: get('SomeContentObjectsNotCopied');
    	 	else 
    	 		$message = Translation :: get('ContentObjectsNotCopied');
    	}
    	else
    	{
    		if($failed == 0)
    			$message = Translation :: get('ContentObjectCopied');
    		else 
    			$message = Translation :: get('ContentObjectNotCopied');
    	}
    	
    	$this->redirect($message, ($failed > 0), array(RepositoryManager :: PARAM_ACTION => null));
    	
    	
    }
    
	function copy_complex_children($old_parent_id, $new_parent_id, $target_user)
	{
		$condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $old_parent_id, ComplexContentObjectItem :: get_table_name());
		$items = $this->retrieve_complex_content_object_items($condition);
		while($item = $items->next_result())
		{
			$lo = $this->retrieve_content_object($item->get_ref());
			$lo->set_owner_id($target_user);
			$lo->set_parent_id(0);
			$lo->create();
			
			$nitem = new ComplexContentObjectItem();
			$nitem->set_user_id($item->get_user_id());
			$nitem->set_display_order($item->get_display_order());
			$nitem->set_parent($new_parent_id);
			$nitem->set_ref($lo->get_id());
			$nitem->create();
			
			$this->copy_complex_children($item->get_ref(), $lo->get_id(), $target_user);
			
		}
	}

}
?>
