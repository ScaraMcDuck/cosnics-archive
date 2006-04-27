<?php
require_once dirname(__FILE__).'/configuration.class.php';
require_once dirname(__FILE__).'/learningobjectpublicationattributes.class.php';

/**
==============================================================================
 *	This is a skeleton for a data manager for the learning object repository.
 *	Data managers must extend this class and implement its abstract methods.
 *	If the user configuration dictates that the "database" data manager is to
 *	be used, this class will automatically attempt to instantiate
 *	"DatabaseRepositoryDataManager"; hence, this naming convention must be
 *	respected for all extensions of this class.
 *
 *	@author Tim De Pauw
 * @package repository
==============================================================================
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
	 * @return array The types.
	 */
	function get_registered_types()
	{
		return array_keys($this->typeProperties);
	}

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
		$object = $this->retrieve_learning_objects('category', $condition, null, null, 0, 1);
		return $object[0];
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
	 * Determines where the learning object with the given ID has been
	 * published in the registered applications.
	 * @param int $id The ID of the learning object.
	 * @return array An array of LearningObjectPublicationAttributes objects;
	 *               empty if the object has not been published anywhere.
	 */
	function get_learning_object_publication_attributes($id)
	{
		$applications = $this->get_registered_applications();
		$info = array();
		foreach($applications as $index => $application_name)
		{
			$application_class = self::application_to_class($application_name);
			$application = new $application_class;
			$info = array_merge($info, $application->get_learning_object_publication_attributes($id));
		}
		return $info;
	}

	/**
	 * Determines whether a learning object can be deleted.
	 * A learning object can sefely be deleted if
	 * - it isn't published in an application
	 * - all of its children can be deleted
	 * @param LearningObject $object
	 * @return boolean True if the given learning object can be deleted
	 */
	function learning_object_can_be_deleted($object)
	{
		if( $this->learning_object_is_published($object->get_id()))
		{
			return false;
		}
		$condition = new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID, $object->get_id());
		$children = $this->retrieve_learning_objects(null, $condition);
		$all_children_can_be_deleted = true;
		foreach ($children as $index => $child)
		{
			$child_can_be_deleted = $this->learning_object_can_be_deleted($child);
			$all_children_can_be_deleted = $all_children_can_be_deleted && $child_can_be_deleted;
		}
		return $all_children_can_be_deleted;
	}

	/**
	 * Invokes the constructor of the class that corresponds to the specified
	 * type of learning object.
	 * @param string $type The learning object type.
	 * @param int $id The ID of the learning object.
	 * @param array $defaultProperties An associative array containing the
	 *                                 default properties of the learning
	 *                                 object.
	 * @param array $additionalProperties An associative array containing the
	 *                                    additional (type-specific)
	 *                                    properties of the learning object.
	 * @return LearningObject The newly instantiated learning object.
	 */
	protected function factory($type, $id, $defaultProperties, $additionalProperties)
	{
		if (!$this->is_registered_type($type))
		{
			die('Learning object type \''.$type.'\' not registered');
		}
		$class = self :: type_to_class($type);
		return new $class ($id, $defaultProperties, $additionalProperties);
	}

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
	 * persistent storage. There are some limitations:
	 * - For now, you can only use the standard learning object properties,
	 *   not the type-specific ones IF you do not specify a single type of
	 *   learning object to retrieve.
	 * - Future versions may include statistical functions.
	 * @param string $type The type of learning objects to retrieve, if any.
	 *                     If you do not specify a type, or the type is not
	 *                     known in advance, you will only be able to select
	 *                     on default properties; also, there will be a
	 *                     significant performance decrease.
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
	 * @param int $firstIndex The index of the first object to return. If
	 *                        omitted or negative, the result set will start
	 *                        from the first object.
	 * @param int $maxObjects The maximum number of objects to return. If
	 *                        omitted or non-positive, every object from the
	 *                        first index will be returned.
	 * @return array An array of the matching learning objects.
	 */
	abstract function retrieve_learning_objects($type = null, $condition = null, $orderBy = array (), $orderDesc = array (), $firstIndex = 0, $maxObjects = -1);

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
	 * @return int The number of matching learning objects.
	 */
	abstract function count_learning_objects($type = null, $condition = null);

	/**
	 * Returns the next available learning object publication ID.
	 * @return int The ID.
	 */
	abstract function get_next_learning_object_id();

	/**
	 * Makes the given learning object persistent.
	 * @param LearningObject $object The learning object.
	 * @return boolean True if creation succceeded, false otherwise.
	 */
	abstract function create_learning_object($object);

	/**
	 * Updates the given learning object in persistent storage.
	 * @param LearningObject $object The learning object.
	 * @return boolean True if the update succceeded, false otherwise.
	 */
	abstract function update_learning_object($object);

	/**
	 * Deletes the given learning object from persistent storage.
	 * @param LearningObject $object The learning object.
	 * @return boolean True if the given object was succesfully deleted, false
	 *                 otherwise. Deletion fails when the object is used
	 *                 somewhere in an application or if one of its children
	 *                 is in use.
	 */
	abstract function delete_learning_object($object);

	/**
	 * Deletes all known learning objects from persistent storage.
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
	 * @param int $id The ID of the learning object for which to retrieve
	 *                attachments.
	 * @return array The attached learning objects.
	 */
	abstract function retrieve_attached_learning_objects ($id);

	/**
	 * Adds a learning object to another's attachment list.
	 * @param int $object_id The ID of the learning object to attach the other
	 *                       learning object to.
	 * @param int $attachment_id The ID of the object to attach.
	 */
	abstract function attach_learning_object ($object_id, $attachment_id);

	/**
	 * Removes a learning object from another's attachment list.
	 * @param int $object_id The ID of the learning object to detach the other
	 *                       learning object from.
	 * @param int $attachment_id The ID of the object to detach.
	 * @return boolean True if the attachment was removed, false if it did not
	 *                 exist.
	 */
	abstract function detach_learning_object ($object_id, $attachment_id);

	/**
	 * Automagically loads all the available types of learning objects
	 * and registers them with this data manager.
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
					$f = $p.'/'.$file.'.properties';
					// XXX: Always require a file, even if empty?
					if (is_file($f))
					{
						$properties = array ();
						foreach (file($f) as $p)
						{
							$properties[] = rtrim($p);
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
	 * Converts a learning object type name to the corresponding class name.
	 * @param string $type The type name.
	 * @return string The class name.
	 */
	static function type_to_class($type)
	{
		return ucfirst(preg_replace('/_([a-z])/e', 'strtoupper(\1)', $type));
	}

	/**
	 * Converts a class name to the corresponding learning object type name.
	 * @param string $class The class name.
	 * @return string The type name.
	 */
	static function class_to_type($class)
	{
		return preg_replace(array ('/^([A-Z])/e', '/([A-Z])/e'), array ('strtolower(\1)', '"_".strtolower(\1)'), $class);
	}


	/**
	 * Returns the names of the applications known to this
	 * repositorydatamanager.
	 * @return array The applications.
	 */
	function get_registered_applications()
	{
		return $this->applications;
	}

	/**
	 * Registers an application with this repositorydatamanager.
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
				$toolPath = $path.'/'.$file;
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
}
?>