<?php

class WikiToolDeleterComponent extends WikiToolComponent
{
	function run()
	{        
		if($this->is_allowed(DELETE_RIGHT))
		{
           
			if(isset($_GET[WikiTool :: PARAM_COMPLEX_ID]))
				$complex_ids = $_GET[WikiTool :: PARAM_COMPLEX_ID];
			else
				$complex_ids = $_POST[WikiTool :: PARAM_COMPLEX_ID];

			if (!is_array($complex_ids))
			{                
				$complex_ids = array ($complex_ids);
			}

			$this->delete_pages($complex_ids);
		}
	}

	function delete_pages($complex_ids)
	{
		$datamanager = RepositoryDataManager :: get_instance();
		$success = true;        
        
		foreach($complex_ids as $index => $cid)
		{            
			$condition = new EqualityCondition(WikiTool :: PARAM_COMPLEX_ID, $cid);

            $clo_item = $datamanager->retrieve_complex_learning_object_item($cid);
            
            if(!$clo_item->delete())
                $success = false;            
            
		}

		if(count($complex_ids) > 1)
		{
			if ($success)
				$message = htmlentities(Translation :: get('LearningObjectPublicationsDeleted'));
			else
				$message = htmlentities(Translation :: get('SomePublicationsCouldNotBeDeleted'));
		}
		else
		{
			if ($success)
				$message = htmlentities(Translation :: get('LearningObjectPublicationDeleted'));
			else
				$message = htmlentities(Translation :: get('LearningObjectPublicationNotDeleted'));

		}

		$this->redirect(null, $message, (!$success), array('cid' => null));
	}
}
?>
