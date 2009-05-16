<?php
ini_set('include_path',realpath(dirname(__FILE__).'/../../../plugin/pear'));
require_once dirname(__FILE__).'/../../global.inc.php';
include(dirname(__FILE__) . '/settings.inc.php');
include(dirname(__FILE__) . '/../my_template.php');
include(dirname(__FILE__) . '/data_class_generator/data_class_generator.class.php');
include(dirname(__FILE__) . '/form_generator/form_generator.class.php');
include(dirname(__FILE__) . '/data_manager_generator/data_manager_generator.class.php');
include(dirname(__FILE__) . '/rights_generator/rights_generator.class.php');
include(dirname(__FILE__) . '/install_generator/install_generator.class.php');

$location = $application['location'];
$name = $application['name'];
$author = $application['author'];

$data_class_generator = new DataClassGenerator();
$form_generator = new FormGenerator();

//Create Folders
create_folders($location, $name);

/**
 * Parse XML files
 * Generate DataClasses
 * Generate Forms
 */
$files = FileSystem :: get_directory_content($location, FileSystem :: LIST_FILES);
foreach($files as $file)
{
	if(substr($file, -4) != '.xml')
		continue;
		
	$new_path = move_file($location, $file); 
	
	$properties = retrieve_properties_from_xml_file($file);
	$classname = DokeosUtilities :: underscores_to_camelcase(str_replace('.xml', '', basename($file)));
	$description = 'This class describes a ' . $classname . ' data object';
	
	$data_class_generator->generate_data_class($location, $classname , $properties, $name, $description, $author);
	$form_generator->generate_form($location . 'forms/', $classname, $properties, $author);
	
	$classes[] = $classname;
}

//Generate the Data Managers
generate_data_managers($location, $name, $classes, $author);

//Generate Rights Files
generate_rights_files($location, $name);

//Generate Install Files
generate_install_files($location, $name, $author);

/**
 * Create folders for the application
 *
 * @param String $location - The location of the application
 * @param String $name - The name of the application
 */
function create_folders($location, $name)
{
	$folders = array('data_manager', 'forms', 'install', $name . '_manager', 'rights');
	foreach($folders as $folder)
	{
		FileSystem :: create_dir($location . $folder);
	}
}

/**
 * Move a file from the root to the install folder
 *
 * @param String $file - Path of the file
 * @return String $new_file - New path of the file
 */
function move_file($location, $file)
{
	$new_file = $location . 'install/' . basename($file);
	FileSystem :: copy_file($file, $new_file);
	return $new_file;
}

/**
 * Retrieves the properties from a data xml file
 *
 * @param String $file - The xml file
 * @return Array of String - The properties
 */
function retrieve_properties_from_xml_file($file)
{
	$properties = array();
	
	$options[] = array(XML_UNSERIALIZER_OPTION_FORCE_ENUM => array('property'));
	$array = DokeosUtilities :: extract_xml_file($file, $options);
	
	foreach($array['properties']['property'] as $property)
	{
		$properties[] = $property['name'];
	}
	
	return $properties;
}

function generate_data_managers($location, $name, $classes, $author)
{
	$data_manager_location = $location;
	$database_location = $location . 'data_manager/';
	$data_manager_generator = new DataManagerGenerator();
	$data_manager_generator->generate_data_managers($data_manager_location, $database_location, $name, $classes, $author);
}

/**
 * Generates rights files for an application
 *
 * @param String $location - The application location
 * @param String $name - The application name
 */
function generate_rights_files($location, $name)
{
	$rights_location = $location . 'rights/';
	$rights_generator = new RightsGenerator();
	$rights_generator->generate_right_files($rights_location, $name);
}

/**
 * Generates install files for an application
 *
 * @param String $location - The application location
 * @param String $name - The application name
 */
function generate_install_files($location, $name, $author)
{
	$install_location = $location . 'install/';
	$install_generator = new InstallGenerator();
	$install_generator->generate_install_files($install_location, $name, $author);
}
?>
