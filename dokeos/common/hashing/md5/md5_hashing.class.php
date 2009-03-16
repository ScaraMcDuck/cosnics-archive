<?php

/**
 * Class that defines md5 hashing
 * @author vanpouckesven
 *
 */
class Md5Hashing extends Hashing
{
	function create_hash($value)
	{
		return md5($value);
	}
	
	function create_file_hash($file)
	{
		return md5_file($file);
	}
	
}
?>