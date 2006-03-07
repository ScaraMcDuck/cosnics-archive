<?php

/**
==============================================================================
 *	This class represents a learning object in the repository. Every object
 *	that can be associated with a module is in fact a learning object.
 *
 *	Learning objects have a number of default properties:
 *	- id: the numeric ID of the learning object;
 *	- parent: the numeric ID of the parent of this learning object, which
 *	  is also a learning object (not applicable for any learning object);
 *	- owner: the ID of the user who owns the learning object;
 *	- title: the title of the learning object;
 *	- description: a brief description of the learning object; may also be
 *	  used to store its content in select cases;
 *	- created: the date when the learning object was created, as returned by
 *	  PHP's time() function (UNIX time, seconds since the epoch);
 *	- modified: the date when the learning object was last modified, as
 *	  returned by PHP's time() function.
 *
 *	Actual learning objects must be instances of extensions of this class.
 *	They may define additional properties which are specific to that
 *	particular type of learning object, e.g. the path to a document. This
 *	class provides a framework for that purpose.
 *
 *	To create your own type of learning object, you should follow these steps:
 *	- Decide on a name for the type, e.g. "MyType".
 *	- Create a new subdirectory in /repository/lib/learning_object. For
 *	  "MyType", it would be called "my_type".
 *	- Create two files in that subdirectory:
 *	  - The properties file (e.g. "my_type.properties") is a plain text list
 *	    of the names of all the properties of your type, one name per line.
 *	    This file may be omitted if your type does not require additional
 *	    properties.
 *	  - The class file (e.g. "my_type.class.php") is a PHP class that may
 *	    provide specific methods for the type. Even if the type does not
 *	    require additional methods, you must still define the class. Take
 *	    a look at the types that are already defined for examples.
 *	- The data manager will now automagically be aware of the type. All that's
 *	  left for you to do is create the physical storage for the type. This
 *	  will heavily depend on the type of data manager you are using. As MySQL
 *	  is the default, you will probably have to create a table named after the
 *	  type you are defining. This table should contain a numeric "id" column,
 *	  as well as columns for all the properties in the properties file. You do
 *	  not need columns for the default properties! These are stored elsewhere.
 *	When you've completed these steps, you should be able to instantiate the
 *	class and manipulate the objects at will.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class LearningObject
{
	/**
	 * Names of default properties of any learning object.
	 */
	static $DEFAULT_PROPERTIES = array ('owner', 'title', 'description', 'created', 'modified');

	/**
	 * Numeric identifier of the learning object.
	 */
	private $id;

	/**
	 * Default properties of the learning object, stored in an associative
	 * array.
	 */
	private $defaultProperties;

	/**
	 * Additional properties specific to this type of learning object, stored
	 * in an associative array.
	 */
	private $additionalProperties;

	/**
	 * Creates a new learning object.
	 * @param int $id The numeric ID of the learning object. May be omitted
	 *                if creating a new object.
	 * @param array $defaultProperties The default properties of the learning
	 *                                 object. Associative array.
	 * @param array $additionalProperties The properties specific for this
	 *                                    type of learning object.
	 *                                    Associative array.
	 */
	function LearningObject($id = 0, $defaultProperties = array (), $additionalProperties = array ())
	{
		$this->id = $id;
		$this->defaultProperties = $defaultProperties;
		$this->additionalProperties = $additionalProperties;
	}

	/**
	 * Returns the ID of this learning object.
	 * @return int The ID.
	 */
	function get_id()
	{
		return $this->id;
	}

	/**
	 * Returns a string representation of the type of this learning object.
	 * @return string The type.
	 */
	function get_type()
	{
		return preg_replace(array ('/^([A-Z])/e', '/([A-Z])/e'), array ('strtolower(\1)', '"_".strtolower(\1)'), get_class($this));
	}

	/**
	 * Returns the ID of this learning object's owner.
	 * @return int The ID.
	 */
	function get_owner_id()
	{
		return $this->get_default_property('owner');
	}

	/**
	 * Returns the title of this learning object.
	 * @return string The title.
	 */
	function get_title()
	{
		return $this->get_default_property('title');
	}

	/**
	 * Returns the description of this learning object.
	 * @return string The description.
	 */
	function get_description()
	{
		return $this->get_default_property('description');
	}

	/**
	 * Returns the date when this learning object was created, as returned
	 * by PHP's time() function.
	 * @return int The creation date.
	 */
	function get_creation_date()
	{
		return $this->get_default_property('created');
	}

	/**
	 * Returns the date when this learning object was last modified, as
	 * returned by PHP's time() function.
	 * @return int The modification time.
	 */
	function get_modification_date()
	{
		return $this->get_default_property('modified');
	}

	/**
	 * Sets the ID of this learning object.
	 * @param int $id The ID.
	 */
	function set_id($id)
	{
		$this->id = $id;
	}

	/**
	 * Sets the ID of this learning object's owner.
	 * @param int $id The ID.
	 */
	function set_owner_id($owner)
	{
		$this->set_default_property('owner', $owner);
	}

	/**
	 * Sets the title of this learning object.
	 * @param string $title The title.
	 */
	function set_title($title)
	{
		$this->set_default_property('title', $title);
	}

	/**
	 * Sets the description of this learning object.
	 * @param string $description The description.
	 */
	function set_description($description)
	{
		$this->set_default_property('description', $description);
	}

	/**
	 * Sets the date when this learning object was created.
	 * @param int $created The creation date, as returned by time().
	 */
	function set_creation_date($created)
	{
		$this->set_default_property('created', $created);
	}

	/**
	 * Sets the date when this learning object was modified.
	 * @param int $modified The modification date, as returned by time().
	 */
	function set_modification_date($modified)
	{
		$this->set_default_property('modified', $modified);
	}

	/**
	 * Returns whether or not this learning object is extended, i.e. whether
	 * its type defines additional properties.
	 * @return boolean True if the learning object is extended, false
	 *                 otherwise.
	 */
	function is_extended()
	{
		return DataManager :: get_instance()->is_extended_type($this->get_type());
	}

	/**
	 * Gets a default property of this learning object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	/**
	 * Sets a default property of this learning object by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	/**
	 * Gets an additional (type-specific) property of this learning object by
	 * name.
	 * @param string $name The name of the property.
	 */
	function get_additional_property($name)
	{
		return $this->additionalProperties[$name];
	}

	/**
	 * Sets an additional (type-specific) property of this learning object by
	 * name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_additional_property($name, $value)
	{
		$this->additionalProperties[$name] = $value;
	}

	/**
	 * Gets the default properties of this learning object.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	/**
	 * Gets the additional (type-specific) properties of this learning
	 * object.
	 * @return array An associative array containing the properties.
	 */
	function get_additional_properties()
	{
		return $this->additionalProperties;
	}

	/**
	 * Instructs the data manager to create the learning object, making it
	 * persistent. Also sets the learning object's creation date to the
	 * current time. The data manager automatically assigns an ID to the
	 * learning object.
	 * @return int The newly assigned ID of the learning object.
	 */
	function create()
	{
		$now = time();
		$this->set_creation_date($now);
		$this->set_modification_date($now);
		return DataManager :: get_instance()->create_learning_object($this);
	}

	/**
	 * Instructs the data manager to update the learning object, making any
	 * modifications permanent. Also sets the learning object's modification
	 * date to the current time.
	 */
	function update()
	{
		$this->set_modification_date(time());
		return DataManager :: get_instance()->update_learning_object($this);
	}

	/**
	 * Instructs the data manager to delete the learning object.
	 */
	function delete()
	{
		return DataManager :: get_instance()->delete_learning_object($this);
	}

	// XXX: Keep this around? Override? Make useful?
	function __tostring()
	{
		return get_class($this).'#'.$this->get_id().' ('.$this->get_title().')';
	}
}
?>