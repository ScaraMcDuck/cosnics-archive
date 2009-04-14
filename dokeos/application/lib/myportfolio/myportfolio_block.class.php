<?php
/**
 * @package application.lib.search_portal
 */
 
require_once Path :: get_library_path() . 'block.class.php';

/**
==============================================================================
==============================================================================
 */

class MyPortfolioBlock extends Block
{
	/**
	 * Constructor.
	 * @param array $types The learning object types that may be published.
	 * @param  boolean $email_option If true the publisher has the option to
	 * send the published learning object by email to the selecter target users.
	 */
	function MyPortfolioBlock($parent, $block_info)
	{
		parent :: __construct($parent, $block_info);
	}
	
	/**
	 * Create a new profiler component
	 * @param string $type The type of the component to create.
	 * @param Weblcms $weblcms The weblcms in
	 * which the created component will be used
	 */
	static function factory($myportfolio, $block)
	{
		$type = $block->get_component();
		$filename = dirname(__FILE__).'/block/myportfolio_'.$type.'.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" block');
		}
		$class = 'MyPortfolio'.DokeosUtilities :: underscores_to_camelcase($type);
		require_once $filename;
		return new $class($myportfolio, $block);
	}
}
?>
