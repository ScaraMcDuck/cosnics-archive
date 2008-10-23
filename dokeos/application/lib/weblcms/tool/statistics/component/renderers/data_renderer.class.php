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
    
    public static function factory($renderer_type, $parent, $data)
    {
    	$filename = dirname(__FILE__).'/'. DokeosUtilities :: camelcase_to_underscores($renderer_type) . '_data_renderer.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$renderer_type.'" component');
		}
		$class = $renderer_type.'DataRenderer';
		require_once $filename;
		return new $class($parent, $data);
    }
}
?>