<?php
/**
 * $Id$
 * @package repository
 */
require_once Path :: get_library_path().'configuration/configuration.class.php';
require_once dirname(__FILE__).'/learning_object_publication_attributes.class.php';
require_once dirname(__FILE__).'/learning_object.class.php';
require_once dirname(__FILE__).'/complex_learning_object_item.class.php';
require_once dirname(__FILE__).'/data_manager/database/database_learning_object_result_set.class.php';
require_once dirname(__FILE__).'/../../application/lib/application.class.php';
require_once Path :: get_home_path() . 'lib/home_data_manager.class.php';
require_once Path :: get_admin_path().'lib/admin_manager/admin_manager.class.php';

/**
 *	This is a skeleton for a data manager for the learning object repository.
 *	Data managers must extend this class and implement its abstract methods.
 *	If the user configuration dictates that the "database" data manager is to
 *	be used, this class will automatically attempt to instantiate
 *	"DatabaseRepositoryDataManager"; hence, this naming convention must be
 *	respected for all extensions of this class.
 *
 *	@author Tim De Pauw
 *  @author Hans De Bisschop
 *  @author Dieter De Neef
 */
abstract class RepositoryDataManager
{
	/**
	 * Instance of this class for the singleton pattern.
	 */
	private static $instance;

	/**
	 * Associative array that maps learning object types to their
	 * corresponding array of property names.
	 */
	private $typeProperties;

	/**
	 * Array which contains the registered applications running on top of this
	 * repositorydatamanager
	 */
	private $applications;

	/**
	 * Constructor.
	 */
	protected function RepositoryDataManager()
	{
		$this->initialize();
		$this->typeProperties = array ();
		$this->load_types();
		$this->applications = array();
	}

	/**
	 * Uses a singleton pattern and a factory pattern to return the data
	 * manager. The configuration determines which data manager class is to
	 * be instantiated.
	 * @return RepositoryDataManager The data manager.
	 */
	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.strtolower($type).'.class.php';
			$class = $type.'RepositoryDataManager';
			self :: $instance = new $class ();
		}
		return self :: $instance;
	}

	/**
	 * Returns the learning object types registered with the data manager.
	 * @param boolean $only_master_types Only return the master type learning
	 * objects (which can exist on their own). Returns all learning object types
	 * by default.
	 * @return array The types.
	 */
	function get_registered_types($only_master_types = false)
	{
		$adm = AdminDataManager :: get_instance();
		$condition = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_LEARNING_OBJECT);
		
		$learning_objects = $adm->retrieve_registrations($condition);
		$active_learning_objects = array();
		
		while ($learning_object = $learning_objects->next_result())
		{
			$active_learning_objects[] = $learning_object->get_name();
		}
		
		return $active_learning_objects;
	}

	/**
	 * Is the learning object attached to another one ?
	 * @param LearningObject The learning object.
	 * @return boolean Is Attached.
	 */
	abstract function is_attached($object, $type = null);

	/**
	 * Checks if a type name corresponds to an extended learning object type.
	 * @param string $type The type name.
	 * @return boolean True if the corresponding type is extended, false
	 *                 otherwise.
	 */
	function is_extended_type($type)
	{
		//echo $type;
		$temp_class = LearningObject :: factory($type);
		$has_additional_properties = count($temp_class->get_additional_property_names()) > 0;
		unset($temp_class);
		return $has_additional_properties;
	}

	/**
	 * Returns the root category of a user's repository.
	 * @param int $owner The user ID of the owner.
	 * @return Category The root category of this user's repository.
	 */
//	function retrieve_root_category($owner)
//	{
//		$condition1 = new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, $owner);
//		$condition2 = new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID, 0);
//		$condition = new AndCondition($condition1, $condition2);
//		$objects = $this->retrieve_learning_objects('category', $condition, null, null, 0, 1, -1);
//		return $objects->next_result();
//	}

	/**
	 * Creates a root category for the given user
	 * @param int $user_id The id of the user for which the category should be
	 * created.
	 * @return Categroy The newly created root category of the user's repository
	 */
//	function create_root_category($user_id)
//	{
//		$object = new Category();
//		$object->set_owner_id($user_id);
//		$object->set_title(Translation :: get('MyRepository'));
//		$object->set_description('...');
//		$object->create();
//		return $object;
//	}

	/**
	 * Determines whether the learning object with the given ID has been
	 * published in any of the registered applications.
	 * @param int $id The ID of the learning object.
	 * @return boolean True if the learning object has been published anywhere,
	 *                 false otherwise.
	 */
	function learning_object_is_published($id)
	{
		$applications = $this->get_registered_applications();
		$result = false;
		foreach($applications as $index => $application_name)
		{
			$application = Application::factory($application_name);
			if ($application->learning_object_is_published($id))
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Determines whether a learning object with the given IDs has been
	 * published in any of the registered applications.
	 * @param array $ids The IDs of the learning objects.
	 * @return boolean True if one of the given learning objects has been
	 * published anywhere, false otherwise.
	 */
	function any_learning_object_is_published($ids)
	{
		$applications = $this->get_registered_applications();
		$result = false;
		foreach($applications as $index => $application_name)
		{
			
			$application = Application::factory($application_name);
			if ($application->any_learning_object_is_published($ids))
			{ 
				return true;
			}
		}
		
		$admin = new AdminManager();
		if ($admin->any_learning_object_is_published($ids))
		{
			return true;
		}
		
		return false;
	}

	/**
	 * Get the attributes of the learning object publication
	 * @param int $id The ID of the learning object.
	 * @return array An array of LearningObjectPublicationAttributes objects;
	 *               empty if the object has not been published anywhere.
	 */
	function get_learning_object_publication_attributes($user, $id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		$applications = $this->get_registered_applications();
		$info = array();
		foreach($applications as $index => $application_name)
		{
			$application = Application::factory($application_name,$user);
			$attributes = $application->get_learning_object_publication_attributes($id, $type, $offset, $count, $order_property, $order_direction);
			if(!is_null($attributes) && count($attributes) > 0)
			{
				$info = array_merge($info, $attributes);
			}
		}
		
		$admin = new AdminManager($user);
		$attributes = $admin->get_learning_object_publication_attributes($id, $type, $offset, $count, $order_property, $order_direction);
		if(!is_null($attributes) && count($attributes) > 0)
		{
			$info = array_merge($info, $attributes);
		}
		
		return $info;
	}

	/**
	 * Get the attribute of the learning object publication
	 * @param int $id The ID of the learning object.
	 * @return array An array of LearningObjectPublicationAttributes objects;
	 *               empty if the object has not been published anywhere.
	 */
	function get_learning_object_publication_attribute($id, $application, $user)
	{
		$application = Application::factory($application);
		return $application->get_learning_object_publication_attribute($id);
	}

	/**
	 * Determines whether a learning object can be deleted.
	 * A learning object can sefely be deleted if
	 * - it isn't published in an application
	 * - all of its children can be deleted
	 * @param LearningObject $object
	 * @return boolean True if the given learning object can be deleted
	 */
	function learning_object_deletion_allowed($object, $type = null, $user)
	{	
		$homeportal = PlatformSetting :: get('portal_home');
		if($object->get_id() == $homeportal)
			return false;	
		
		$conditions[] = new EqualityCondition('variable', 'use_object');
		$conditions[] = new EqualityCondition('value', $object->get_id());
		$condition = new AndCondition($conditions);
	
		$blockinfos = HomeDataManager :: get_instance()->retrieve_home_block_config($condition);
		if($blockinfos->size() > 0)
			return false;
		
		if (isset($type))
		{
			if ($this->is_attached($object, 'version'))
			{
				return false;
			}
			$forbidden = array();
			$forbidden[] = $object->get_id();
		}
		else
		{ 
			if ($this->is_attached($object))
			{ 
				return false;
			}
			$children = array();
			//$children = $this->get_children_ids($object);
			$versions = array();
			$versions = $this->get_version_ids($object);
			$forbidden = array_merge($children, $versions);
		}
//		if($object->is_complex_learning_object())
//		{
//			//return $this->complex_learning_object_removal_allowed($object, $object);	
//		}
		
		return !$this->any_learning_object_is_published($forbidden);
	}
	
	function complex_learning_object_removal_allowed($clo, $root_clo)
	{
		if($this->learning_object_is_published($clo))
			return false;
		
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $clo->get_id());
		$items = $this->retrieve_complex_learning_object_items($condition);
		if($items->size() > 1)
			return false;
		if($items->size() == 1)
			if($clo->get_id() == $root_clo->get_id())
				return false;
			else
			{
				$parent = $this->retrieve_learning_object($items->next_result()->get_parent());
				return $this->complex_learning_object_removal_allowed($parent, $root_clo);
			}
		if($items->size() == 0)
			if($clo->get_id() == $root_clo->get_id())
				return true;
			else
				return false;
	}
	
	/**
	 * Copies a complex learning object
	 */
	function copy_complex_learning_object($clo)
	{
		$clo->create_all();
		$this->copy_complex_children($clo);
		return $clo;
	}
	
	function copy_complex_children($clo)
	{
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $clo->get_id());
		$items = $this->retrieve_complex_learning_object_items($condition);
		while($item = $items->next_result())
		{
			$nitem = new ComplexLearningObjectItem();			
			$nitem->set_user_id($item->get_user_id());
			$nitem->set_display_order($item->get_display_order());
			$nitem->set_parent($clo->get_id());
			$nitem->set_ref($item->get_ref());
			$nitem->create();
			$lo = $this->retrieve_learning_object($item->get_ref());
			if($lo->is_complex_learning_object())
			{
				$lo->create_all();
				$nitem->set_ref($lo->get_id());
				$nitem->update();
				$this->copy_complex_learning_object($lo);
			}
		}
	}

	/**
	 * Determines whether a version is revertable.
	 * @param LearningObject $object
	 * @return boolean True if the given learning object version can be reverted
	 */
	function learning_object_revert_allowed($object)
	{
		return !$this->is_latest_version($object);
	}

	/**
	 * Determines whether a learning object can be edited.
	 * @param LearningObject $object
	 * @return boolean True if the given learning object can be edited
	 */
	abstract function is_latest_version($object);

	/**
	 * Gets all ids of all children/grandchildren/... of a given learning
	 * object.
	 * @param LearningObject $object The learning object
	 * @return array The requested id's
	 */
	abstract function get_children_ids($object);

	/**
	 * Get number of times a physical document is used by a learning object's versions.
	 * @param String $path The document path
	 * @return boolean True if the physical document occurs only once, else False.
	 */
	abstract function is_only_document_occurence($path);

	/**
	 * Gets all ids of all versions of a given learning object.
	 * @param LearningObject $object The learning object
	 * @return array The requested id's
	 */
	abstract function get_version_ids($object);

	/**
	 * Initializes the data manager.
	 */
	abstract function initialize();

	/**
	 * Determines the type of the learning object with the given ID.
	 * @param int $id The ID of the learning object.
	 * @return string The learning object type.
	 */
	abstract function determine_learning_object_type($id);

	/**
	 * Retrieves the learning object with the given ID from persistent
	 * storage. If the type of learning object is known, it should be
	 * passed in order to save time.
	 * @param int $id The ID of the learning object.
	 * @param string $type The type of the learning object. May be omitted.
	 * @return LearningObject The learning object.
	 */
	abstract function retrieve_learning_object($id, $type = null);

	/**
	 * Retrieves the learning objects that match the given criteria from
	 * persistent storage.
	 * As far as ordering goes, there are two things to take into account:
	 * - If, after applying the passed conditions, there is no order between
	 *   two learning objects, the display order index should be taken into
	 *   account.
	 * - Regardless of what the order specification states, learning objects
	 *   of the "category" types must always come before others.
	 * Finally, there are some limitations to this method:
	 * - For now, you can only use the standard learning object properties,
	 *   not the type-specific ones IF you do not specify a single type of
	 *   learning object to retrieve.
	 * - Future versions may include statistical functions.
	 * @param string $type The type of learning objects to retrieve, if any.
	 *                     If you do not specify a type, or the type is not
	 *                     known in advance, you will only be able to select
	 *                     on default properties; also, there will be a
	 *                     significant performance decrease. In this case,
	 *                     the values of the additional properties will not
	 *                     yet be known; they will be retrieved JIT, i.e.
	 *                     right before they are accessed.
	 * @param Condition $condition The condition to use for learning object
	 *                             selection, structured as a Condition
	 *                             object. Please consult the appropriate
	 *                             documentation.
	 * @param array $orderBy An array of properties to sort the learning
	 *                       objects on.
	 * @param array $orderDir An array that indicates the sorting direction
	 *                        for the property at the corresponding position
	 *                        in $orderBy. The PHP constant SORT_DESC sorts
	 *                        the objects in descending order; SORT_ASC is
	 *                        the default and uses ascending order.
	 * @param int $offset The index of the first object to return. If
	 *                    omitted or negative, the result set will start
	 *                    from the first object.
	 * @param int $maxObjects The maximum number of objects to return. If
	 *                        omitted or non-positive, every object from the
	 *                        first index will be returned.
	 * @param int $state The state the learning objects should have. Any of
	 *                   the LearningObject :: STATE_* constants. A negative
	 *                   number means the state should be ignored. Defaults
	 *                   to LearningObject :: STATE_NORMAL. You can just as
	 *                   easily use your own condition for this; this
	 *                   parameter is merely for convenience, and to ensure
	 *                   that the function does not apply to recycled objects
	 *                   by default.
	 * @param boolean $different_parent_state True to enforce that the parent
	 *                                        learning object's state be
	 *                                        different from $state. This is
	 *                                        useful when retrieving removed
	 *                                        tree structures.
	 * @return ResultSet A set of matching learning objects.
	 */
	abstract function retrieve_learning_objects($type = null, $condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1, $state = LearningObject :: STATE_NORMAL, $different_parent_state = false);

	/**
	 * Retrieves the additional properties of the given learning object.
	 * @param LearningObject $learning_object The learning object for which to
	 *                                        fetch additional properties.
	 * @return array The properties as an associative array.
	 */
	abstract function retrieve_additional_learning_object_properties($learning_object);

	/**
	 * Returns the number of learning objects that match the given criteria.
	 * This method has the same limitations as retrieve_learning_objects.
	 * @param string $type The type of learning objects to search for, if any.
	 *                     If you do not specify a type, or the type is not
	 *                     known in advance, you will only be able to select
	 *                     on default properties; also, there will be a
	 *                     significant performance decrease.
	 * @param Condition $condition The condition to use for learning object
	 *                             selection, structured as a Condition
	 *                             object. Please consult the appropriate
	 *                             documentation.
	 * @param int $state The state the learning objects should have. Any of
	 *                   the LearningObject :: STATE_* constants. A negative
	 *                   number means the state should be ignored. Defaults
	 *                   to LearningObject :: STATE_NORMAL. You can just as
	 *                   easily use your own condition for this; this
	 *                   parameter is merely for convenience, and to ensure
	 *                   that the function does not apply to recycled objects
	 *                   by default.
	 * @param boolean $different_parent_state True to enforce that the parent
	 *                                        learning object's state be
	 *                                        different from $state. This is
	 *                                        useful when retrieving removed
	 *                                        tree structures.
	 * @return int The number of matching learning objects.
	 */
	abstract function count_learning_objects($type = null, $condition = null, $state = LearningObject :: STATE_NORMAL, $different_parent_state = false);

	/**
	 * Returns the number of learning objects that match the given criteria.
	 * This method has the same limitations as retrieve_learning_objects.
	 * @param string $type The type of learning objects to search for, if any.
	 *                     If you do not specify a type, or the type is not
	 *                     known in advance, you will only be able to select
	 *                     on default properties; also, there will be a
	 *                     significant performance decrease.
	 * @param Condition $condition The condition to use for learning object
	 *                             selection, structured as a Condition
	 *                             object. Please consult the appropriate
	 *                             documentation.
	 * @param int $state The state the learning objects should have. Any of
	 *                   the LearningObject :: STATE_* constants. A negative
	 *                   number means the state should be ignored. Defaults
	 *                   to LearningObject :: STATE_NORMAL. You can just as
	 *                   easily use your own condition for this; this
	 *                   parameter is merely for convenience, and to ensure
	 *                   that the function does not apply to recycled objects
	 *                   by default.
	 * @param boolean $different_parent_state True to enforce that the parent
	 *                                        learning object's state be
	 *                                        different from $state. This is
	 *                                        useful when retrieving removed
	 *                                        tree structures.
	 * @return int The number of matching learning objects.
	 */
	function count_publication_attributes($user, $type = null, $condition = null)
	{
		$applications = $this->get_registered_applications();
		$info = 0;
		foreach($applications as $index => $application_name)
		{
			$application = Application::factory($application_name,$user);
			$info += $application->count_publication_attributes($type, $condition);
		}
		
		$admin = new AdminManager($user);
		$info += $admin->count_publication_attributes($type, $condition);
		
		return $info;
	}

	/**
	 * Returns the next available learning object publication ID.
	 * @return int The ID.
	 */
	abstract function get_next_learning_object_id();

	/**
	 * Returns the next available learning object number.
	 * @return int The ID.
	 */
	abstract function get_next_learning_object_number();

	/**
	 * Makes the given learning object persistent.
	 * @param LearningObject $object The learning object.
	 * @return boolean True if creation succceeded, false otherwise.
	 */
	abstract function create_learning_object($object, $type);

	/**
	 * Updates the given learning object in persistent storage.
	 * @param LearningObject $object The learning object.
	 * @return boolean True if the update succceeded, false otherwise.
	 */
	abstract function update_learning_object($object);

	/**
	 * Updates the given learning object publications learning object id.
	 * @param LearningObjectPublicationAttribute $object The learning object publication attribute.
	 * @return boolean True if the update succceeded, false otherwise.
	 */
	function update_learning_object_publication_id($publication_attr)
	{
		$application = Application::factory($publication_attr->get_application());
		return $application->update_learning_object_publication_id($publication_attr);
	}

	/**
	 * Deletes the given learning object from persistent storage.
	 * This function deletes
	 * - all children of the given learning object (using this function
	 *   recursively)
	 * - links from this object to other objects (so called attachments)
	 * - links from other objects to this object (so called attachments)
	 * - the object itself
	 * @param LearningObject $object The learning object.
	 * @return boolean True if the given object was succesfully deleted, false
	 *                 otherwise. Deletion fails when the object is used
	 *                 somewhere in an application or if one of its children
	 *                 is in use.
	 */
	abstract function delete_learning_object($object);

	/**
	 * Returns the next available complex learning object ID.
	 * @return int The ID.
	 */
	abstract function get_next_complex_learning_object_item_id();
	
	/**
	 * Creates a new complex learning object in the database
	 * @param ComplexLearningObject $clo - The complex learning object
	 * @return True if success
	 */
	abstract function create_complex_learning_object_item($clo_item);
	
	/**
	 * Updates a complex learning object in the database
	 * @param ComplexLearningObject $clo - The complex learning object
	 * @return True if success
	 */
	abstract function update_complex_learning_object_item($clo_item);
	
	/**
	 * Deletes a complex learning object in the database
	 * @param ComplexLearningObject $clo - The complex learning object
	 * @return True if success
	 */
	abstract function delete_complex_learning_object_item($clo_item);
	
	/**
	 * Retrieves a complex learning object from the database with a given id
	 * @param Int $clo_id
	 * @return The complex learning object 
	 */
	abstract function retrieve_complex_learning_object_item($clo_item_id);
	
	/**
	 * Counts the available complex learning objects with the given condition
	 * @param Condition $condition
	 * @return Int the amount of complex learning objects
	 */
	abstract function count_complex_learning_object_items($condition);
	
	/**
	 * Retrieves the complex learning object items with the given condition
	 * @param Condition 
	 */
	abstract function retrieve_complex_learning_object_items($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1);

	/**
	 * Deletes the given learning object version from persistent storage.
	 * This function deletes
	 * - the selected version
	 * This function updates
	 * - the latest version entry if necessary
	 * @param LearningObject $object The learning object.
	 * @return boolean True if the given version was succesfully deleted, false
	 *                 otherwise. Deletion fails when the version is used
	 *                 somewhere in an application or if one of its children
	 *                 is in use.
	 */
	abstract function delete_learning_object_version($object);

	/**
	 * Gets all learning objects from this user id, and removes them
	 */
	abstract function retrieve_learning_object_by_user($user_id);

	/**
	 * Deletes all learning objects a user_id has:
	 * Retrieves the learning object(s) a user has made,
	 * deletes the publications made with these object(s),
	 * and finally, deletes the object itself.
	 */
	function delete_learning_object_by_user($user_id)
	{
		$learning_object = $this->retrieve_learning_object_by_user($user_id);
		while ($object = $learning_object->next_result())
		{
			if (!$this->delete_learning_object_publications($object))
			{
				return false;
			}
			if (!$object->delete())
			{
				return false;
			}
		}
		return true;
	}

	function delete_learning_object_publications($object)
	{
		$applications = $this->get_registered_applications();
		foreach($applications as $index => $application_name)
		{
			$application = Application::factory($application_name);
			$application->delete_learning_object_publications($object->get_id());
		}
		
		$admin = AdminDataManager :: get_instance()->delete_learning_object_publications($object->get_id());
		
		return true;
	}

	abstract function delete_learning_object_attachments($object);

	/**
	 * Deletes all known learning objects from persistent storage.
	 * @note Only for testing purpuses. This function also deletes the root
	 *       category of a user's repository.
	 */
	abstract function delete_all_learning_objects();

	/**
	 * Moves a learning object among its siblings.
	 * @param LearningObject $object The learning object to move.
	 * @param int $places The number of places to move the object down by. If
	 *                    negative, the publication will be moved up.
	 * @return int The number of places that the publication was moved down.
	 */
	abstract function move_learning_object($object, $places);

	/**
	 * Gets the next available index in the display order.
	 * @param int $parent The numeric identifier of the learning object's
	 *                    parent learning object.
	 * @param string $type The type of learning object.
	 * @return int The requested display order index.
	 */
	abstract function get_next_learning_object_display_order_index($parent, $type);

	/**
	 * Sets the given learning object's display order index to the next
	 * available index in the display order. This is a convenience function.
	 * @param LearningObject $object The learning object.
	 * @return int The newly assigned index.
	 */
	function assign_learning_object_display_order_index ($object)
	{
		$index = $this->get_next_learning_object_display_order_index($object->get_parent_id(), $object->get_type());
		$object->set_display_order_index($index);
		return $index;
	}

	/**
	 * Returns the learning objects that are attached to the learning object
	 * with the given ID.
	 * @param LearningObject $object The learning object for which to retrieve
	 *                               attachments.
	 * @return array The attached learning objects.
	 */
	abstract function retrieve_attached_learning_objects ($object);

	abstract function retrieve_learning_object_versions ($object);

	abstract function get_latest_version_id ($object);

	/**
	 * Adds a learning object to another's attachment list.
	 * @param LearningObject $object The learning object to attach the other
	 *                               learning object to.
	 * @param int $attachment_id The ID of the object to attach.
	 */
	abstract function attach_learning_object ($object, $attachment_id);

	/**
	 * Removes a learning object from another's attachment list.
	 * @param LearningObject $object The learning object to detach the other
	 *                               learning object from.
	 * @param int $attachment_id The ID of the object to detach.
	 * @return boolean True if the attachment was removed, false if it did not
	 *                 exist.
	 */
	abstract function detach_learning_object ($object, $attachment_id);

	/**
	 * Sets the requested learning objects' state to one of the STATE_*
	 * constants defined in the LearningObject class. This function's main use
	 * is to make a learning object's children inherit its state.
	 * @param array $object_ids The learning object IDs.
	 * @param int $state The new state.
	 * @return boolean True upon success, false upon failure.
	 */
	abstract function set_learning_object_states ($object_ids, $state);

	/**
	 * Automagically loads all the available types of learning objects
	 * and registers them with this data manager.
	 * @todo This function now parses the XML-files of every learning object
	 * type. There's probably a faster way to retrieve this information by
	 * saving the types and their properties in the database when the learning
	 * object type is installed on the system.
	 */
	private function load_types()
	{
		$path = Path :: get_repository_path() . 'lib/learning_object/';
		
		foreach($this->get_registered_types(true) as $learning_object_type)
		{
			$learning_object_path = $path . $learning_object_type . '/' . $learning_object_type . '.class.php';
			require_once $learning_object_path;
		}
	}

	/**
	 * Checks if an identifier is a valid name for a learning object type.
	 * @param string $name The name.
	 * @return boolean True if a valid learning object type name was passed,
	 *                 false otherwise.
	 */
	static function is_learning_object_type_name($name)
	{
		return (preg_match('/^[a-z][a-z_]+$/', $name) > 0);
	}

	/**
	 * Returns the names of the applications known to this
	 * repository datamanager.
	 * @return array The applications.
	 */
	function get_registered_applications()
	{
		if (!isset($this->applications) || count($this->applications) == 0)
		{
			$this->applications = Application::load_all();
		}
		
		return $this->applications;
	}

	/**
	 * Gets the disk space consumed by the given user.
	 * @param int $user The user ID.
	 * @return int The number of bytes used.
	 */
	abstract function get_used_disk_space($user);

	/**
	 * Creates a storage unit
	 * @param string $name Name of the storage unit
	 * @param array $properties Properties of the storage unit
	 * @param array $indexes The indexes which should be defined in the created
	 * storage unit
	 */
	abstract function create_storage_unit($name,$properties,$indexes);

	abstract function get_next_category_id();
	abstract function select_next_category_display_order($parent_category_id, $user_id);
	abstract function delete_category($category);
	abstract function update_category($category);
	abstract function create_category($category);
	abstract function count_categories($conditions = null);
	abstract function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null);
	

	/**
	 * Gets the number of categories the user has defined in his repository
	 * @param int $user_id
	 * @return int
	 */
	function get_number_of_categories($user_id)
	{
		if(!isset($this->number_of_categories{$user_id}))
		{
			$condition = new EqualityCondition(RepositoryCategory :: PROPERTY_USER_ID, $user_id);
			//$this->number_of_categories{$user_id} = $this->count_learning_objects('category', $condition);
			$this->number_of_categories[$user_id] = $this->count_categories($condition);
		}
		return $this->number_of_categories{$user_id};

	}
}
?>