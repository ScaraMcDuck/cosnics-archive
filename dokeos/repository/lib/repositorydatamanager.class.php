<?php
/**
 * $Id$
 * @package repository
 */
require_once dirname(__FILE__).'/configuration.class.php';
require_once dirname(__FILE__).'/learningobjectpublicationattributes.class.php';
require_once dirname(__FILE__).'/data_manager/database/databaselearningobjectresultset.class.php';

/**
 *	This is a skeleton for a data manager for the learning object repository.
 *	Data managers must extend this class and implement its abstract methods.
 *	If the user configuration dictates that the "database" data manager is to
 *	be used, this class will automatically attempt to instantiate
 *	"DatabaseRepositoryDataManager"; hence, this naming convention must be
 *	respected for all extensions of this class.
 *
 *	@author Tim De Pauw
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
		$this->load_applications();
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
	 * Returns the properties that are specific to the passed type of
	 * learning object.
	 * @param string $type The learning object type.
	 * @return array The properties.
	 */
	function get_additional_properties($type)
	{
		return $this->typeProperties[$type];
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
		$types = array_keys($this->typeProperties);
		if(!$only_master_types)
		{
			return $types;
		}
		$master_types = array();
		foreach($types as $index => $type)
		{
			$class_type = LearningObject::type_to_class($type);
			if(call_user_func(array($class_type,'is_master_type')))
			{
				$master_types[] = $type;
			}
		}
		return $master_types;
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
		return (count($this->typeProperties[$type]) > 0);
	}

	/**
	 * Returns the root category of a user's repository.
	 * @param int $owner The user ID of the owner.
	 * @return Category The root category of this user's repository.
	 */
	function retrieve_root_category($owner)
	{
		$condition1 = new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, $owner);
		$condition2 = new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID, 0);
		$condition = new AndCondition($condition1, $condition2);
		$objects = $this->retrieve_learning_objects('category', $condition, null, null, 0, 1, -1);
		return $objects->next_result();
	}
	
	function create_root_category($user_id)
	{
		$object = new Category();
		$object->set_owner_id($user_id);
		$object->set_title(get_lang('MyRepository'));
		$object->set_description('...');
		$object->create();
	}

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
			$application_class = self::application_to_class($application_name);
			$application = new $application_class;
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
			$application_class = self::application_to_class($application_name);
			$application = new $application_class;
			if ($application->any_learning_object_is_published($ids))
			{
				return true;
			}
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
			$application_class = self::application_to_class($application_name);
			$application = new $application_class($user);
			$info = array_merge($info, $application->get_learning_object_publication_attributes($id, $type, $offset, $count, $order_property, $order_direction));
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
		$applications = $this->get_registered_applications();
		$application_class = self::application_to_class($application);
		$application = new $application_class;
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
			$children = $this->get_children_ids($object);
			$versions = array();
			$versions = $this->get_version_ids($object);
			$forbidden = array_merge($children, $versions);
		}
		return !$this->any_learning_object_is_published($forbidden, $user);
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
	function count_publication_attributes($user, $type = null, $condition = null, $user)
	{
		$applications = $this->get_registered_applications();
		$info = 0;
		foreach($applications as $index => $application_name)
		{
			$application_class = self::application_to_class($application_name);
			$application = new $application_class($user);
			$info += $application->count_publication_attributes($type, $condition);
		}
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
		$applications = $this->get_registered_applications();
		$application_class = self::application_to_class($publication_attr->get_application());
		$application = new $application_class;
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
			$this->delete_learning_object_publications($object);
			$object->delete();
		}
		return true;
	}

	function delete_learning_object_publications($object)
	{
		$applications = $this->get_registered_applications();
		foreach($applications as $index => $application_name)
		{
			$application_class = self::application_to_class($application_name);
			$application = new $application_class;
			$application->delete_learning_object_publications($object->get_id());
		}
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
		$path = dirname(__FILE__).'/learning_object';
		if ($handle = opendir($path))
		{
			while (false !== ($file = readdir($handle)))
			{
				$p = $path.'/'.$file;
				if (is_dir($p) && self :: is_learning_object_type_name($file))
				{
					require_once $p.'/'.$file.'.class.php';
					$f = $p.'/'.$file.'.xml';
					// XXX: Always require a file, even if empty?
					if (is_file($f))
					{
						$properties = array ();
						$doc = new DOMDocument();
						$doc->load($f);
						$xml_properties = $doc->getElementsByTagname('property');
						foreach($xml_properties as $index => $property)
						{
							$properties[] = trim($property->getAttribute('name'));
						}
						$this->register_type($file, $properties);
					}
					else
					{
						$this->register_type($file);
					}
				}
			}
			closedir($handle);
		}
		else
		{
			die('Failed to load learning object types');
		}
	}

	/**
	 * Registers a learning object type with this data manager.
	 * @param string $type The name of the type.
	 * @param array $additionalProperties The additional properties
	 *                                    associated with the type. May be
	 *                                    omitted if none.
	 */
	private function register_type($type, $additionalProperties = array ())
	{
		if (array_key_exists($type, $this->typeProperties))
		{
			die('Type already registered: '.$type);
		}
		$this->typeProperties[$type] = $additionalProperties;
	}
	/**
	 * Checks if a learning object type is allready registered with this
	 * datamanager
	 * @return boolean
	 */
	private function is_registered_type($type)
	{
		return array_key_exists($type, $this->typeProperties);
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
		return $this->applications;
	}

	/**
	 * Registers an application with this repository datamanager.
	 * @param string $application The application name.
	 */
	function register_application($application)
	{
		if (in_array($application, $this->applications))
		{
			die('Application already registered: '.$application);
		}
		$this->applications[] = $application;
	}

	/**
	 * Loads the applications installed on the system. Applications are classes
	 * in the /application/lib subdirectory. Each application is a directory,
	 * which in its turn contains a class file named after the application. For
	 * instance, the weblcms application is the class Weblcms, defined in
	 * /application/lib/weblcms/weblcms.class.php. Applications must extend the
	 * Application class.
	 */
	private function load_applications()
	{
		$path = dirname(__FILE__).'/../../application/lib';
		if ($handle = opendir($path))
		{
			while (false !== ($file = readdir($handle)))
			{
				$toolPath = $path.'/'. $file .'/'.$file.'_manager';
				if (is_dir($toolPath) && self :: is_application_name($file))
				{
					require_once $toolPath.'/'.$file.'.class.php';
					$this->register_application($file);
				}
			}
			closedir($handle);
		}
		else
		{
			die('Failed to load applications');
		}

	}

	/**
	 * Converts an application name to the corresponding class name.
	 * @param string $application The application name.
	 * @return string The class name.
	 */
	static function application_to_class($application)
	{
		return ucfirst(preg_replace('/_([a-z])/e', 'strtoupper(\1)', $application));
	}

	/**
	 * Converts an application class name to the corresponding application name.
	 * @param string $class The class name.
	 * @return string The application name.
	 */
	static function class_to_application($class)
	{
		return preg_replace(array ('/^([A-Z])/e', '/([A-Z])/e'), array ('strtolower(\1)', '"_".strtolower(\1)'), $class);
	}

	/**
	 * Determines whether or not the given name is a valid application name.
	 * @param string $name The name to evaluate.
	 * @return True if the name is a valid application name, false otherwise.
	 */
	static function is_application_name($name)
	{
		return (preg_match('/^[a-z][a-z_]+$/', $name) > 0);
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

	/**
	 * Gets the number of categories the user has defined in his repository
	 * @param int $user_id
	 * @return int
	 */
	function get_number_of_categories($user_id)
	{
		if(!isset($this->number_of_categories{$user_id}))
		{
			$condition = new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, $user_id);
			$this->number_of_categories{$user_id} = $this->count_learning_objects('category', $condition);
		}
		return $this->number_of_categories{$user_id};

	}
}
?>