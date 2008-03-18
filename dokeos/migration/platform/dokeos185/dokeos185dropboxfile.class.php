<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 dropbox_file
 *
 * @author Sven Vanpoucke
 */
class Dokeos185DropboxFile
{
	/**
	 * Dokeos185DropboxFile properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_UPLOADER_ID = 'uploader_id';
	const PROPERTY_FILENAME = 'filename';
	const PROPERTY_FILESIZE = 'filesize';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_AUTHOR = 'author';
	const PROPERTY_UPLOAD_DATE = 'upload_date';
	const PROPERTY_LAST_UPLOAD_DATE = 'last_upload_date';
	const PROPERTY_CAT_ID = 'cat_id';
	const PROPERTY_SESSION_ID = 'session_id';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185DropboxFile object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185DropboxFile($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Gets a default property by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	/**
	 * Gets the default properties
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_UPLOADER_ID, SELF :: PROPERTY_FILENAME, SELF :: PROPERTY_FILESIZE, SELF :: PROPERTY_TITLE, SELF :: PROPERTY_DESCRIPTION, SELF :: PROPERTY_AUTHOR, SELF :: PROPERTY_UPLOAD_DATE, SELF :: PROPERTY_LAST_UPLOAD_DATE, SELF :: PROPERTY_CAT_ID, SELF :: PROPERTY_SESSION_ID);
	}

	/**
	 * Sets a default property by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	/**
	 * Sets the default properties of this class
	 */
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Returns the id of this Dokeos185DropboxFile.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the uploader_id of this Dokeos185DropboxFile.
	 * @return the uploader_id.
	 */
	function get_uploader_id()
	{
		return $this->get_default_property(self :: PROPERTY_UPLOADER_ID);
	}

	/**
	 * Returns the filename of this Dokeos185DropboxFile.
	 * @return the filename.
	 */
	function get_filename()
	{
		return $this->get_default_property(self :: PROPERTY_FILENAME);
	}

	/**
	 * Returns the filesize of this Dokeos185DropboxFile.
	 * @return the filesize.
	 */
	function get_filesize()
	{
		return $this->get_default_property(self :: PROPERTY_FILESIZE);
	}

	/**
	 * Returns the title of this Dokeos185DropboxFile.
	 * @return the title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}

	/**
	 * Returns the description of this Dokeos185DropboxFile.
	 * @return the description.
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}

	/**
	 * Returns the author of this Dokeos185DropboxFile.
	 * @return the author.
	 */
	function get_author()
	{
		return $this->get_default_property(self :: PROPERTY_AUTHOR);
	}

	/**
	 * Returns the upload_date of this Dokeos185DropboxFile.
	 * @return the upload_date.
	 */
	function get_upload_date()
	{
		return $this->get_default_property(self :: PROPERTY_UPLOAD_DATE);
	}

	/**
	 * Returns the last_upload_date of this Dokeos185DropboxFile.
	 * @return the last_upload_date.
	 */
	function get_last_upload_date()
	{
		return $this->get_default_property(self :: PROPERTY_LAST_UPLOAD_DATE);
	}

	/**
	 * Returns the cat_id of this Dokeos185DropboxFile.
	 * @return the cat_id.
	 */
	function get_cat_id()
	{
		return $this->get_default_property(self :: PROPERTY_CAT_ID);
	}

	/**
	 * Returns the session_id of this Dokeos185DropboxFile.
	 * @return the session_id.
	 */
	function get_session_id()
	{
		return $this->get_default_property(self :: PROPERTY_SESSION_ID);
	}


}

?>