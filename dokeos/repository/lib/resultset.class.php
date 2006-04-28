<?php
abstract class ResultSet
{
	abstract function next_result();
	
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