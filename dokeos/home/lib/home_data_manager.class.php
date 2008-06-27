<?php
/**
 * $Id: repository_data_manager.class.php 11718 2007-03-27 09:52:32Z Scara84 $
 * @package repository
 */
require_once Path :: get_library_path().'configuration/configuration.class.php';
require_once dirname(__FILE__).'/../../repository/lib/repository_data_manager.class.php';

/**
 *	This is a skeleton for a data manager for the Users table.
 *	Data managers must extend this class and implement its abstract methods.
 *
 *	@author Hans De Bisschop
 *	@author Dieter De Neef
 */
abstract class HomeDataManager
{
	/**
	 * Instance of this class for the singleton pattern.
	 */
	private static $instance;

	/**
	 * Associative array that maps learning object types to their
	 * corresponding array of property names.
	 */
	private $typeProperties;

	/**
	 * Array which contains the registered applications running on top of this
	 * repositorydatamanager
	 */
	private $applications;

	/**
	 * Constructor.
	 */
	protected function HomeDataManager()
	{
		$this->initialize();
		$this->applications = array();
		$this->load_applications();
	}
	
	/**
	 * Initializes the data manager.
	 */
	abstract function initialize();
	
	abstract function get_next_home_column_id();
	
	abstract function get_next_home_block_id();

	/**
	 * Uses a singleton pattern and a factory pattern to return the data
	 * manager. The configuration determines which data manager class is to
	 * be instantiated.
	 * @return UsersDataManager The data manager.
	 */
	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.strtolower($type).'.class.php';
			$class = $type.'HomeDataManager';
			self :: $instance = new $class ();
		}
		return self :: $instance;
	}
	
	/**
	 * Creates a storage unit
	 * @param string $name Name of the storage unit
	 * @param array $properties Properties of the storage unit
	 * @param array $indexes The indexes which should be defined in the created
	 * storage unit
	 */
	abstract function create_storage_unit($name,$properties,$indexes);
	
	abstract function count_home_rows($condition = null);
	
	abstract function count_home_columns($condition = null);
	
	abstract function count_home_blocks($condition = null);
	
	abstract function retrieve_home_rows($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null);
	
	abstract function retrieve_home_column($id);
	
	abstract function retrieve_home_columns($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null);
	
	abstract function retrieve_home_block($id);
	
	abstract function retrieve_home_blocks($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null);
	
	abstract function truncate_home();
	
	/**
	 * Returns the names of the applications known to this
	 * repository datamanager.
	 * @return array The applications.
	 */
	function get_registered_applications()
	{
		return $this->applications;
	}

	/**
	 * Registers an application with this repository datamanager.
	 * @param string $application The application name.
	 */
	function register_application($application)
	{
		if (in_array($application, $this->applications))
		{
			die('Application already registered: '.$application);
		}
		$this->applications[] = $application;
	}

	/**
	 * Loads the applications installed on the system. Applications are classes
	 * in the /application/lib subdirectory. Each application is a directory,
	 * which in its turn contains a class file named after the application. For
	 * instance, the weblcms application is the class Weblcms, defined in
	 * /application/lib/weblcms/weblcms.class.php. Applications must extend the
	 * Application class.
	 */
	private function load_applications()
	{
		$path = dirname(__FILE__).'/../../application/lib';
		if ($handle = opendir($path))
		{
			while (false !== ($file = readdir($handle)))
			{
				$toolPath = $path.'/'. $file .'/'.$file.'_manager';
				if (is_dir($toolPath) && self :: is_application_name($file))
				{
					require_once $toolPath.'/'.$file.'.class.php';
					$this->register_application($file);
				}
			}
			closedir($handle);
		}
		else
		{
			die('Failed to load applications');
		}
	}

	/**
	 * Converts an application name to the corresponding class name.
	 * @param string $application The application name.
	 * @return string The class name.
	 */
	static function application_to_class($application)
	{
		return ucfirst(preg_replace('/_([a-z])/e', 'strtoupper(\1)', $application));
	}

	/**
	 * Converts an application class name to the corresponding application name.
	 * @param string $class The class name.
	 * @return string The application name.
	 */
	static function class_to_application($class)
	{
		return preg_replace(array ('/^([A-Z])/e', '/([A-Z])/e'), array ('strtolower(\1)', '"_".strtolower(\1)'), $class);
	}

	/**
	 * Determines whether or not the given name is a valid application name.
	 * @param string $name The name to evaluate.
	 * @return True if the name is a valid application name, false otherwise.
	 */
	static function is_application_name($name)
	{
		return (preg_match('/^[a-z][a-z_]+$/', $name) > 0);
	}
	
	function get_applications()
	{
		return $this->applications;
	}
	
	abstract function retrieve_home_row_at_sort($sort, $direction);
	
	abstract function retrieve_home_column_at_sort($parent, $sort, $direction);
	
	abstract function retrieve_home_block_at_sort($parent, $sort, $direction);
	
	abstract function update_home_block($home_block);
	
	abstract function update_home_block_config($home_block_config);
	
	abstract function update_home_column($home_column);
	
	abstract function update_home_row($home_row);
	
	abstract function create_home_row($home_row);
	
	abstract function create_home_column($home_column);
	
	abstract function create_home_block($home_block);
	
	abstract function create_home_block_config($home_block_config);
	
	abstract function delete_home_row($home_row);
	
	abstract function delete_home_column($home_column);
	
	abstract function delete_home_block($home_block);
	
	abstract function delete_home_block_config($home_block_config);
	
	abstract function retrieve_home_block_config($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null);
	
	abstract function count_home_block_config($condition = null);
	
	function retrieve_block_properties($component)
	{
		$application = explode('.', strtolower($component), 2);
		$path = dirname(__FILE__).'/../../application/lib/'. $application[0] . '/block/'. $application[0] . $application[1] . '.xml';
		
		if (file_exists($path))
		{
			$doc = new DOMDocument();
			$doc->load($path);
			$object = $doc->getElementsByTagname('block')->item(0);
			$name = $object->getAttribute('name');
			$xml_properties = $doc->getElementsByTagname('property');
			foreach($xml_properties as $index => $property)
			{
				 $properties[$property->getAttribute('name')] = $property->getAttribute('default');
			}
			
			return $properties;
		}
		else
		{
			return null;
		}
	}	
}
?>