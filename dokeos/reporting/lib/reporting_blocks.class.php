<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/reporting_block.class.php';
require_once dirname(__FILE__) . '/reporting_block_layout.class.php';
class ReportingBlocks {

    /**
     * Creates a reporting block in the database
     * @param array $array
     * @return ReportingBlock
     */
	public static function create_reporting_block($array)
	{
		$reporting_block = new ReportingBlock();
        $reporting_block->set_default_properties($array);
		if(!$reporting_block->create())
		{
			return false;
		}
		return $reporting_block;
	}
}
?>