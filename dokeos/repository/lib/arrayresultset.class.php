<?php
/**
 * $Id$
 * @package repository
 */
require_once dirname(__FILE__).'/resultset.class.php';

/**
 * This class allows you to wrap an array in a result set. It does not offer
 * any performance increase, but in select cases, you will need it.
 * @author Tim De Pauw
 */
class ArrayResultSet extends ResultSet
{
	/**
	 * The data in this set
	 */
	private $data;
	/**
	 * A pointer to the current element in the set
	 */
	private $pointer;
	/**
	 * Constructor
	 * @param array $array
	 */
	function ArrayResultSet(& $array)
	{
		$this->data = & $array;
		$this->pointer = 0;
	}
	// Inherited
	function next_result()
	{
		if ($this->pointer < count($this->data))
		{
			return $this->data[$this->pointer++];
		}
		return null;
	}
	// Inherited
	function as_array()
	{
		return $this->data;
	}
	// Inherited
	function size()
	{
		return count($this->data);
	}
	// Inherited
	function skip ($count)
	{
		$this->pointer += $count;
	}
}
?>