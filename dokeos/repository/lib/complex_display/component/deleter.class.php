<?php
/**
 * @author Michael Kyndt
 */

class ComplexDisplayDeleterComponent extends ComplexDisplayComponent
{
    function run()
	{
		if($this->is_allowed(DELETE_RIGHT))
		{
			if(Request :: get('selected_cloi'))
			{
				$cloi_ids = Request :: get('selected_cloi');
			}
			else
			{
				$cloi_ids = $_POST['selected_cloi'];
			}

			if (!is_array($cloi_ids))
			{
				$cloi_ids = array ($cloi_ids);
			}

			foreach($cloi_ids as $cid)
			{
                {
                    $cloi = new ComplexLearningObjectItem();
                    $cloi->set_id($cid);
                    $cloi->delete();
                }

			}
			
            if(count($cloi_ids) > 1)
            {
                $message = htmlentities(Translation :: get('ComplexLearningObjectItemsDeleted'));
            }
            else
            {
                $message = htmlentities(Translation :: get('ComplexLearningObjectItemDeleted'));
            }

            $this->redirect($message, false, array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_VIEW_CLO, 'pid' => Request :: get('pid'), 'cid' => Request :: get('cid')));
		}
	}

}
?>
