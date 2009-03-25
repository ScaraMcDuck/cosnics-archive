<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/reporting_block.class.php';
require_once dirname(__FILE__) . '/reporting_block_layout.class.php';
class ReportingBlocks {

    /**
     * Creates a reporting block in the database
     * @todo not all properties individually but as an array (parameter)
     * @param String $name
     * @param String $application
     * @param int $function
     * @param String $displaymode
     * @param int $width
     * @param int $height
     * @return ReportingBlock
     */
	public static function create_reporting_block($name,$application,$function,$displaymode,$width,$height)
	{
		$reporting_block = new ReportingBlock();
		$reporting_block->set_name($name);
		$reporting_block->set_application($application);
		$reporting_block->set_function($function);
		$reporting_block->set_displaymode($displaymode);
		$reporting_block->set_width($width);
		$reporting_block->set_height($height);
		if(!$reporting_block->create())
		{
			return false;
		}
		return $reporting_block;
	}
}
?>