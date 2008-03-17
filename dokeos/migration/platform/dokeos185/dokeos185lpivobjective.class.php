<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 lp_iv_objective
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_LP_IV_ID, SELF :: PROPERTY_ORDER_ID, SELF :: PROPERTY_OBJECTIVE_ID, SELF :: PROPERTY_SCORE_RAW, SELF :: PROPERTY_SCORE_MAX, SELF :: PROPERTY_SCORE_MIN, SELF :: PROPERTY_STATUS);
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
	 * Sets the id of this Dokeos185LpIvObjective.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
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
	 * Sets the lp_iv_id of this Dokeos185LpIvObjective.
	 * @param lp_iv_id
	 */
	function set_lp_iv_id($lp_iv_id)
	{
		$this->set_default_property(self :: PROPERTY_LP_IV_ID, $lp_iv_id);
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
	 * Sets the order_id of this Dokeos185LpIvObjective.
	 * @param order_id
	 */
	function set_order_id($order_id)
	{
		$this->set_default_property(self :: PROPERTY_ORDER_ID, $order_id);
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
	 * Sets the objective_id of this Dokeos185LpIvObjective.
	 * @param objective_id
	 */
	function set_objective_id($objective_id)
	{
		$this->set_default_property(self :: PROPERTY_OBJECTIVE_ID, $objective_id);
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
	 * Sets the score_raw of this Dokeos185LpIvObjective.
	 * @param score_raw
	 */
	function set_score_raw($score_raw)
	{
		$this->set_default_property(self :: PROPERTY_SCORE_RAW, $score_raw);
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
	 * Sets the score_max of this Dokeos185LpIvObjective.
	 * @param score_max
	 */
	function set_score_max($score_max)
	{
		$this->set_default_property(self :: PROPERTY_SCORE_MAX, $score_max);
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
	 * Sets the score_min of this Dokeos185LpIvObjective.
	 * @param score_min
	 */
	function set_score_min($score_min)
	{
		$this->set_default_property(self :: PROPERTY_SCORE_MIN, $score_min);
	}
	/**
	 * Returns the status of this Dokeos185LpIvObjective.
	 * @return the status.
	 */
	function get_status()
	{
		return $this->get_default_property(self :: PROPERTY_STATUS);
	}

	/**
	 * Sets the status of this Dokeos185LpIvObjective.
	 * @param status
	 */
	function set_status($status)
	{
		$this->set_default_property(self :: PROPERTY_STATUS, $status);
	}

}

?>