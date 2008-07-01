<?php
/**
 * @package application.lib.calendar
 */
 
require_once Path :: get_library_path() . 'block.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/abstract_learning_object.class.php';

/**
==============================================================================
 *	This class provides the means to publish a learning object.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class PersonalMessengerBlock extends Block
{
	/**
	 * Constructor.
	 * @param array $types The learning object types that may be published.
	 * @param  boolean $email_option If true the publisher has the option to
	 * send the published learning object by email to the selecter target users.
	 */
	function PersonalMessengerBlock($parent, $block_info)
	{
		parent :: __construct($parent, $block_info);
	}
	
	/**
	 * Create a new weblcms component
	 * @param string $type The type of the component to create.
	 * @param Weblcms $weblcms The weblcms in
	 * which the created component will be used
	 */
	static function factory($personal_messenger, $block)
	{
		$type = $block->get_component();
		$filename = dirname(__FILE__).'/block/personal_messenger_'.$type.'.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" block');
		}
		$class = 'PersonalMessenger'.DokeosUtilities :: underscores_to_camelcase($type);
		require_once $filename;
		return new $class($personal_messenger, $block);
	}
}
?>