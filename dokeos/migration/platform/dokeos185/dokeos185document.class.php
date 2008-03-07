<?php

/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importdocument.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/learning_object/document/document.class.php';

/**
 * This class represents an old Dokeos 1.8.5 document
 *
 * @author David Van Wayenbergh
 */
 
class Dokeos185Document extends Import
{
	/**
	 * document properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_PATH = 'path';
	const PROPERTY_TITLE = 'title';
 	const PROPERTY_SIZE = 'size';
 	const PROPERTY_COMMENT = 'comment';
 	const PROPERTY_FILETYPE = 'filetype';
 	
 	/**
	 * Alfanumeric identifier of the document object.
	 */
	private $code;
	
	/**
	 * Default properties of the document object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new document object.
	 * @param array $defaultProperties The default properties of the document
	 *                                 object. Associative array.
	 */
	function Dokeos185Document($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this document object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this document.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all documents.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID,self :: PROPERTY_PATH,self :: PROPERTY_TITLE,
			self :: PROPERTY_SIZE,self :: PROPERTY_COMMENT, self :: PROPERTY_FILETYPE);
	}
	
	/**
	 * Sets a default property of this document by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default document
	 * property.
	 * @param string $name The identifier.
	 * @return boolean True if the identifier is a property name, false
	 *                 otherwise.
	 */
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}
	
	/**
	 * Returns the id of this document.
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	
	/**
	 * Returns the path of this document.
	 * @return String The path.
	 */
	function get_path()
	{
		return $this->get_default_property(self :: PROPERTY_PATH);
	}
	
	/**
	 * Returns the title of this document.
	 * @return String The title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}
	
	/**
	 * Returns the size of this document.
	 * @return int The size.
	 */
	function get_size()
	{
		return $this->get_default_property(self :: PROPERTY_SIZE);
	}
	
	/**
	 * Returns the filetype of this document.
	 * @return String The filetype.
	 */
	function get_filetype()
	{
		return $this->get_default_property(self :: PROPERTY_FILETYPE);
	}
	
	/**
	 * Sets the id of this document.
	 * @param int $id The id.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	
	/**
	 * Sets the path of this document.
	 * @param String $path The $path.
	 */
	function set_path($path)
	{
		$this->set_default_property(self :: PROPERTY_PATH, $path);
	}
	
	/**
	 * Sets the title of this document.
	 * @param String $title The title.
	 */
	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
	}
	
	/**
	 * Sets the size of this document.
	 * @param String $size The size.
	 */
	function set_size($size)
	{
		$this->set_default_property(self :: PROPERTY_SIZE, $size);
	}
	
	/**
	 * Sets the comment of this document.
	 * @param String $comment The comment.
	 */
	function set_comment($comment)
	{
		$this->set_default_property(self :: PROPERTY_COMMENT, $comment);
	}
	
	/**
	 * Sets the filetype of this document.
	 * @param String $filetype The filetype.
	 */
	function set_filetype($filetype)
	{
		$this->set_default_property(self :: PROPERTY_FILETYPE, $filetype);
	}
}
?>
