<?php

/**
 * Class that defines Haval256 hashing with 5 passes
 * @author Samumon
 *
 */
class Haval256Hashing extends Hashing
{
	function create_hash($value)
	{
		return hash('haval256,5', $value);
	}

	function create_file_hash($file)
	{
		return hash_file('haval256,5', $file);
	}

}
?>