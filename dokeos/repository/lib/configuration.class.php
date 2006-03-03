<?php
/**
==============================================================================
*	This is a class representing a configuration based on an INI file called
*	"configuration.ini" in the "conf" directory. It uses a singleton pattern
*	to ensure that the file is read only once.
==============================================================================
*/
class Configuration {
	/**
	 * Instance of this class for the singleton pattern.
	 */
	private static $instance;
	
	/**
	 * Parameters defined in the configuration file. Stored as an associative
	 * array, as returned by PHP's parse_ini_file() function.
	 */
	private $params;
	
	/**
	 * Constructor.
	 */
    private function Configuration () {
    	$this->params = parse_ini_file(
    		dirname(__FILE__) . '/../conf/configuration.ini', 
    		true);
    }
    
    /**
     * Returns the instance of this class.
     * @return Configuration The instance.
     */
    static function get_instance () {
    	if (!isset(self::$instance)) {
    		self::$instance = new self();
    	}
    	return self::$instance;
    }
	
	/**
	 * Gets a parameter from the configuration.
	 * @param string $section The name of the section in which the parameter
	 *                        is located.
	 * @param string $name The parameter name.
	 * @return mixed The parameter value.
	 */
    function get_parameter ($section, $name) {
    	return $this->params[$section][$name];
    }
}
?>