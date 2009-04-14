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
                if($this->is_locked($cid)==0)
                {
                    $cloi = new ComplexLearningObjectItem();
                    $cloi->set_id($cid);
                    $cloi->delete();
                }
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

    private function is_locked(&$publication)
    {
        $publication = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_item($publication);
        $conditions[] = new EqualityCondition(ComplexLearningObjectItem::PROPERTY_PARENT,0);
        $conditions[] = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF,$publication->get_parent());
        $wiki_cloi = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items(new AndCondition($conditions))->as_array();
        return $wiki_cloi[0]->get_is_locked();
    }

}
?>