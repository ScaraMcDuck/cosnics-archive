<?php
/**
 * @package application.lib.portfolio
 */
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';
//require_once Path :: get_user_path(). 'lib/users_data_manager.class.php';

/**
 *	This class represents a PortfolioPublication. 
 *
 *	PortfolioPublication objects have a number of default properties:
 *	- id: the numeric ID of the PortfolioPublication;
 *	- portfolio: the numeric object ID of the PortfolioPublication (from the repository);
 *	- publisher: the publisher of the PortfolioPublication;
 *	- published: the date when the PortfolioPublication was "posted";
 *	@author Hans de Bisschop
 *	@author Dieter De Neef
 */
class PortfolioPublication
{
	const PROPERTY_ID = 'id';
	const PROPERTY_ITEM = 'portfolio_item';
	const PROPERTY_PUBLISHER = 'publisher';
	const PROPERTY_PUBLISHED = 'published';
	const PROPERTY_TREEITEM = 'treeitem';
	
	
	private $id;
	private $defaultProperties;
	
	/**
	 * Creates a new portfolio object.
	 * @param int $id The numeric ID of the PortfolioPublication object. May be omitted
	 *                if creating a new object.
	 * @param array $defaultProperties The default properties of the PortfolioPublication
	 *                                 object. Associative array.
	 */
	function PortfolioPublication($id = 0, $defaultProperties = array ())
	{
		$this->id = $id;
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this PortfolioPublication object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this PortfolioPublication.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all PortfolioPublications.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_ITEM, self :: PROPERTY_PUBLISHER, self :: PROPERTY_PUBLISHED, self :: PROPERTY_TREEITEM);
	}
	
	/**
	 * Sets a default property of this PortfolioPublication by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default portfolior
	 * property.
	 * @param string $name The identifier.
	 * @return boolean True if the identifier is a property name, false
	 *                 otherwise.
	 */
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}
	
	/**
	 * Returns the id of this PortfolioPublication.
	 * @return int The PortfolioPublication id.
	 */
	function get_id()
	{
		return $this->id;
	}
	
	/**
	 * Returns the learning object id from this PortfolioPublication object
	 * @return int The Portfolio ID
	 */
	function get_portfolio_item()
	{
		return $this->get_default_property(self :: PROPERTY_ITEM);
	}

	/**
	 * Returns the treeitem id from this PortfolioPublication object
	 * @return int The treeitem ID
	 */
	function get_treeitem()
	{
		return $this->get_default_property(self :: PROPERTY_TREEITEM);
	}
	
	 /**
	  * Returns the user of this PortfolioPublication object
	  * @return int the user
	  */
	function get_publisher()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLISHER);
	}
	
	/**
	 * Returns the published timestamp of this PortfolioPublication object
	 * @return Timestamp the published date
	 */
	function get_published()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLISHED);
	} 
	  
	/**
	 * Sets the id of this PortfolioPublication.
	 * @param int $pm_id The PortfolioPublication id.
	 */
	function set_id($id)
	{
		$this->id = $id;
	}	
	
	/**
	 * Sets the learning object id of this PortfolioPublication.
	 * @param Int $id the portfolio ID.
	 */
	function set_portfolio_item($id)
	{
		$this->set_default_property(self :: PROPERTY_ITEM, $id);
	}
	/**
	 * Sets the treeitem id of this PortfolioPublication.
	 * @param Int $id the treeitem ID.
	 */
	function set_treeitem($id)
	{
		$this->set_default_property(self :: PROPERTY_TREEITEM, $id);
	}
	
	/**
	 * Sets the user of this PortfolioPublication.
	 * @param int $user the User.
	 */
	function set_publisher($publisher)
	{
		$this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher);
	}

	/**
	 * Sets the published date of this PortfolioPublication.
	 * @param int $published the timestamp of the published date.
	 */
	function set_published($published)
	{
		$this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
	}
	
	function get_publication_object()
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->retrieve_learning_object($this->get_portfolio_item());
	}
	
	function get_publication_publisher()
	{
		$udm = UserDataManager :: get_instance();
		return $udm->retrieve_user($this->get_publisher());
	}
	
	/**
	 * Instructs the data manager to create the personal message publication, making it
	 * persistent. Also assigns a unique ID to the publication and sets
	 * the publication's creation date to the current time.
	 * @return boolean True if creation succeeded, false otherwise.
	 */
	function create()
	{
		$now = time();
		$this->set_published($now);
		$pmdm = PortfolioDataManager :: get_instance();
		$id = $pmdm->get_next_portfolio_publication_id();
		$this->set_id($id);
		$rdm = RepositoryDataManager :: get_instance();
		$ptm = PFTreeManager :: get_instance();
		$new_item=$ptm->create_child($ptm->get_current_item(), $this->get_publisher());
		$this->set_treeitem($new_item);
		return $pmdm->create_portfolio_publication($this);
	}
	
	/**
	 * Deletes this publication from persistent storage
	 * @see PortfolioDataManager::delete_portfolio_publication()
	 */
	function delete()
	{
		return PortfolioDataManager :: get_instance()->delete_portfolio_publication($this);
	}
	
	/**
	 * Updates this publication in persistent storage
	 * @see PortfolioDataManager::update_portfolio_publication()
	 */
	function update()
	{
		return PortfolioDataManager :: get_instance()->update_portfolio_publication($this);
	}
}
?>
