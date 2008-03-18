<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 lp
 *
 * @author Sven Vanpoucke
 */
class Dokeos185Lp
{
	/**
	 * Dokeos185Lp properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_LP_TYPE = 'lp_type';
	const PROPERTY_NAME = 'name';
	const PROPERTY_REF = 'ref';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_PATH = 'path';
	const PROPERTY_FORCE_COMMIT = 'force_commit';
	const PROPERTY_DEFAULT_VIEW_MOD = 'default_view_mod';
	const PROPERTY_DEFAULT_ENCODING = 'default_encoding';
	const PROPERTY_DISPLAY_ORDER = 'display_order';
	const PROPERTY_CONTENT_MAKER = 'content_maker';
	const PROPERTY_CONTENT_LOCAL = 'content_local';
	const PROPERTY_CONTENT_LICENSE = 'content_license';
	const PROPERTY_PREVENT_REINIT = 'prevent_reinit';
	const PROPERTY_JS_LIB = 'js_lib';
	const PROPERTY_DEBUG = 'debug';
	const PROPERTY_THEME = 'theme';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185Lp object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185Lp($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_LP_TYPE, SELF :: PROPERTY_NAME, SELF :: PROPERTY_REF, SELF :: PROPERTY_DESCRIPTION, SELF :: PROPERTY_PATH, SELF :: PROPERTY_FORCE_COMMIT, SELF :: PROPERTY_DEFAULT_VIEW_MOD, SELF :: PROPERTY_DEFAULT_ENCODING, SELF :: PROPERTY_DISPLAY_ORDER, SELF :: PROPERTY_CONTENT_MAKER, SELF :: PROPERTY_CONTENT_LOCAL, SELF :: PROPERTY_CONTENT_LICENSE, SELF :: PROPERTY_PREVENT_REINIT, SELF :: PROPERTY_JS_LIB, SELF :: PROPERTY_DEBUG, SELF :: PROPERTY_THEME);
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
	 * Returns the id of this Dokeos185Lp.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the lp_type of this Dokeos185Lp.
	 * @return the lp_type.
	 */
	function get_lp_type()
	{
		return $this->get_default_property(self :: PROPERTY_LP_TYPE);
	}

	/**
	 * Returns the name of this Dokeos185Lp.
	 * @return the name.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}

	/**
	 * Returns the ref of this Dokeos185Lp.
	 * @return the ref.
	 */
	function get_ref()
	{
		return $this->get_default_property(self :: PROPERTY_REF);
	}

	/**
	 * Returns the description of this Dokeos185Lp.
	 * @return the description.
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}

	/**
	 * Returns the path of this Dokeos185Lp.
	 * @return the path.
	 */
	function get_path()
	{
		return $this->get_default_property(self :: PROPERTY_PATH);
	}

	/**
	 * Returns the force_commit of this Dokeos185Lp.
	 * @return the force_commit.
	 */
	function get_force_commit()
	{
		return $this->get_default_property(self :: PROPERTY_FORCE_COMMIT);
	}

	/**
	 * Returns the default_view_mod of this Dokeos185Lp.
	 * @return the default_view_mod.
	 */
	function get_default_view_mod()
	{
		return $this->get_default_property(self :: PROPERTY_DEFAULT_VIEW_MOD);
	}

	/**
	 * Returns the default_encoding of this Dokeos185Lp.
	 * @return the default_encoding.
	 */
	function get_default_encoding()
	{
		return $this->get_default_property(self :: PROPERTY_DEFAULT_ENCODING);
	}

	/**
	 * Returns the display_order of this Dokeos185Lp.
	 * @return the display_order.
	 */
	function get_display_order()
	{
		return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
	}

	/**
	 * Returns the content_maker of this Dokeos185Lp.
	 * @return the content_maker.
	 */
	function get_content_maker()
	{
		return $this->get_default_property(self :: PROPERTY_CONTENT_MAKER);
	}

	/**
	 * Returns the content_local of this Dokeos185Lp.
	 * @return the content_local.
	 */
	function get_content_local()
	{
		return $this->get_default_property(self :: PROPERTY_CONTENT_LOCAL);
	}

	/**
	 * Returns the content_license of this Dokeos185Lp.
	 * @return the content_license.
	 */
	function get_content_license()
	{
		return $this->get_default_property(self :: PROPERTY_CONTENT_LICENSE);
	}

	/**
	 * Returns the prevent_reinit of this Dokeos185Lp.
	 * @return the prevent_reinit.
	 */
	function get_prevent_reinit()
	{
		return $this->get_default_property(self :: PROPERTY_PREVENT_REINIT);
	}

	/**
	 * Returns the js_lib of this Dokeos185Lp.
	 * @return the js_lib.
	 */
	function get_js_lib()
	{
		return $this->get_default_property(self :: PROPERTY_JS_LIB);
	}

	/**
	 * Returns the debug of this Dokeos185Lp.
	 * @return the debug.
	 */
	function get_debug()
	{
		return $this->get_default_property(self :: PROPERTY_DEBUG);
	}

	/**
	 * Returns the theme of this Dokeos185Lp.
	 * @return the theme.
	 */
	function get_theme()
	{
		return $this->get_default_property(self :: PROPERTY_THEME);
	}


}

?>