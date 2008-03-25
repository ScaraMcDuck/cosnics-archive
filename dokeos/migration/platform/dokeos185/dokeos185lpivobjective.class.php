<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 lp_iv_objective
 *
 * @author Sven Vanpoucke
 */
class Dokeos185LpIvObjective
{
	/**
	 * Dokeos185LpIvObjective properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_LP_IV_ID = 'lp_iv_id';
	const PROPERTY_ORDER_ID = 'order_id';
	const PROPERTY_OBJECTIVE_ID = 'objective_id';
	const PROPERTY_SCORE_RAW = 'score_raw';
	const PROPERTY_SCORE_MAX = 'score_max';
	const PROPERTY_SCORE_MIN = 'score_min';
	const PROPERTY_STATUS = 'status';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185LpIvObjective object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185LpIvObjective($defaultProperties = array ())
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_LP_IV_ID, self :: PROPERTY_ORDER_ID, self :: PROPERTY_OBJECTIVE_ID, self :: PROPERTY_SCORE_RAW, self :: PROPERTY_SCORE_MAX, self :: PROPERTY_SCORE_MIN, self :: PROPERTY_STATUS);
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
	 * Returns the id of this Dokeos185LpIvObjective.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the lp_iv_id of this Dokeos185LpIvObjective.
	 * @return the lp_iv_id.
	 */
	function get_lp_iv_id()
	{
		return $this->get_default_property(self :: PROPERTY_LP_IV_ID);
	}

	/**
	 * Returns the order_id of this Dokeos185LpIvObjective.
	 * @return the order_id.
	 */
	function get_order_id()
	{
		return $this->get_default_property(self :: PROPERTY_ORDER_ID);
	}

	/**
	 * Returns the objective_id of this Dokeos185LpIvObjective.
	 * @return the objective_id.
	 */
	function get_objective_id()
	{
		return $this->get_default_property(self :: PROPERTY_OBJECTIVE_ID);
	}

	/**
	 * Returns the score_raw of this Dokeos185LpIvObjective.
	 * @return the score_raw.
	 */
	function get_score_raw()
	{
		return $this->get_default_property(self :: PROPERTY_SCORE_RAW);
	}

	/**
	 * Returns the score_max of this Dokeos185LpIvObjective.
	 * @return the score_max.
	 */
	function get_score_max()
	{
		return $this->get_default_property(self :: PROPERTY_SCORE_MAX);
	}

	/**
	 * Returns the score_min of this Dokeos185LpIvObjective.
	 * @return the score_min.
	 */
	function get_score_min()
	{
		return $this->get_default_property(self :: PROPERTY_SCORE_MIN);
	}

	/**
	 * Returns the status of this Dokeos185LpIvObjective.
	 * @return the status.
	 */
	function get_status()
	{
		return $this->get_default_property(self :: PROPERTY_STATUS);
	}


}

?>