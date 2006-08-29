<?php
/**
 * $Id$
 * @package repository.metadata
 * @subpackage ieee_lom
 */
/**
 * A Vocabulary field in IEEE LOM
 */
class Vocabulary {
	/**
	 * The source
	 */
	private $source;
	/**
	 * The value
	 */
	private $value;
	/**
	 * Constructor
	 * @param string|null $source
	 * @param string|null $value
	 */
	public function Vocabulary($source=null,$value=null)
	{
		$this->source = $source;
		$this->value = $value;
	}
	/**
	 * Gets the source
	 * @return string|null
	 */
	public function get_source()
	{
		return $this->source;
	}
	/**
	 * Gets the value
	 * @return string|null
	 */
	public function get_value()
	{
		return $this->value;
	}
}
?>
