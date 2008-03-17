<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 blog_rating
 *
 * @author Sven Vanpoucke
 */
class Dokeos185BlogRating
{
	/**
	 * Dokeos185BlogRating properties
	 */
	const PROPERTY_RATING_ID = 'rating_id';
	const PROPERTY_BLOG_ID = 'blog_id';
	const PROPERTY_RATING_TYPE = 'rating_type';
	const PROPERTY_ITEM_ID = 'item_id';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_RATING = 'rating';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185BlogRating object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185BlogRating($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_RATING_ID, SELF :: PROPERTY_BLOG_ID, SELF :: PROPERTY_RATING_TYPE, SELF :: PROPERTY_ITEM_ID, SELF :: PROPERTY_USER_ID, SELF :: PROPERTY_RATING);
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
	 * Returns the rating_id of this Dokeos185BlogRating.
	 * @return the rating_id.
	 */
	function get_rating_id()
	{
		return $this->get_default_property(self :: PROPERTY_RATING_ID);
	}

	/**
	 * Sets the rating_id of this Dokeos185BlogRating.
	 * @param rating_id
	 */
	function set_rating_id($rating_id)
	{
		$this->set_default_property(self :: PROPERTY_RATING_ID, $rating_id);
	}
	/**
	 * Returns the blog_id of this Dokeos185BlogRating.
	 * @return the blog_id.
	 */
	function get_blog_id()
	{
		return $this->get_default_property(self :: PROPERTY_BLOG_ID);
	}

	/**
	 * Sets the blog_id of this Dokeos185BlogRating.
	 * @param blog_id
	 */
	function set_blog_id($blog_id)
	{
		$this->set_default_property(self :: PROPERTY_BLOG_ID, $blog_id);
	}
	/**
	 * Returns the rating_type of this Dokeos185BlogRating.
	 * @return the rating_type.
	 */
	function get_rating_type()
	{
		return $this->get_default_property(self :: PROPERTY_RATING_TYPE);
	}

	/**
	 * Sets the rating_type of this Dokeos185BlogRating.
	 * @param rating_type
	 */
	function set_rating_type($rating_type)
	{
		$this->set_default_property(self :: PROPERTY_RATING_TYPE, $rating_type);
	}
	/**
	 * Returns the item_id of this Dokeos185BlogRating.
	 * @return the item_id.
	 */
	function get_item_id()
	{
		return $this->get_default_property(self :: PROPERTY_ITEM_ID);
	}

	/**
	 * Sets the item_id of this Dokeos185BlogRating.
	 * @param item_id
	 */
	function set_item_id($item_id)
	{
		$this->set_default_property(self :: PROPERTY_ITEM_ID, $item_id);
	}
	/**
	 * Returns the user_id of this Dokeos185BlogRating.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	/**
	 * Sets the user_id of this Dokeos185BlogRating.
	 * @param user_id
	 */
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}
	/**
	 * Returns the rating of this Dokeos185BlogRating.
	 * @return the rating.
	 */
	function get_rating()
	{
		return $this->get_default_property(self :: PROPERTY_RATING);
	}

	/**
	 * Sets the rating of this Dokeos185BlogRating.
	 * @param rating
	 */
	function set_rating($rating)
	{
		$this->set_default_property(self :: PROPERTY_RATING, $rating);
	}

}

?>