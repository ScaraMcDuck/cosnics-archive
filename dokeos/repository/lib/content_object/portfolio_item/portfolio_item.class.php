<?php
require_once dirname(__FILE__) . '/../../content_object.class.php';
/**
 * @package repository.learningobject
 * @subpackage portfolio
 */
class PortfolioItem extends ContentObject
{
	const PROPERTY_REFERENCE = 'reference_id';

	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_REFERENCE);
	}

	function get_reference()
	{
		return $this->get_additional_property(self :: PROPERTY_REFERENCE);
	}

	function set_reference($reference)
	{
		$this->set_additional_property(self :: PROPERTY_REFERENCE, $reference);
	}
}
?>