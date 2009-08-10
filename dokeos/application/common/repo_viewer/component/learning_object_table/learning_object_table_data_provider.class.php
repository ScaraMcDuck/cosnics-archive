<?php
/**
 * @package application.lib.profiler.publisher.publication_candidate_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object.class.php';
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';
require_once Path :: get_library_path().'condition/equality_condition.class.php';
require_once Path :: get_library_path().'condition/and_condition.class.php';
require_once Path :: get_library_path().'condition/or_condition.class.php';
/**
 * This class represents a data provider for a publication candidate table
 */
class LearningObjectTableDataProvider extends ObjectTableDataProvider
{
/**
 * The user id of the current active user.
 */
    private $owner;
    /**
     * The possible types of learning objects which can be selected.
     */
    private $types;
    /**
     * The search query, or null if none.
     */
    private $query;

    private $parent;
    /**
     * Constructor.
     * @param int $owner The user id of the current active user.
     * @param array $types The possible types of learning objects which can be
     * selected.
     * @param string $query The search query.
     */
    function LearningObjectTableDataProvider($owner, $types, $query = null, $parent)
    {
        $this->types = $types;
        $this->owner = $owner;
        $this->query = $query;
        $this->parent = $parent;
    }
	/*
	 * Inherited
	 */
    function get_objects($offset, $count, $order_property = null, $order_direction = null)
    {
        $order_property = $this->get_order_property($order_property);
        $order_direction = $this->get_order_direction($order_direction);

        $dm = RepositoryDataManager :: get_instance();

        return $dm->retrieve_learning_objects(null, $this->get_condition(), $order_property, $order_direction, $offset, $count);
    }
	/*
	 * Inherited
	 */
    function get_object_count()
    {
        $dm = RepositoryDataManager :: get_instance();
        return $dm->count_learning_objects(null, $this->get_condition());
    }
    /**
     * Gets the condition by which the learning objects should be selected.
     * @return Condition The condition.
     */
    function get_condition()
    {
        $owner = $this->owner;
        
        if(!Request :: get('SharedBrowser') == 1)
        {
            $category = Request :: get('category');
            $category = $category?$category:0;

            $conds = array();
            $conds[] = new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, $owner->get_id());
            $conds[] = new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID, $category);
            $conds[] = new EqualityCondition(LearningObject :: PROPERTY_STATE, 0);
            $type_cond = array();
            $types = $this->types;
            foreach ($types as $type)
            {
                $type_cond[] = new EqualityCondition(LearningObject :: PROPERTY_TYPE, $type);
            }
            $conds[] = new OrCondition($type_cond);
            $c = DokeosUtilities :: query_to_condition($this->query);
            if (!is_null($c))
            {
                $conds[] = $c;
            }

            foreach($this->parent->get_excluded_objects() as $excluded)
            {
                $conds[] = new NotCondition(new EqualityCondition(LearningObject :: PROPERTY_ID, $excluded));
            }

            return new AndCondition($conds);
        }else
        {
            $rdm = RightsDataManager :: get_instance();
            $udm = UserDataManager :: get_instance();
            $gdm = GroupDataManager :: get_instance();

            //retrieve the roles for this user and its groups
            $role_cond = new EqualityCondition(Role :: PROPERTY_USER_ID, $owner->get_id());
            $user_roles = $udm->retrieve_user_roles($role_cond);

            while($user_role = $user_roles->next_result())
            {
                if(!in_array($user_role->get_role_id(), $roles))
                    $roles[] = $user_role->get_role_id();
            }

            $groups = $gdm->retrieve_user_groups($owner->get_id());

            while($group = $groups->next_result())
            {
                $group_roles_cond = new EqualityCondition(GroupRole :: PROPERTY_GROUP_ID, $group->get_group_id());
                $group_roles = $gdm->retrieve_group_roles($group_roles_cond);
                while($group_role = $group_roles->next_result())
                {
                    if(!in_array($group_role->get_role_id(), $roles))
                        $roles[] = $group_role->get_role_id();
                }
            }

            //retrieve all the rights
            $reflect = new ReflectionClass(Application :: application_to_class(RepositoryManager :: APPLICATION_NAME) . 'Rights');
            $rights_db = $reflect->getConstants();

            foreach($rights_db as $right_name => $right_id)
            {
                if($right_id != RepositoryRights :: USE_RIGHT/* && $right_id != RepositoryRights :: REUSE_RIGHT*/)
                    continue;
                $rights[] = $right_id;
            }


            $shared_learning_objects = $rdm->retrieve_shared_learning_objects($roles,$rights);

            while($role_right_location = $shared_learning_objects->next_result())
            {
                if(!in_array($role_right_location->get_location_id(), $location_ids))
                    $location_ids[] = $role_right_location->get_location_id();

                $list[] = array('location_id' => $role_right_location->get_location_id(),'role' => $role_right_location->get_role_id(), 'right' => $role_right_location->get_right_id());
            }

            $location_cond = new InCondition('id',$location_ids);
            $locations = $rdm->retrieve_locations($location_cond);

            while($location = $locations->next_result())
            {
                $ids[] = $location->get_identifier();

                foreach ($list as $key => $value)
                {
                    if($value['location_id'] == $location->get_id())
                    {
                        $value['learning_object'] = $location->get_identifier();
                        $list[$key] = $value;
                    }
                }
            }

            if($ids)
                $conditions[] = new InCondition('id',$ids);


            if($conditions)
                $condition = new AndCondition($conditions);

            if(!$condition)
            {
                $condition = new EqualityCondition('id', -1);
            }

            return $condition;
        }
    }
}
?>