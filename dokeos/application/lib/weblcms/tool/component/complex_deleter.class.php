<?php

class ToolComplexDeleterComponent extends ToolComponent
{
	function run()
	{
		if($this->is_allowed(DELETE_RIGHT))
		{ 
			if(isset($_GET[Tool :: PARAM_COMPLEX_ID]))
				$cloi_ids = $_GET[Tool :: PARAM_COMPLEX_ID]; 
			else
				$cloi_ids = $_POST[Tool :: PARAM_COMPLEX_ID]; 
				
			if (!is_array($cloi_ids))
			{
				$cloi_ids = array ($cloi_ids);
			}
			
			$datamanager = RepositoryDataManager :: get_instance();
			
			foreach($cloi_ids as $index => $cid)
			{
				//$publication = $datamanager->retrieve_complex_learning_object_item($pid);
				$cloi = new ComplexLearningObjectItem();
				$cloi->set_id($cid);
				$cloi->delete();
			}
			if(count($cloi_ids) > 1)
			{
				$message = htmlentities(Translation :: get('LearningObjectPublicationsDeleted'));
			}
			else
			{
				$message = htmlentities(Translation :: get('LearningObjectPublicationDeleted'));
			}
			
			$this->redirect(null, $message, false, array(Tool :: PARAM_ACTION => 'view', 'pid' => $_GET['pid']));
		}
	}

}
?>