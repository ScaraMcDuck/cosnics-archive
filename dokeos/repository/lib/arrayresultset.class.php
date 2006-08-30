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
	private $data;

	private $pointer;

	function ArrayResultSet(& $array)
	{
		$this->data = & $array;
		$this->pointer = 0;
	}

	function next_result()
	{
		if ($this->pointer < count($this->data))
		{
			return $this->data[$this->pointer++];
		}
		return null;
	}

	function as_array()
	{
		return $this->data;
	}

	function size()
	{
		return count($this->data);
	}

	function skip ($count)
	{
		$this->pointer += $count;
	}
}
?>