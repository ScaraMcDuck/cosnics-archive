<?php 
/**
 * assessment
 */
require_once Path :: get_common_path() . 'data_class.class.php';

/**
 * This class describes a AssessmentPublication data object
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentPublication extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * AssessmentPublication properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_LEARNING_OBJECT = 'learning_object';
	const PROPERTY_FROM_DATE = 'from_date';
	const PROPERTY_TO_DATE = 'to_date';
	const PROPERTY_HIDDEN = 'hidden';
	const PROPERTY_PUBLISHER = 'publisher';
	const PROPERTY_PUBLISHED = 'published';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_LEARNING_OBJECT, self :: PROPERTY_FROM_DATE, self :: PROPERTY_TO_DATE, self :: PROPERTY_HIDDEN, self :: PROPERTY_PUBLISHER, self :: PROPERTY_PUBLISHED);
	}

	function get_data_manager()
	{
		return AssessmentDataManager :: get_instance();
	}

	/**
	 * Returns the id of this AssessmentPublication.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this AssessmentPublication.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	/**
	 * Returns the learning_object of this AssessmentPublication.
	 * @return the learning_object.
	 */
	function get_learning_object()
	{
		return $this->get_default_property(self :: PROPERTY_LEARNING_OBJECT);
	}

	/**
	 * Sets the learning_object of this AssessmentPublication.
	 * @param learning_object
	 */
	function set_learning_object($learning_object)
	{
		$this->set_default_property(self :: PROPERTY_LEARNING_OBJECT, $learning_object);
	}

	/**
	 * Returns the from_date of this AssessmentPublication.
	 * @return the from_date.
	 */
	function get_from_date()
	{
		return $this->get_default_property(self :: PROPERTY_FROM_DATE);
	}

	/**
	 * Sets the from_date of this AssessmentPublication.
	 * @param from_date
	 */
	function set_from_date($from_date)
	{
		$this->set_default_property(self :: PROPERTY_FROM_DATE, $from_date);
	}

	/**
	 * Returns the to_date of this AssessmentPublication.
	 * @return the to_date.
	 */
	function get_to_date()
	{
		return $this->get_default_property(self :: PROPERTY_TO_DATE);
	}

	/**
	 * Sets the to_date of this AssessmentPublication.
	 * @param to_date
	 */
	function set_to_date($to_date)
	{
		$this->set_default_property(self :: PROPERTY_TO_DATE, $to_date);
	}

	/**
	 * Returns the hidden of this AssessmentPublication.
	 * @return the hidden.
	 */
	function get_hidden()
	{
		return $this->get_default_property(self :: PROPERTY_HIDDEN);
	}

	/**
	 * Sets the hidden of this AssessmentPublication.
	 * @param hidden
	 */
	function set_hidden($hidden)
	{
		$this->set_default_property(self :: PROPERTY_HIDDEN, $hidden);
	}

	/**
	 * Returns the publisher of this AssessmentPublication.
	 * @return the publisher.
	 */
	function get_publisher()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLISHER);
	}

	/**
	 * Sets the publisher of this AssessmentPublication.
	 * @param publisher
	 */
	function set_publisher($publisher)
	{
		$this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher);
	}

	/**
	 * Returns the published of this AssessmentPublication.
	 * @return the published.
	 */
	function get_published()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLISHED);
	}

	/**
	 * Sets the published of this AssessmentPublication.
	 * @param published
	 */
	function set_published($published)
	{
		$this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
	}


	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>