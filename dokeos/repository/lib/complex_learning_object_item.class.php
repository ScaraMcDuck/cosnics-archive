<?php
/**
 * @package repository
 */
require_once dirname(__FILE__).'/repository_data_manager.class.php';
/**
 * Instances of this class group generic information about a complex learning object item
 *
 * @author Sven Vanpoucke
 */

class ComplexLearningObjectItem
{
	const PROPERTY_ID = 'id';
	const PROPERTY_REF = 'ref';
	const PROPERTY_PARENT = 'parent';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_DISPLAY_ORDER = 'display_order';
	const PROPERTY_ADD_DATE = 'add_date';

	private $defaultProperties;
	private $additionalProperties;

    function ComplexLearningObjectItem($defaultProperties = array (), $additionalProperties = array())
    {
		$this->defaultProperties = $defaultProperties;
		$this->additionalProperties = $additionalProperties;
    }
    
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	/**
	 * Gives a value to a given default property
	 * @param String $name the name of the default property
	 * @param Object $value the new value
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	function get_additional_property($name)
	{
		return $this->additionalProperties[$name];
	}
	
	/**
	 * Gives a value to a given additional property
	 * @param String $name the name of the additional property
	 * @param Object $value the new value
	 */
	function set_additional_property($name, $value)
	{
		$this->additionalProperties[$name] = $value;
	}
	
	/**
	 * Retrieves the default properties of this class
	 * @return Array of Objects
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Sets the default properties of this class
	 * @param Array Of Objects $defaultProperties
	 */
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}
	
		
	/**
	 * Retrieves the default properties of this class
	 * @return Array of Objects
	 */
	function get_additional_properties()
	{
		return $this->additionalProperties;
	}
	
	/**
	 * Sets the default properties of this class
	 * @param Array Of Objects $defaultProperties
	 */
	function set_additional_properties($additionalProperties)
	{
		$this->additionalProperties = $additionalProperties;
	}
	
	/**
	 * Get the default property names
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_REF, self :: PROPERTY_PARENT, 
		self :: PROPERTY_USER_ID, self :: PROPERTY_DISPLAY_ORDER, self :: PROPERTY_ADD_DATE);
	}
	
	/**
	 * Gets the additional property names
	 * Should be overridden when needed
	 * @return array the aditional property names
	 */
	static function get_additional_property_names()
	{
		return array();
	}
	
	/**
	 * Method to check whether a certain name is a default property name
	 * @param String $name
	 * @return 
	 */
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}
    
    function get_id()
    {
    	return $this->get_default_property(self :: PROPERTY_ID);
    }
    
    function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	
    function get_add_date()
    {
    	return $this->get_default_property(self :: PROPERTY_ADD_DATE);
    }
    
    function set_add_date($add_date)
	{
		$this->set_default_property(self :: PROPERTY_ADD_DATE, $add_date);
	}
	
    function get_ref()
    {
    	return $this->get_default_property(self :: PROPERTY_REF);
    }
    
	function set_ref($ref)
	{
		$this->set_default_property(self :: PROPERTY_REF, $ref);
	}
    
    function get_parent()
    {
    	return $this->get_default_property(self :: PROPERTY_PARENT);
    }
	
	function set_parent($parent)
	{
		$this->set_default_property(self :: PROPERTY_PARENT, $parent);
	}
	
	function get_user_id()
    {
    	return $this->get_default_property(self :: PROPERTY_USER_ID);
    }
	
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}
	
	function get_display_order()
	{
		return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
	}
	
	function set_display_order($display_order)
	{
		$this->set_default_property(self :: PROPERTY_DISPLAY_ORDER, $display_order);
	}
	
	function update()
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->update_complex_learning_object_item($this);
	}
	
	function create()
	{
		$rdm = RepositoryDataManager :: get_instance();
		$id = $rdm->get_next_complex_learning_object_item_id();
		$this->set_id($id);
		$this->set_add_date(DokeosUtilities :: to_db_date(time()));
		return $rdm->create_complex_learning_object_item($this);
	}
	
	function delete()
	{
		$rdm = RepositoryDataManager :: get_instance();
		$succes = $rdm->delete_complex_learning_object_item($this);
		
		/*if($succes)
		{
			$conditions[] = new InEqualityCondition(self :: PROPERTY_DISPLAY_ORDER, InEqualityCondition :: GREATER_THAN, $this->get_display_order());
			$conditions[] = new EqualityCondition(self :: PROPERTY_PARENT, $this->get_parent());
			$condition = new AndCondition($conditions); print_r($condition);
			$items = $rdm->retrieve_complex_learning_object_items($condition);
			
			while($item = $items->next_result())
			{
				$item->set_display_order($item->get_display_order() - 1);
				$item->update();
			}
		}*/
		
		return $succes;
	}
	
	/**
	 * Determines whether this complex learning object is an extended complex learning object or not
	 */
	function is_extended()
	{
		return count($this->get_additional_property_names()) > 0;
	}
	
	/**
	 * Retrieves the allowed types to add to this complex learning object item
	 * @return Array of learning object types
	 */
	function get_allowed_types()
	{
		return array();
	}
	
	function is_complex()
	{
		return count($this->get_allowed_types()) > 0;
	}
	
	/**
	 * Creates an instance of an extended class with the given type
	 */
	static function factory($type, $defaultProperties = array(), $additionalProperties = array())
	{
		if($type)
		{
			$class = 'Complex'.DokeosUtilities :: underscores_to_camelcase($type);
			require_once dirname(__FILE__).'/learning_object/'.$type.'/complex_'.$type.'.class.php';
			return new $class ($defaultProperties, $additionalProperties); 
		}
		else
		{ 
			return new self ($defaultProperties, $additionalProperties); 
		}
	}

}
?>