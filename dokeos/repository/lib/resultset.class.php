<?php
/**
 * @package repository
 */
/**
 * This class represents a result set. It allows you to create an abstract
 * representation of a remote set of data, e.g. a database record set.
 * Typically, a result set is accessed the following way:
 * 
 *     // Create an instance of a ResultSet implementation
 *     $set = new MyResultSet();
 *     // Iterate over the set
 *     while ($item = $set->next_result())
 *     {
 *         // Do something with the item
 *         echo $item->get_something(), "\n";
 *     }
 * 
 * @author Tim De Pauw
 */
abstract class ResultSet
{
	/**
	 * Retrieves next item from this result set
	 * @return mixed The item, or null if none.
	 */
	abstract function next_result();
	/**
	 * Retrieves the number of items in this result set.
	 * @return int The number of items.
	 */
	abstract function size();
	/**
	 * Skips a number of items. The default implementation of this method
	 * merely discards the output of the next_result() function $count times.
	 * @param int $count The number of items to skip.
	 */
	function skip($count)
	{
		for ($i = 0; $i < $count; $i++)
		{
			$this->next_result();
		}
	}
	/**
	 * Returns an array representation of this result set.
	 * @return array An array containing all the items in the set.
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