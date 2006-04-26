<?php
/**
 * @package repository
 */
/**
 * A PublicationInformation object can be used to pass information about the
 * publication of a learning object from an application to the repository.
 */
class publicationInformation {
	/**
	 * User id of the publisher
	 */
	private $publisher_user_id;
	/**
	 * The date on which the object was published
	 */
	private $publication_date;
	/**
	 * The application in which the learning object was published
	 */
	private $application;
	/**
	 * The location in the application where the learning object was published
	 */
	private $location;
	/**
	 * An url pointing to the location where the learning object was published
	 */
	private $url;
	/**
	 * Create a new publication information object
	 */
    function PublicationInformation() {
    }
    /**
     * Set publisher user id
     * @param int $user_id
     */
    function set_publisher_user_id($user_id)
    {
    	$this->publisher_user_id = $user_id;
    }
    /**
     * Get publisher user id
     * @return int
     */
    function get_publisher_user_id()
    {
    	return $this->publisher_user_id;
    }
    /**
     * Set publication date
     * @param int $date
     */
    function set_publication_date($date)
    {
    	$this->publication_date = $date;
    }
    /**
     * Get publication date
     * @return int
     */
    function get_publication_date()
    {
    	return $this->publication_date;
    }
    /**
     * Get application
     * @return string
     */
    function get_application()
    {
    	return $this->application;
    }
    /**
     * Set application
     * @param string $application
     */
    function set_application($application)
    {
    	$this->application = $application;
    }
    /**
     * Get location
     * @return string
     */
    function get_location()
    {
    	return $this->location;
    }
    /**
     * Set location
     * @param string $location
     */
    function set_location($location)
	{
		$this->location = $location;
	}
	/**
	 * Get url
	 * @return string
	 */
	function get_url()
	{
		return $this->url;
	}
	/**
	 * Set url
	 * @param string $url
	 */
	function set_url($url)
	{
		$this->url = $url;
	}
}
?>