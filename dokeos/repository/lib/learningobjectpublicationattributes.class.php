<?php
/**
==============================================================================
 * Instances of this class group generic information about a publication of
 * a learning object within an application.
 * 
 * @package repository
 * @author Bart Mollet
 * @author Tim De Pauw
==============================================================================
 */

class LearningObjectPublicationAttributes
{
	/**
	 * The name of the application in which the learning object was published.
	 */
	private $application;
	
	/**
	 * The location in the application where the learning object was published.
	 */
	private $location;
	
	/**
	 * A URL pointing to the location where the learning object was published.
	 */
	private $url;
	
	/**
	 * The ID of the user who published the learning object.
	 */
	private $publisher_user_id;
	
	/**
	 * The date on which the learning object was published.
	 */
	private $publication_date;
	
	/**
	 * Constructor.
	 */
	function LearningObjectPublicationAttributes()
	{
	}
	
	/**
	 * Gets the name of the application where the learning object was
	 * published.
	 * @return string The application name.
	 */
	function get_application()
	{
		return $this->application;
	}
	
	/**
	 * Sets the name of the application where the learning object was
	 * published.
	 * @param string $application The application name.
	 */
	function set_application($application)
	{
		$this->application = $application;
	}
	
	/**
	 * Gets the location within the application where the learning object was
	 * published.
	 * @return string The location.
	 */
	function get_location()
	{
		return $this->location;
	}
	
	/**
	 * Sets the location within the application where the learning object was
	 * published.
	 * @param string $location The location.
	 */ 
	function set_location($location)
	{
		$this->location = $location;
	}
	
	/**
	 * Gets the URL where the publication can be found.
	 * @return string The URL.
	 */
	function get_url()
	{
		return $this->url;
	}
	
	/**
	 * Sets the URL where the publication can be found.
	 * @param string $url The URL.
	 */
	function set_url($url)
	{
		$this->url = $url;
	}
	
	/**
	 * Sets the ID of the user who published the learning object.
	 * @return int The ID.
	 */
	function get_publisher_user_id()
	{
		return $this->publisher_user_id;
	}
	
	/**
	 * Sets the ID of the user who published the learning object.
	 * @param int $user_id The user ID.
	 */
	function set_publisher_user_id($user_id)
	{
		$this->publisher_user_id = $user_id;
	}
	
	/**
	 * Gets the date when the learning object was published.
	 * @return int The date as a UNIX timestamp.
	 */
	function get_publication_date()
	{
		return $this->publication_date;
	}
	
	/**
	 * Sets the date when the learning object was published.
	 * @param int $date The date as a UNIX timestamp.
	 */
	function set_publication_date($date)
	{
		$this->publication_date = $date;
	}
}
?>