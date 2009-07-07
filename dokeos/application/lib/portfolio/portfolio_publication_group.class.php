<?php 
/**
 * portfolio
 */

/**
 * This class describes a PortfolioPublicationGroup data object
 *
 * @author Sven Vanpoucke
 */
class PortfolioPublicationGroup
{
	const CLASS_NAME = __CLASS__;

	/**
	 * PortfolioPublicationGroup properties
	 */
	const PROPERTY_PORTFOLIO_PUBLICATION = 'portfolio_publication';
	const PROPERTY_GROUP_ID = 'group_id';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new PortfolioPublicationGroup object
	 * @param array $defaultProperties The default properties
	 */
	function PortfolioPublicationGroup($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Gets a default property by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	/**
	 * Gets the default properties
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_PORTFOLIO_PUBLICATION, self :: PROPERTY_GROUP_ID);
	}

	/**
	 * Sets a default property by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	/**
	 * Sets the default properties of this class
	 */
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Returns the portfolio_publication of this PortfolioPublicationGroup.
	 * @return the portfolio_publication.
	 */
	function get_portfolio_publication()
	{
		return $this->get_default_property(self :: PROPERTY_PORTFOLIO_PUBLICATION);
	}

	/**
	 * Sets the portfolio_publication of this PortfolioPublicationGroup.
	 * @param portfolio_publication
	 */
	function set_portfolio_publication($portfolio_publication)
	{
		$this->set_default_property(self :: PROPERTY_PORTFOLIO_PUBLICATION, $portfolio_publication);
	}
	/**
	 * Returns the group_id of this PortfolioPublicationGroup.
	 * @return the group_id.
	 */
	function get_group_id()
	{
		return $this->get_default_property(self :: PROPERTY_GROUP_ID);
	}

	/**
	 * Sets the group_id of this PortfolioPublicationGroup.
	 * @param group_id
	 */
	function set_group_id($group_id)
	{
		$this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
	}

	function delete()
	{
		$dm = PortfolioDataManager :: get_instance();
		return $dm->delete_portfolio_publication_group($this);
	}

	function create()
	{
		$dm = PortfolioDataManager :: get_instance();
		$this->set_id($dm->get_next_portfolio_publication_group_id());
       	return $dm->create_portfolio_publication_group($this);
	}

	function update()
	{
		$dm = PortfolioDataManager :: get_instance();
		return $dm->update_portfolio_publication_group($this);
	}

	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>