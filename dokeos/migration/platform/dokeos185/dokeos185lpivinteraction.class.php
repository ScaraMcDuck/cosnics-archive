<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 lp_iv_interaction
 *
 * @author Sven Vanpoucke
 */
class Dokeos185LpIvInteraction
{
	/**
	 * Dokeos185LpIvInteraction properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_ORDER_ID = 'order_id';
	const PROPERTY_LP_IV_ID = 'lp_iv_id';
	const PROPERTY_INTERACTION_ID = 'interaction_id';
	const PROPERTY_INTERACTION_TYPE = 'interaction_type';
	const PROPERTY_WEIGHTING = 'weighting';
	const PROPERTY_COMPLETION_TIME = 'completion_time';
	const PROPERTY_CORRECT_RESPONSES = 'correct_responses';
	const PROPERTY_STUDENT_RESPONSE = 'student_response';
	const PROPERTY_RESULT = 'result';
	const PROPERTY_LATENCY = 'latency';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185LpIvInteraction object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185LpIvInteraction($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_ORDER_ID, SELF :: PROPERTY_LP_IV_ID, SELF :: PROPERTY_INTERACTION_ID, SELF :: PROPERTY_INTERACTION_TYPE, SELF :: PROPERTY_WEIGHTING, SELF :: PROPERTY_COMPLETION_TIME, SELF :: PROPERTY_CORRECT_RESPONSES, SELF :: PROPERTY_STUDENT_RESPONSE, SELF :: PROPERTY_RESULT, SELF :: PROPERTY_LATENCY);
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
	 * Returns the id of this Dokeos185LpIvInteraction.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Dokeos185LpIvInteraction.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the order_id of this Dokeos185LpIvInteraction.
	 * @return the order_id.
	 */
	function get_order_id()
	{
		return $this->get_default_property(self :: PROPERTY_ORDER_ID);
	}

	/**
	 * Sets the order_id of this Dokeos185LpIvInteraction.
	 * @param order_id
	 */
	function set_order_id($order_id)
	{
		$this->set_default_property(self :: PROPERTY_ORDER_ID, $order_id);
	}
	/**
	 * Returns the lp_iv_id of this Dokeos185LpIvInteraction.
	 * @return the lp_iv_id.
	 */
	function get_lp_iv_id()
	{
		return $this->get_default_property(self :: PROPERTY_LP_IV_ID);
	}

	/**
	 * Sets the lp_iv_id of this Dokeos185LpIvInteraction.
	 * @param lp_iv_id
	 */
	function set_lp_iv_id($lp_iv_id)
	{
		$this->set_default_property(self :: PROPERTY_LP_IV_ID, $lp_iv_id);
	}
	/**
	 * Returns the interaction_id of this Dokeos185LpIvInteraction.
	 * @return the interaction_id.
	 */
	function get_interaction_id()
	{
		return $this->get_default_property(self :: PROPERTY_INTERACTION_ID);
	}

	/**
	 * Sets the interaction_id of this Dokeos185LpIvInteraction.
	 * @param interaction_id
	 */
	function set_interaction_id($interaction_id)
	{
		$this->set_default_property(self :: PROPERTY_INTERACTION_ID, $interaction_id);
	}
	/**
	 * Returns the interaction_type of this Dokeos185LpIvInteraction.
	 * @return the interaction_type.
	 */
	function get_interaction_type()
	{
		return $this->get_default_property(self :: PROPERTY_INTERACTION_TYPE);
	}

	/**
	 * Sets the interaction_type of this Dokeos185LpIvInteraction.
	 * @param interaction_type
	 */
	function set_interaction_type($interaction_type)
	{
		$this->set_default_property(self :: PROPERTY_INTERACTION_TYPE, $interaction_type);
	}
	/**
	 * Returns the weighting of this Dokeos185LpIvInteraction.
	 * @return the weighting.
	 */
	function get_weighting()
	{
		return $this->get_default_property(self :: PROPERTY_WEIGHTING);
	}

	/**
	 * Sets the weighting of this Dokeos185LpIvInteraction.
	 * @param weighting
	 */
	function set_weighting($weighting)
	{
		$this->set_default_property(self :: PROPERTY_WEIGHTING, $weighting);
	}
	/**
	 * Returns the completion_time of this Dokeos185LpIvInteraction.
	 * @return the completion_time.
	 */
	function get_completion_time()
	{
		return $this->get_default_property(self :: PROPERTY_COMPLETION_TIME);
	}

	/**
	 * Sets the completion_time of this Dokeos185LpIvInteraction.
	 * @param completion_time
	 */
	function set_completion_time($completion_time)
	{
		$this->set_default_property(self :: PROPERTY_COMPLETION_TIME, $completion_time);
	}
	/**
	 * Returns the correct_responses of this Dokeos185LpIvInteraction.
	 * @return the correct_responses.
	 */
	function get_correct_responses()
	{
		return $this->get_default_property(self :: PROPERTY_CORRECT_RESPONSES);
	}

	/**
	 * Sets the correct_responses of this Dokeos185LpIvInteraction.
	 * @param correct_responses
	 */
	function set_correct_responses($correct_responses)
	{
		$this->set_default_property(self :: PROPERTY_CORRECT_RESPONSES, $correct_responses);
	}
	/**
	 * Returns the student_response of this Dokeos185LpIvInteraction.
	 * @return the student_response.
	 */
	function get_student_response()
	{
		return $this->get_default_property(self :: PROPERTY_STUDENT_RESPONSE);
	}

	/**
	 * Sets the student_response of this Dokeos185LpIvInteraction.
	 * @param student_response
	 */
	function set_student_response($student_response)
	{
		$this->set_default_property(self :: PROPERTY_STUDENT_RESPONSE, $student_response);
	}
	/**
	 * Returns the result of this Dokeos185LpIvInteraction.
	 * @return the result.
	 */
	function get_result()
	{
		return $this->get_default_property(self :: PROPERTY_RESULT);
	}

	/**
	 * Sets the result of this Dokeos185LpIvInteraction.
	 * @param result
	 */
	function set_result($result)
	{
		$this->set_default_property(self :: PROPERTY_RESULT, $result);
	}
	/**
	 * Returns the latency of this Dokeos185LpIvInteraction.
	 * @return the latency.
	 */
	function get_latency()
	{
		return $this->get_default_property(self :: PROPERTY_LATENCY);
	}

	/**
	 * Sets the latency of this Dokeos185LpIvInteraction.
	 * @param latency
	 */
	function set_latency($latency)
	{
		$this->set_default_property(self :: PROPERTY_LATENCY, $latency);
	}

}

?>