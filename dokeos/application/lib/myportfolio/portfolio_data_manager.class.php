<?php
/**
 * $Id:$
 * @package application.portfolio
 */

require_once Path :: get_library_path().'configuration/configuration.class.php';
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';

/**
================================================================================
 *	
 *
 *	@author Roel Neefs
================================================================================
 */

abstract class PortfolioDataManager
{
	private static $instance;

	protected function PortfolioDataManager()
	{
		$this->initialize();
	}

	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.strtolower($type).'.class.php';
			$class = $type.'PortfolioDataManager';
			self :: $instance = new $class ();
		}
		return self :: $instance;
	}

	abstract function initialize();

	abstract function get_root_element($user);

	abstract function get_owner($item);

	abstract function get_item_title($item);

	abstract function get_item_children($item);

	abstract function create_page($user);

	abstract function connect_parent_to_child($parent,$child,$user);

	abstract function get_parent($item);

	abstract function set_parent($item, $new_parent);

	abstract function remove_item($item); 

	/**
	 * Determines whether any of the given learning objects has been published
	 * in this application.
	 * @param array $object_ids The Id's of the learning objects
	 * @return boolean True if at least one of the given objects is published in
	 * this application, false otherwise
	 */
	abstract function any_learning_object_is_published($object_ids);
	
	/**
	 * Returns whether a given object id is published in this application 
	 * @param int $object_id
	 * @return boolean Is the object is published
	 */
	abstract function learning_object_is_published($object_id);
	
	/**
	 * Gets the publication attributes of a given learning object id
	 * @param int $object_id The object id
	 * @param string $type Type of retrieval
	 * @param int $offset
	 * @param int $count
	 * @param int $order_property
	 * @param int $order_direction
	 * @return LearningObjectPublicationAttribute
	 */
	abstract function get_learning_object_publication_attribute($object_id);
	
	/**
	 * Gets the publication attributes of a given array of learning object id's
	 * @param array $object_id The array of object ids
	 * @param string $type Type of retrieval
	 * @param int $offset
	 * @param int $count
	 * @param int $order_property
	 * @param int $order_direction
	 * @return array An array of Learing Object Publication Attributes
	 */
	abstract function get_learning_object_publication_attributes($user, $object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null);
	
	/**
	 * Counts the publication attributes
	 * @param string $type Type of retrieval
	 * @param Condition $conditions
	 * @return int
	 */
	abstract function count_publication_attributes($user, $type = null, $condition = null);
    
    /**
     * Get the next available portfolio publication ID
     * @return int
     */
    abstract function get_next_portfolio_publication_id();
    
    /**
     * Get the next available treeitem ID
     * @return int
     */
    abstract function get_next_tree_item_id();
    
    /**
	 * Count the publications
	 * @param Condition $condition
	 * @return int
	 */	
    abstract function count_portfolio_publications($condition = null);


	/**
	 * Retrieve a portfolio publication
	 * @param int $id
	 * @return ProfilePublication
	 */	
	abstract function retrieve_portfolio_publication($id);

	/**
	 * Retrieve a portfolio publication
	 * @param int $id
	 * @return ProfilePublication
	 */	
	abstract function retrieve_portfolio_publication_from_item($item);
    
    /**
	 * Retrieve a series of portfolio publications 
	 * @param Condition $condition
	 * @param array $orderBy
	 * @param array $orderDir
	 * @param int $offset
	 * @param int $maxObjects
	 * @return ProfilePublicationResultSet
	 */	
    abstract function retrieve_portfolio_publications($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1);
    
    /**
	 * Update the publication
	 * @param ProfilePublication $portfolio_publication
	 * @return boolean
	 */	
    abstract function update_portfolio_publication($portfolio_publication);
    
    /**
	 * Delete the publication
	 * @param ProfilePublication $portfolio_publication
	 * @return boolean
	 */	
    abstract function delete_portfolio_publication($portfolio_publication);
    
    /**
	 * Delete the publications
	 * @param Array $object_id An array of publication ids
	 * @return boolean
	 */	
    abstract function delete_portfolio_publications($object_id);
    
    /**
	 * Update the publication id
	 * @param LearningObjectPublicationAttribure $publication_attr
	 * @return boolean
	 */	
    abstract function update_portfolio_publication_id($publication_attr);
    
    /**
	 * Create a publication
	 * @param PersonalMessagePublication $publication
	 * @return boolean
	 */
    abstract function create_portfolio_publication($publication);

	/**
	 * Creates a storage unit
	 * @param string $name Name of the storage unit
	 * @param array $properties Properties of the storage unit
	 * @param array $indexes The indexes which should be defined in the created
	 * storage unit
	 */
	abstract function create_storage_unit($name,$properties,$indexes);

}
?>