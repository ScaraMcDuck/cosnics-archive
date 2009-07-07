<?php 
/**
 * portfolio
 */

/**
 * This class describes a PortfolioPublicationUser data object
 *
 * @author Sven Vanpoucke
 */
class PortfolioPublicationUser
{
	const CLASS_NAME = __CLASS__;

	/**
	 * PortfolioPublicationUser properties
	 */
	const PROPERTY_PORTFOLIO_PUBLICATION = 'portfolio_publication';
	const PROPERTY_USER = 'user';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new PortfolioPublicationUser object
	 * @param array $defaultProperties The default properties
	 */
	function PortfolioPublicationUser($defaultProperties = array ())
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
		return array (self :: PROPERTY_PORTFOLIO_PUBLICATION, self :: PROPERTY_USER);
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
	 * Returns the portfolio_publication of this PortfolioPublicationUser.
	 * @return the portfolio_publication.
	 */
	function get_portfolio_publication()
	{
		return $this->get_default_property(self :: PROPERTY_PORTFOLIO_PUBLICATION);
	}

	/**
	 * Sets the portfolio_publication of this PortfolioPublicationUser.
	 * @param portfolio_publication
	 */
	function set_portfolio_publication($portfolio_publication)
	{
		$this->set_default_property(self :: PROPERTY_PORTFOLIO_PUBLICATION, $portfolio_publication);
	}
	/**
	 * Returns the user of this PortfolioPublicationUser.
	 * @return the user.
	 */
	function get_user()
	{
		return $this->get_default_property(self :: PROPERTY_USER);
	}

	/**
	 * Sets the user of this PortfolioPublicationUser.
	 * @param user
	 */
	function set_user($user)
	{
		$this->set_default_property(self :: PROPERTY_USER, $user);
	}

	function delete()
	{
		$dm = PortfolioDataManager :: get_instance();
		return $dm->delete_portfolio_publication_user($this);
	}

	function create()
	{
		$dm = PortfolioDataManager :: get_instance();
		$this->set_id($dm->get_next_portfolio_publication_user_id());
       	return $dm->create_portfolio_publication_user($this);
	}

	function update()
	{
		$dm = PortfolioDataManager :: get_instance();
		return $dm->update_portfolio_publication_user($this);
	}

	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>