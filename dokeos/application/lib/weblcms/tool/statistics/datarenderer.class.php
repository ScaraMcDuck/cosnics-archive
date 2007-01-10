<?php
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