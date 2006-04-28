<?php
abstract class ResultSet
{
	abstract function next_result();
	
	abstract function size();
	
	function skip($count)
	{
		for ($i = 0; $i < $count; $i++)
		{
			$this->next_result();
		}
	}
	
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