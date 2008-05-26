<?php
/**
 * $Id$
 * @package repository
 */
/**
 * This interface provides some common accessor functions for learning objects.
 */
interface AccessibleLearningObject
{
	/**
	 * Gets the type of the learning object
	 * @return string
	 */
	function get_type();
	/**
	 * Gets the title of the learning object
	 * @return string
	 */
	function get_title();
	/**
	 * Gets the description of the learning object
	 * @return string
	 */
	function get_description();
	/**
	 * Gets the creation date of the learning object
	 * @return int The creation date
	 */
	function get_creation_date();
	/**
	 * Gets the modification date of the learning object
	 * @return int The modification date
	 */
	function get_modification_date();
	/**
	 * Returns the full URL where this learning object may be viewed.
	 * @return string The URL.
	 */
	function get_view_url();
}
?>