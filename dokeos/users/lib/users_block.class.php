<?php
/**
 * @package application.lib.weblcms
 */

/**
==============================================================================
 *	This class represents a general Weblcms Block.
 *
 *	@author Hans De bisschop
==============================================================================
 */

class UsersBlock
{
	const PARAM_ACTION = 'block_action';
		
	private $parent;
	private $type;
	private $block_info;
	private $configuration;
	
	/**
	 * Constructor.
	 * @param array $types The learning object types that may be published.
	 * @param  boolean $email_option If true the publisher has the option to
	 * send the published learning object by email to the selecter target users.
	 */
	function UsersBlock($parent, $block_info)
	{
		$this->parent = $parent;
		$this->block_info = $block_info;
		$this->configuration = $block_info->get_configuration();
	}
	
	/**
	 * Create a new weblcms component
	 * @param string $type The type of the component to create.
	 * @param Weblcms $weblcms The weblcms in
	 * which the created component will be used
	 */
	static function factory($weblcms, $block)
	{
		$type = $block->get_component();
		$filename = dirname(__FILE__).'/../block/users_'.$type.'.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" block');
		}
		$class = 'Users'.DokeosUtilities :: underscores_to_camelcase($type);
		require_once $filename;
		return new $class($weblcms, $block);
	}

	/**
	 * Returns the tool which created this publisher.
	 * @return RepositoryTool The tool.
	 */
	function get_parent()
	{
		return $this->parent;
	}
	
	function get_configuration()
	{
		return $this->configuration;
	}

	/**
	 * @see RepositoryTool::get_user_id()
	 */
	function get_user_id()
	{
		return $this->get_parent()->get_user_id();
	}
	
	function get_user()
	{
		return $this->get_parent()->get_user();
	}

	/**
	 * Returns the types of learning object that this object may publish.
	 * @return array The types.
	 */
	function get_type()
	{
		return $this->type;
	}
	
	function get_block_info()
	{
		return $this->block_info;
	}
}
?>