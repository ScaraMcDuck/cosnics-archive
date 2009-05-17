<?php
/**
 * $Id$
 * @package application
 */
require_once(Path :: get_library_path().'filesystem/filesystem.class.php');
require_once(Path :: get_admin_path().'lib/admin_data_manager.class.php');
require_once(Path :: get_admin_path().'lib/registration.class.php');
/**
 * This	is the base class for all applications based on the learning object
 * repository.
 * @author Tim De Pauw
 */
abstract class Application
{
	const PARAM_APPLICATION = 'application';
	
	/**
	 * Runs the application.
	 */
	abstract function run();
	
	/**
	 * Determines whether the given learning object has been published in this
	 * application.
	 * @param int $object_id The ID of the learning object.
	 * @return boolean True if the object is currently published, false
	 *                 otherwise.
	 */
	abstract function learning_object_is_published($object_id);
	
	/**
	 * Determines whether any of the given learning objects has been published
	 * in this application.
	 * @param array $object_ids The Id's of the learning objects
	 * @return boolean True if at least one of the given objects is published in
	 * this application, false otherwise
	 */
	abstract function any_learning_object_is_published($object_ids);
	
	/**
	 * Determines where in this application the given learning object has been
	 * published.
	 * @param int $object_id The ID of the learning object.
	 * @return array An array of LearningObjectPublicationAttributes objects;
	 *               empty if the object has not been published anywhere.
	 */
	abstract function get_learning_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null);
	
	/**
	 * Determines where in this application the given learning object
	 * publication is published.
	 * @param int $publication_id The ID of the learning object publication.
	 * @return LearningObjectPublicationAttributes
	 */
	abstract function get_learning_object_publication_attribute($publication_id);
	
	/**
	 * Counts the number of publications
	 * @param string $type
	 * @param Condition $condition
	 * @return int
	 */
	abstract function count_publication_attributes($type = null, $condition = null);
	
	/**
	 * Deletes all publications of a given learning object
	 * @param int $object_id The id of the learning object
	 */
	abstract function delete_learning_object_publications($object_id);
	
	abstract function get_learning_object_publication_locations($learning_object);
	
	abstract function publish_learning_object($learning_object, $location);
	
	/**
	 *
	 */
	abstract function update_learning_object_publication_id($publication_attr);
	
	/**
	 * Gets the links to admin-components of this application
	 */
	abstract function get_application_platform_admin_links();
	
	/**
	 * Gets a platform setting
	 */
	abstract function get_platform_setting($variable);
	
	/**
	 * Loads the applications installed on the system. Applications are classes
	 * in the /application/lib subdirectory. Each application is a directory,
	 * which in its turn contains a class file named after the application. For
	 * instance, the weblcms application is the class Weblcms, defined in
	 * /application/lib/weblcms/weblcms.class.php. Applications must extend the
	 * Application class.
	 */
	public static function load_all_from_filesystem($include_application_classes = true)
	{
		$applications = array ();
		$path = dirname(__FILE__);
		$directories = Filesystem :: get_directory_content($path, Filesystem::LIST_DIRECTORIES,false);
		foreach($directories as $index => $directory)
		{
			$application_name = basename($directory);
			if(Application :: is_application_name($application_name))
			{
				if (!in_array($application_name, $applications))
				{
					if ($include_application_classes)
					{
						require_once($directory . '/' . $application_name . '_manager/' . $application_name . '_manager.class.php');
					}
					$applications[] = $application_name;
				}
			}
		}
		return $applications;
	}
	
	public static function load_all($include_application_classes = true)
	{
		$path = Path :: get_application_path() . 'lib';
		$adm = AdminDataManager :: get_instance();
		$condition = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_APPLICATION);
		
		$applications = $adm->retrieve_registrations($condition);
		$active_applications = array();
		
		while ($application = $applications->next_result())
		{
			if ($include_application_classes)
			{
				require_once $path . '/' . $application->get_name() . '/' . $application->get_name() . '_manager/'.$application->get_name().'_manager.class.php';
			}
			$active_applications[] = $application->get_name();
		}
		
		return $active_applications;
	}
	
	/**
	 * Determines if a given name is the name of an application
	 * @param string $name
	 * @return boolean
	 * @todo Better would be to check if the class for the application exists
	 */
	public static function is_application_name($name)
	{
		return (preg_match('/^[a-z][a-z_]+$/', $name) > 0);
	}
	
	/**
	 * Determines if a given application exists
	 * @param string $name
	 * @return boolean
	 */
	public static function is_application($name)
	{
		$path = dirname(__FILE__);
		$application_path = $path . '/' . $name;
		if (file_exists($application_path) && is_dir($application_path))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Converts an application name to the corresponding class name.
	 * @param string $application The application name.
	 * @return string The class name.
	 */
	public static function application_to_class($application)
	{
		return ucfirst(preg_replace('/_([a-z])/e', 'strtoupper(\1)', $application));
	}
	/**
	 * Creates a new instance of the given application
	 * @param string $application
	 * @return Application An instance of the application corresponding to the
	 * given $application
	 */
	public static function factory($application, $user = null)
	{
		$class = Application :: application_to_class($application) . 'Manager';
		return new $class($user);
	}
	
	public function is_active($application)
	{
		if (self :: is_application($application))
		{
			$adm = AdminDataManager :: get_instance();
			
			$conditions = array();
			$conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, 'application');
			$conditions[] = new EqualityCondition(Registration :: PROPERTY_NAME, $application);
			$condition = new AndCondition($conditions);
			
			$registrations = $adm->retrieve_registrations($condition);
			if ($registrations->size() > 0)
			{
				$registration = $registrations->next_result();
				if ($registration->is_active())
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
}
?>