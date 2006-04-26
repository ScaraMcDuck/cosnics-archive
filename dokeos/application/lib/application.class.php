<?php

/**
==============================================================================
 *	This is the base class for all applications based on the learning object
 *	repository.
 *
 *	@author Tim De Pauw
==============================================================================
 */

abstract class Application
{
	/**
	 * Runs the application.
	 */
	abstract function run();
	/**
	 * Checks if a given learning object is published in this application
	 * @param int $object_id
	 * @return boolean True if the given learning object is in use in this
	 * application
	 */
	abstract function is_published($object_id);
	/**
	 * Get information about the publication of the given learning object
	 * @param int $object_id
	 * @return array An array of PublicationInformation objects (empty array if
	 * the requested learning object isn't published in this application)
	 */
	abstract function get_publication_information($object_id);
}
?>