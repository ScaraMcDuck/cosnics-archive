<?php
/**
 * $Id$
 * @package application
 */
require_once(dirname(__FILE__).'/../../common/filesystem/filesystem.class.php');
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

	abstract function get_learning_object_publication_attribute($publication_id);

	abstract function count_publication_attributes($type = null, $condition = null);

	abstract function delete_learning_object_publications($object_id);

	abstract function update_learning_object_publication_id($publication_attr);

	abstract function get_application_platform_admin_links();
	/**
	 * Loads the applications installed on the system. Applications are classes
	 * in the /application/lib subdirectory. Each application is a directory,
	 * which in its turn contains a class file named after the application. For
	 * instance, the weblcms application is the class Weblcms, defined in
	 * /application/lib/weblcms/weblcms.class.php. Applications must extend the
	 * Application class.
	 */
	public static function load_all()
	{
		$applications = array ();
		$path = dirname(__FILE__);
		$directories = Filesystem::get_directory_content($path,Filesystem::LIST_DIRECTORIES,false);
		foreach($directories as $index => $directory)
		{
			$application_name = basename($directory);
			if(Application::is_application_name($application_name))
			{
				require_once($directory.'/'.$application_name.'_manager/'.$application_name.'.class.php');
				if (!in_array($application_name, applications))
					{
						$applications[] = $application_name;
					}
			}
		}
		return $applications;
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
	public static function factory($application,$user = null)
	{
		$class = Application::application_to_class($application);
		return new $class($user);
	}
}
?>