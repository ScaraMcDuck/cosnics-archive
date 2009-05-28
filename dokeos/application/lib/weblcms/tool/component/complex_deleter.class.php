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
                //if(!WikiTool :: is_wiki_locked($cid))
                {
                    $cloi = new ComplexLearningObjectItem();
                    $cloi->set_id($cid);
                    $cloi->delete();
                }

			}
            if(empty($message))
            {
                if(count($cloi_ids) > 1)
                {
                    $message = htmlentities(Translation :: get('LearningObjectPublicationsDeleted'));
                }
                else
                {
                    $message = htmlentities(Translation :: get('LearningObjectPublicationDeleted'));
                }
            }

            $wiki = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication(Request :: get('pid'));
            if(!empty($wiki))
            {
                $wiki_homepage_cloi = WikiTool ::get_wiki_homepage($wiki->get_learning_object()->get_id());
                if(WikiTool ::get_wiki_homepage($wiki->get_learning_object()->get_id())!=null)
                $this->redirect($message, false, array(Tool :: PARAM_ACTION => 'view_item', 'cid' => $wiki_homepage_cloi->get_id(), 'pid' => Request :: get('pid')));
                else
                $this->redirect($message, false, array(Tool :: PARAM_ACTION => 'view', 'pid' => Request :: get ('pid')));
            }
            else
            {
                $this->redirect($message, false, array(Tool :: PARAM_ACTION => 'view', 'pid' => Request :: get ('pid')));
            }
		}
	}
}
?>