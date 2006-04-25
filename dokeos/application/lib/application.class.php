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
	 * @param boolean $object_id
	 * @return boolean True if the given learning object is in use in this
	 * application
	 */
	abstract function is_published($object_id);
}
?>