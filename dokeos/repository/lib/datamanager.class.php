<?php
require_once dirname(__FILE__).'/configuration.class.php';

/**
==============================================================================
 *	This is a skeleton for a data manager. Data managers must extend this
 *	class and implement its abstract methods. If the user configuration
 *	dictates that the "database" data manager is to be used, this class will
 *	automatically attempt to load "DatabaseDataManager"; hence, this naming
 *	convention must be respected for all extensions of this class.
 *
 *	@author Tim De Pauw
==============================================================================
 */

abstract class DataManager
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
	 * Constructor.
	 */
	protected function DataManager()
	{
		$this->initialize();
		$this->typeProperties = array ();
		$this->load_types();
	}

	/**
	 * Uses a singleton pattern and a factory pattern to return the data
	 * manager. The configuration determines which data manager class is to
	 * be instantiated.
	 * @return DataManager The data manager.
	 */
	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.strtolower($type).'.class.php';
			$class = $type.'DataManager';
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
	 * @return LearningObjec The newly instantiated learning object.
	 */
	protected function factory($type, $id, $defaultProperties, $additionalProperties)
	{
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
	abstract function retrieve_learning_objects($type = null, $conditions = null, $orderBy = array (), $orderDesc = array (), $firstIndex = 0, $maxObjects = -1);
	
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
	abstract function count_learning_objects($type = null, $conditions = null);

	/**
	 * Makes the given learning object persistent, assigning an ID to it.
	 * @param LearningObject $object The learning object.
	 * @return int The newly assigned ID.
	 */
	abstract function create_learning_object($object);

	/**
	 * Updates the given learning object in persistent storage.
	 * @param LearningObject $object The learning object.
	 */
	abstract function update_learning_object($object);

	/**
	 * Deletes the given learning object from persistent storage.
	 * @param LearningObject $object The learning object.
	 */
	abstract function delete_learning_object($object);

	/**
	 * Checks if an identifier is a valid name for a learning object type.
	 * @param string $name The name.
	 * @return boolean True if a valid learning object type name was passed,
	 *                 false otherwise.
	 */
	function is_learning_object_type_name($name)
	{
		return (preg_match('/^[a-z][a-z_]+$/', $name) > 0);
	}
	
	/**
	 * Converts a learning object type name to the corresponding class name.
	 * @param string $type The type name.
	 * @return string The class name.
	 */
	function type_to_class($type)
	{
		return ucfirst(preg_replace('/_([a-z])/e', 'strtoupper(\1)', $type));
	}

	/**
	 * Converts a learning object class name to the corresponding class type.
	 * @param string $class The class name.
	 * @return string The type name.
	 */
	function class_to_type($class)
	{
		return preg_replace(array ('/^([A-Z])/e', '/([A-Z])/e'), array ('strtolower(\1)', '"_".strtolower(\1)'), $class);
	}

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
}
?>