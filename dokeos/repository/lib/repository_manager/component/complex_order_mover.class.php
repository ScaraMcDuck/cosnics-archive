<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__) . '/../repository_manager.class.php';
require_once dirname(__FILE__) . '/../repository_manager_component.class.php';
/**
 * Repository manager component which provides functionality to delete a
 * learning object from the users repository.
 */
class RepositoryManagerComplexOrderMoverComponent extends RepositoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = $_GET[RepositoryManager :: PARAM_CLOI_ID];
        $root = $_GET[RepositoryManager :: PARAM_CLOI_ROOT_ID];
        $direction = $_GET[RepositoryManager :: PARAM_MOVE_DIRECTION];
        $succes = true;

        if (isset($id))
        {
            $cloi = $this->retrieve_complex_learning_object_item($id);
            $parent = $cloi->get_parent();
            $display_order = $cloi->get_display_order();
            $new_place = ($display_order + ($direction == RepositoryManager :: PARAM_DIRECTION_UP ? - 1 : 1));
            $cloi->set_display_order($new_place);

            $conditions[] = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_DISPLAY_ORDER, $new_place);
            $conditions[] = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $parent);
            $condition = new AndCondition($conditions);
            $items = $this->retrieve_complex_learning_object_items($condition);
            $new_cloi = $items->next_result();
            $new_cloi->set_display_order($display_order);

            if (! $cloi->update() || ! $new_cloi->update())
            {
                $succes = false;
            }

            $this->redirect($succes ? Translation :: get('ComplexLearningObjectItemsMoved') : Translation :: get('ComplexLearningObjectItemsNotMoved'), false, array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_COMPLEX_LEARNING_OBJECTS, RepositoryManager :: PARAM_CLOI_ID => $parent, RepositoryManager :: PARAM_CLOI_ROOT_ID => $root, 'publish' => $_GET['publish'], 'clo_action' => 'organise'));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
}
?>