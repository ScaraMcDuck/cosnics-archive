<?php
/**
 * @package application.lib.encyclopedia.publisher
 */
require_once dirname(__FILE__).'/../publisher_component.class.php';

/**
 * This class represents a publisher component which publishes objects passed to it.
 */
abstract class PublisherPublicationCreatorComponent extends PublisherComponent
{
	
	/*
	 * Returns the html code for this publisher's form for a given object id.
	 */
	function as_html($params = array()) {
		$oid = $_GET[Publisher :: PARAM_ID];
		$edit = $_GET[Publisher :: PARAM_EDIT];
		return $this->get_publication_form($oid, ($edit == 0));
	}
	
	/**
	 * Gets the form to publish the learning object.
	 * @return string|null A HTML-representation of the form. When the
	 * publication form was validated, this function will send header
	 * information to redirect the end user to the location where the
	 * publication was made.
	 */
	abstract function get_publication_form($learning_object_id, $new = false);
	
}

?>