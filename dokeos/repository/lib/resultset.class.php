<?php
/**
 * This can be used to hold a resultset and retrieve items from the set.
 * @package repository
 */
abstract class ResultSet
{
	/**
	 * Retrieve next item from this resultset
	 * @return mixed The next item (null if no item available)
	 */
	abstract function next_result();
	/**
	 * Retrieve the number of items in this resultset.
	 * @return int The number of items
	 */
	abstract function size();
	/**
	 * Skip a number of items.
	 * This is the same as calling $count times next_result.
	 * @param int $count The number of items to skip in the resultset.
	 */
	function skip($count)
	{
		for ($i = 0; $i < $count; $i++)
		{
			$this->next_result();
		}
	}
	/**
	 * Return this resultset as an array
	 * @return array An array containing all items from this resultset.
	 */
	function as_array()
	{
		$array = array();
		while ($result = $this->next_result())
		{
			$array[] = $result;
		}
		return $array;
	}
}
?>