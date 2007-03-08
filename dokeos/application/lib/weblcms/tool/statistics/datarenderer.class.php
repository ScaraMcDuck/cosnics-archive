<?php
/**
 * $Id: statisticstool.class.php 10644 2007-01-10 13:13:43Z bmol $
 * Statistics tool: Data renderer
 * @package application.weblcms.tool
 * @subpackage statistics
 */
abstract class DataRenderer
{
	protected $data;
	protected $parent;
	public function DataRenderer($parent,$data)
	{
		$this->parent = $parent;
		$this->data = $data;
	}
    abstract function display();
}
?>