<?php
/**
 * @package application.alexia
 * 
 * @author Hans De bisschop
 * 
 * This class represents a general Alexia Block.
 * 
 */ 
require_once Path :: get_library_path() . 'block.class.php';

class AlexiaBlock extends Block
{
	/**
	 * Constructor.
	 */
	function AlexiaBlock($parent, $block_info)
	{
		parent :: __construct($parent, $block_info);
	}
	
	static function factory($alexia, $block)
	{
		$type = $block->get_component();
		$filename = dirname(__FILE__) . '/block/alexia_' . $type . '.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "' . $type . '" block');
		}
		$class = 'Alexia' . DokeosUtilities :: underscores_to_camelcase($type);
		require_once $filename;
		return new $class($alexia, $block);
	}
}
?>