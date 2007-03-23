<?php
//require_once dirname(__FILE__).'/accessiblelearningobject.class.php';

/**
 *	This class represents a course in the weblcms.
 *
 *	courses have a number of default properties:
 *	- id: the numeric ID of the learning object;
 *	- owner: the ID of the user who owns the learning object;
 *	- title: the title of the learning object;
 *	- description: a brief description of the learning object; may also be
 *	  used to store its content in select cases;
 *	- parent: the numeric ID of the parent object of this learning object;
 *    this is a learning object by itself, usually a category;
 *  - display_order: a number giving the learning object a position among its
 *    siblings; only applies if the learning object is ordered;
 *	- created: the date when the learning object was created, as returned by
 *	  PHP's time() function (UNIX time, seconds since the epoch);
 *	- modified: the date when the learning object was last modified, as
 *	  returned by PHP's time() function;
 *  - state: the state the learning object is in; currently only used to mark
 *    learning objects as "recycled", i.e. moved to the Recycle Bin.
 *
 *	Actual learning objects must be instances of extensions of this class.
 *	They may define additional properties which are specific to that
 *	particular type of learning object, e.g. the path to a document. This
 *	class provides a framework for that purpose.
 *
 * To access the values of the properties, this class and its subclasses
 * should provide accessor methods. The names of the properties should be
 * defined as class constants, for standardization purposes. It is recommended
 * that the names of these constants start with the string "PROPERTY_".
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
 *	@author Hans De Bisschop
 *	@author Dieter De Neef
 */

class Course {
	
	const PROPERTY_ID = 'code';
	const PROPERTY_VISUAL = 'visual_code';
	const PROPERTY_DB = 'db_name';
	const PROPERTY_NAME = 'title';
	const PROPERTY_PATH = 'directory';
	const PROPERTY_TITULAR = 'tutor_name';
	const PROPERTY_LANGUAGE = 'course_language';
	const PROPERTY_EXTLINK_URL = 'department_url';
	const PROPERTY_EXTLINK_NAME = 'department_name';
	const PROPERTY_CATEGORY = 'category';
	const PROPERTY_CATEGORY_CODE = 'category_code';
	const PROPERTY_VISIBILITY = 'visibility';
	const PROPERTY_SUBSCRIBE_ALLOWED = 'subscribe';
	const PROPERTY_UNSUBSCRIBE_ALLOWED = 'unsubscribe';
	
	
	private $id;
	private $defaultProperties;

    function Course($id = null, $defaultProperties = array ())
    {
    	$this->id = $id;
		$this->defaultProperties = $defaultProperties;
    }
    
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Get the default properties of all courses.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_VISUAL, self :: PROPERTY_NAME, self :: PROPERTY_DB, self :: PROPERTY_PATH, self :: PROPERTY_TITULAR, self :: PROPERTY_LANGUAGE, self :: PROPERTY_EXTLINK_URL, self :: PROPERTY_EXTLINK_NAME, self :: PROPERTY_VISIBILITY, self :: PROPERTY_SUBSCRIBE_ALLOWED, self :: PROPERTY_UNSUBSCRIBE_ALLOWED);
	}
    
    function get_id()
    {
    	return $this->id;
    }

    function get_visual()
    {
    	return $this->get_default_property(self :: PROPERTY_VISUAL);
    }
    
    function get_name()
    {
    	return $this->get_default_property(self :: PROPERTY_NAME);
    }
    
    function get_db()
    {
    	return $this->get_default_property(self :: PROPERTY_DB);
    }
    
    function get_path()
    {
    	return $this->get_default_property(self :: PROPERTY_PATH);	
    }
    
    function get_titular()
    {
    	return $this->get_default_property(self :: PROPERTY_TITULAR);
    }
    
    function get_language()
    {
    	return $this->get_default_property(self :: PROPERTY_LANGUAGE);
    }
    
    function get_extlink_url()
    {
    	return $this->get_default_property(self :: PROPERTY_EXTLINK_URL);
    }
    
    function get_extlink_name()
    {
    	return $this->get_default_property(self :: PROPERTY_EXTLINK_NAME);
    }
    
    function get_category()
    {
    	return $this->get_default_property(self :: PROPERTY_CATEGORY);
    }
    
    function get_visibility()
    {
    	return $this->get_default_property(self :: PROPERTY_VISIBILITY);
    }
    
    function get_subscribe_allowed()
    {
    	return $this->get_default_property(self :: PROPERTY_SUBSCRIBE_ALLOWED);
    }
    
    function get_unsubscribe_allowed()
    {
    	return $this->get_default_property(self :: PROPERTY_UNSUBSCRIBE_ALLOWED);
    }
    
    function set_id($id)
	{
		$this->id = $id;
	}		
	
	function set_visual($visual)
	{
		$this->set_default_property(self :: PROPERTY_VISUAL, $visual);
	}
	
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}
	
	function set_db($db)
	{
		$this->set_default_property(self :: PROPERTY_DB, $db);
	}
	
	function set_path($path)
	{
		$this->set_default_property(self :: PROPERTY_PATH, $path);
	}
	
	function set_titular($titular)
	{
		$this->set_default_property(self :: PROPERTY_TITULAR, $titular);
	}
	
	function set_language($language)
	{
		$this->set_default_property(self :: PROPERTY_LANGUAGE, $language);
	}
	
	function set_extlink_url($url)
	{
		$this->set_default_property(self :: PROPERTY_EXTLINK_URL, $url);
	}
	
	function set_extlink_name($name)
	{
		$this->set_default_property(self :: PROPERTY_EXTLINK_URL, $name);
	}
	
	function set_category($category)
	{
		$this->set_default_property(self :: PROPERTY_CATEGORY, $category);
	}
	
	function set_visibility($visibility)
	{
		$this->set_default_property(self :: PROPERTY_VISIBILIT, $visibility);
	}
	
	function set_subscribe_allowed($subscribe)
	{
		$this->set_default_property(self :: PROPERTY_SUBSCRIBE_ALLOWED, $subscribe);
	}
	
	function set_unsubscribe_allowed($subscribe)
	{
		$this->set_default_property(self :: PROPERTY_UNSUBSCRIBE_ALLOWED, $subscribe);
	}
	
	function delete()
	{
		return RepositoryDataManager :: get_instance()->delete_learning_object($this);
	}
	
	function create()
	{
		$now = time();
		$this->set_creation_date($now);
		$this->set_modification_date($now);
		$dm = RepositoryDataManager :: get_instance();
		$id = $dm->get_next_learning_object_id();
		$this->set_id($id);
		$object_number = $dm->get_next_learning_object_number();
		$this->set_object_number($object_number);
		return $dm->create_learning_object($this, 'new');
	}
	
	function update($trueUpdate = true)
	{
		if ($trueUpdate)
		{
			$this->set_modification_date(time());
		}
		$dm = RepositoryDataManager :: get_instance();
		$success = $dm->update_learning_object($this);
		if (!$success)
		{
			return false;
		}
		$state = $this->get_state();
		if ($state == $this->oldState)
		{
			return true;
		}
		$child_ids = self :: get_child_ids($this->get_id());
		$dm->set_learning_object_states($child_ids, $state);

		return true;
	}
}
?>