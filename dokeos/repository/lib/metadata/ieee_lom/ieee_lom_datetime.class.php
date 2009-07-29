<?php
/**
 * $Id: ieee_lom_datetime.class.php 9169 2006-08-29 13:01:29Z bmol $
 * @package repository.metadata
 * @subpackage ieee_lom
 */
/**
 * A DateTime field used in IEEE LOM. This object contains a date & time value
 * and a description
 */
class IeeeLomDateTime {
	/**
	 * The date & time value
	 */
	private $datetime;
	
	/**
	 * The description
	 */
	private $description;
	
	/**
	 * Constructor
	 * @param string|null $datetime
	 * @param LangString|null $description
	 */
    function IeeeLomDateTime($datetime = null, $description = null) 
    {
    	$this->datetime = $datetime;
    	$this->description = $description;
    }
    
    /**
     * Gets the date & time value
     * @return string|null
     */
    function get_datetime()
    {
    	return $this->datetime;
    }
    
    /**
     * Gets the description
     * @return LangString|null
     */
    function get_description()
    {
    	return $this->description;
    }
    
}
?>