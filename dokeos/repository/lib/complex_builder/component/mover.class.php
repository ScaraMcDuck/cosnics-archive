<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../complex_builder.class.php';
require_once dirname(__FILE__).'/../complex_builder_component.class.php';
/**
 * Repository manager component which provides functionality to delete a
 * learning object from the users repository.
 */
class ComplexBuilderMoverComponent extends ComplexBuilderComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$id = Request :: get(ComplexBuilder :: PARAM_CLOI_ID);
		$root = Request :: get(ComplexBuilder :: PARAM_ROOT_LO);
		$direction = Request :: get(ComplexBuilder :: PARAM_DIRECTION);
		$succes = true;
		
		if (isset($id))
		{
			$rdm = RepositoryDataManager :: get_instance();
			$cloi = $rdm->retrieve_complex_learning_object_item($id);
			$parent = $cloi->get_parent();
			
			$display_order = $cloi->get_display_order();
			$new_place = ($display_order + ($direction == RepositoryManager :: PARAM_DIRECTION_UP?-1:1));
			$cloi->set_display_order($new_place);
			
			$conditions[] = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_DISPLAY_ORDER, $new_place);
			$conditions[] = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $parent);
			$condition = new AndCondition($conditions);
			$items = $rdm->retrieve_complex_learning_object_items($condition);
			$new_cloi = $items->next_result();
			$new_cloi->set_display_order($display_order);
			
			if(!$cloi->update() || !$new_cloi->update())
			{
				$succes = false;
			}

			if($parent == $root) $parent = null;
			
			$this->redirect($succes?Translation :: get('ComplexLearningObjectItemsMoved'):Translation :: get('ComplexLearningObjectItemsNotMoved'), false, 
				array('go' => 'build_complex',
					  ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE_CLO,  
				      ComplexBuilder :: PARAM_ROOT_LO => $root, 
				      'publish' => Request :: get('publish')));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
		}
	}
}
?>